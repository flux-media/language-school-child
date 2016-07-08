<?php

defined('ABSPATH') or die('No script kiddies please!');

class ASOrderHelper
{
    protected $GABIA_SMS_API;

    protected $MESSAGE_FORBIDDEN = '흐즈므르...';
    protected $MESSAGE_NOT_AUTHENTICATED = '권한이 없습니다.';
    protected $MESSAGE_WRONG_ORDER_ID = '잘못된 Order ID입니다.';
    protected $MESSAGE_UNKNOWN = '죄송합니다... 무엇이 문제일까요?';
    protected $MESSAGE_SUCCESS = '환불 요청이 성공적으로 완료되었습니다.';

    public function __construct()
    {
        // Initialize SMS module.
        require_once(dirname(__FILE__) . '/api.class.php');
        $this->GABIA_SMS_API = new gabiaSmsApi();

        // Auto-refund via customer's AJAX call.
        add_action('wp_ajax_refund_order', array($this, 'refund'));

        // Send notifications (SMS, E-mail) when order status is changed.

        // When completed by e-mail.
        add_action('wp_ajax_send_registration_feedback', array($this, 'send_registration_feedback'));
        add_action('wp_ajax_nopriv_send_registration_feedback', array($this, 'send_registration_feedback'));
        // When completed by plugin.
        add_action('woocommerce_order_status_completed', array($this, 'on_order_complete'));
        // When refunded.
        add_action('woocommerce_order_refunded', array($this, 'on_order_refund'), 10, 2);

        // Automatically empty the cart.
        add_action('woocommerce_add_to_cart', array($this, 'woocommerce_empty_cart_before_add'), 0);
    }

    public function refund()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(array('error' => $this->MESSAGE_NOT_AUTHENTICATED));
            return;
        }

        $current_user = wp_get_current_user();
        $order = new WC_Order($_POST['order_id']);
        // TODO: Only 'completed' orders matter?
        if (empty($order) || $order->status != 'completed') {
            wp_send_json_error(array('error' => $this->MESSAGE_WRONG_ORDER_ID));
            return;
        }
        $order_user = $order->get_user();
        if ($order_user == false || $current_user->id != $order_user->id) {
            wp_send_json_error(array('error' => $this->MESSAGE_NOT_AUTHENTICATED));
            return;
        }
        $order_items = $order->get_items();
        $refund_amount = 0;
        $refund_reason = '고객 직접 환불';
        $order_id = $order->id;
        $line_item_qtys = array();
        $line_item_totals = array();
        $line_item_tax_totals = array();
        $refund = false;

        foreach ($order_items as $order_item) {
            $product = $order->get_product_from_item($order_item);
            if (empty($product)) {
                wp_send_json_error(array('error' => $this->MESSAGE_WRONG_ORDER_ID));
                return;
            }

            $line_item_qtys[$product->get_id()] = $order_item['qty'];
            $line_item_totals[$product->get_id()] = $order->get_item_subtotal($order_item, false);
            $line_item_tax_totals[$product->get_id()] = $order->get_item_tax($order_item);

            $as_product = new ASProduct($product);
            $refund_amount = $as_product->get_refund_rate() * $order->get_line_subtotal($order_item, true);
            break;
        }

        if ($refund_amount > 0) {
            // From woocommerce/includes/class-wc-ajax.php#refund_line_items
            try {
                $line_items = array();
                $item_ids = array_unique(array_merge(array_keys($line_item_qtys, $line_item_totals)));

                foreach ($item_ids as $item_id) {
                    $line_items[$item_id] = array('qty' => 0, 'refund_total' => 0, 'refund_tax' => array());
                }
                foreach ($line_item_qtys as $item_id => $qty) {
                    $line_items[$item_id]['qty'] = max($qty, 0);
                }
                foreach ($line_item_totals as $item_id => $total) {
                    $line_items[$item_id]['refund_total'] = wc_format_decimal($total);
                }
                foreach ($line_item_tax_totals as $item_id => $tax_totals) {
                    $line_items[$item_id]['refund_tax'] = array_map('wc_format_decimal', $tax_totals);
                }

                $refund = wc_create_refund(array(
                    'amount' => $refund_amount,
                    'reason' => $refund_reason,
                    'order_id' => $order->id,
                    'line_items' => $line_items,
                ));

                if (is_wp_error($refund)) {
                    throw new Exception($refund->get_error_message());
                }

                // Refund via API
                if (WC()->payment_gateways()) {
                    $payment_gateways = WC()->payment_gateways->payment_gateways();
                }

                if (isset($payment_gateways[$order->payment_method]) && $payment_gateways[$order->payment_method]->supports('refunds')) {
                    $result = $payment_gateways[$order->payment_method]->process_refund($order_id, $refund_amount, $refund_reason);

                    do_action('woocommerce_refund_processed', $refund, $result);

                    if (is_wp_error($result)) {
                        throw new Exception($result->get_error_message());
                    } elseif (!$result) {
                        throw new Exception(__('Refund failed', 'woocommerce'));
                    }
                } else {
                    wp_send_json_error(array('error' => $this->MESSAGE_UNKNOWN));
                    return;
                }

                // Restock items
                foreach ($order_items as $order_item) {
                    $_product = $order->get_product_from_item($order_item);

                    if ($_product && $_product->exists() && $_product->managing_stock()) {
                        $old_stock = wc_stock_amount($_product->stock);
                        $new_quantity = $_product->increase_stock($order_item['qty']);

                        $order->add_order_note(sprintf(__('Item #%s stock increased from %s to %s.', 'woocommerce'), $order_item['product_id'], $old_stock, $new_quantity));

                        do_action('woocommerce_restock_refunded_item', $_product->id, $old_stock, $new_quantity, $order);
                    }
                }

                // Trigger notifications and status changes
                if ($as_product->get_refund_rate() < 1) {
                    /**
                     * woocommerce_order_partially_refunded.
                     *
                     * @since 2.4.0
                     * Note: 3rd arg was added in err. Kept for bw compat. 2.4.3.
                     */
                    do_action('woocommerce_order_partially_refunded', $order_id, $refund->id, $refund->id);
                } else {
                    do_action('woocommerce_order_fully_refunded', $order_id, $refund->id);
                    $order->update_status(apply_filters('woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund->id));
                    $response_data['status'] = 'fully_refunded';
                }

                do_action('woocommerce_order_refunded', $order_id, $refund->id);

                // Clear transients
                wc_delete_shop_order_transients($order_id);
                wp_send_json_success();
            } catch (Exception $e) {
                if ($refund && is_a($refund, 'WC_Order_Refund')) {
                    wp_delete_post($refund->id, true);
                }
                wp_send_json_error(array('error' => $e->getMessage()));
            }
        } else {
            wp_send_json_error(array('error' => $this->MESSAGE_FORBIDDEN));
        }
    }

    public function on_order_refund($order_id, $refund_id)
    {
        $refund = new WC_Order_Refund($refund_id);

        // Send SMS
        $tel = get_post_meta($order_id, '_billing_phone', true);
        $message = $refund->get_refund_amount() . '원이 환불되었습니다. 감사합니다 - 어벤져스쿨';
        $this->sendSms($tel, $message, '고객환불');
    }

    public function on_order_complete($order_id)
    {
        $order = new WC_Order($order_id);
        $tel = get_post_meta($order_id, '_billing_phone', true);
        $course_titles = '';
        foreach ($order->get_items() as $item) {
            $course_titles .= '<' . $item['name'] . '> ';
        }
        $message = $course_titles . '강연 입금 및 등록이 완료되었습니다. 감사합니다. - 어벤져스쿨';
        $this->sendSms($tel, $message, '아임포트');
    }

    // TODO: Deprecate when contact form is no longer used as payment means.
    public function send_registration_feedback()
    {
        // Get parameters.
        $admin_account = $this->GABIA_SMS_API->getAdminAccount();

        // From contact form.
        $email = $_POST['email'];
        $tel = $_POST['tel'];
        $course_title = $_POST['course_title'];
        $name = $_POST['name'];
        $amount = $_POST['amount'];

        wp_mail($email, '[어벤져스쿨] 성공적으로 강연 등록이 완료되었습니다.',
            '강의 제목: ' . $course_title . "\n" .
            '수강자 이름: ' . $name . "\n" .
            '결제 금액: ' . $amount . "\n" .
            $admin_account . "\n" .
            '입급 전 정원 초과시 자동 취소되오니 빠른 결제 부탁드립니다.' . "\n" .
            '- 어벤져스쿨.');
        $message = '<' . $course_title . '> 강연 등록이 완료되었습니다. 입급 전 정원 초과시 자동 취소되오니 빠른 결제 부탁드립니다. - 어벤져스쿨';
        $this->sendSms($tel, $message, '무통장입금');
    }

    /**
     * Hook: Empty cart before adding a new product to cart WITHOUT throwing woocommerce_cart_is_empty
     * https://wordpress.org/support/topic/how-to-empty-cart-before-adding-the-new-product-to-the-cart-in-woocommerce-1
     */
    public function woocommerce_empty_cart_before_add()
    {
        global $woocommerce;

        // Get 'product_id' and 'quantity' for the current woocommerce_add_to_cart operation
        if (isset($_GET["add-to-cart"])) {
            $prodId = (int)$_GET["add-to-cart"];
        } else if (isset($_POST["add-to-cart"])) {
            $prodId = (int)$_POST["add-to-cart"];
        } else {
            $prodId = null;
        }
        if (isset($_GET["quantity"])) {
            $prodQty = (int)$_GET["quantity"];
        } else if (isset($_POST["quantity"])) {
            $prodQty = (int)$_POST["quantity"];
        } else {
            $prodQty = 1;
        }

        // If cart is empty
        if ($woocommerce->cart->get_cart_contents_count() == 0) {

            // Simply add the product (nothing to do here)

            // If cart is NOT empty
        } else {

            $cartQty = $woocommerce->cart->get_cart_item_quantities();
            $cartItems = $woocommerce->cart->cart_contents;

            // Check if desired product is in cart already
            if (array_key_exists($prodId, $cartQty)) {

                // Then first adjust its quantity
                foreach ($cartItems as $k => $v) {
                    if ($cartItems[$k]['product_id'] == $prodId) {
                        $woocommerce->cart->set_quantity($k, $prodQty);
                    }
                }

                // And only after that, set other products to zero quantity
                foreach ($cartItems as $k => $v) {
                    if ($cartItems[$k]['product_id'] != $prodId) {
                        $woocommerce->cart->set_quantity($k, '0');
                    }
                }
            }
        }
    }

    private function sendSms($tel, $message, $from)
    {
        // Get parameters.
        $admin_email = get_option('admin_email');
        $admin_tel = $this->GABIA_SMS_API->getAdminTel();
        $result = false;

        if (mb_strlen($message, 'UTF-8') > 45) {
            $message_result = $this->GABIA_SMS_API->lms_send($tel, $admin_tel, $message);
        } else {
            $message_result = $this->GABIA_SMS_API->sms_send($tel, $admin_tel, $message);
        }

        if ($message_result == gabiaSmsApi::$RESULT_OK) {
            $result = true;
        } else {
            wp_mail($admin_email, '[어벤져스쿨] 문자 전송 실패. (' . $from . ')',
                $this->GABIA_SMS_API->getResultCode() . " : " . $this->GABIA_SMS_API->getResultMessage());
        }

        return $result;
    }
}

$order_helper = new ASOrderHelper();
