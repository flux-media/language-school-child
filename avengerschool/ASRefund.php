<?php
class ASRefund {
	protected $MESSAGE_NOT_AUTHENTICATED = '권한이 없습니다.';
	protected $MESSAGE_WRONG_ORDER_ID = '잘못된 Order ID입니다.';
	protected $MESSAGE_UNKNOWN = '죄송합니다... 무엇이 문제일까요?';
	protected $MEESAGE_SUCCESS = '환불 요청이 성공적으로 완료되었습니다.';

	public function __construct() {
		add_action( 'wp_ajax_refund_order', array( $this, 'refund' ) );
	}

	public function refund() {
		if (!is_user_logged_in()) {
			wp_send_json_error(array('error' => $this->MESSAGE_NOT_AUTHENTICATED));
			return;
		}

		$current_user = wp_get_current_user();
		$order = new WC_Order( $_POST['order_id'] );
		// TODO: Only 'completed' orders matter?
		if (empty($order) || $order->status != 'completed') {
			wp_send_json_error( array( 'error' => $this->MESSAGE_WRONG_ORDER_ID ) );
			return;
		}
		$order_user = $order->get_user();
		if ($order_user == false || $current_user->id != $order_user->id) {
			wp_send_json_error( array( 'error' => $this->MESSAGE_NOT_AUTHENTICATED ) );
			return;
		}
		$order_items 			= $order->get_items();
		$refund_amount 			= 0;
		$refund_reason 			= '고객 직접 환불';
		$order_id               = $order->id;
		$line_item_qtys         = array();
		$line_item_totals       = array();
		$line_item_tax_totals   = array();
		$refund 				= false;

		foreach ($order_items as $order_item) {
			$product = $order->get_product_from_item ( $order_item );
			if (empty($product)) {
				wp_send_json_error( array( 'error' => $this->MESSAGE_WRONG_ORDER_ID ) );
				return;
			}

			$line_item_qtys[$product->get_id()] = $order_item['qty'];
			$line_item_totals[$product->get_id()] = $order->get_item_subtotal($order_item, false);
			$line_item_tax_totals[$product->get_id()] = $order->get_item_tax($order_item);

			$as_product = new ASProduct( $product );
			$refund_amount = $as_product->get_refund_rate() * $order->get_line_subtotal( $order_item, true );
			break;
		}

		// From woocommerce/includes/class-wc-ajax.php#refund_line_items
		try {
			$line_items = array();
			$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys, $line_item_totals ) ) );

			foreach ( $item_ids as $item_id ) {
				$line_items[ $item_id ] = array( 'qty' => 0, 'refund_total' => 0, 'refund_tax' => array() );
			}
			foreach ( $line_item_qtys as $item_id => $qty ) {
				$line_items[ $item_id ]['qty'] = max( $qty, 0 );
			}
			foreach ( $line_item_totals as $item_id => $total ) {
				$line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
			}
			foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
				$line_items[ $item_id ]['refund_tax'] = array_map( 'wc_format_decimal', $tax_totals );
			}

			$refund = wc_create_refund( array(
				'amount'     => $refund_amount,
				'reason'     => $refund_reason,
				'order_id'   => $order->id,
				'line_items' => $line_items,
			) );

			if ( is_wp_error( $refund ) ) {
				throw new Exception( $refund->get_error_message() );
			}

			// Refund via API
			if ( WC()->payment_gateways() ) {
				$payment_gateways = WC()->payment_gateways->payment_gateways();
			}

			if ( isset( $payment_gateways[ $order->payment_method ] ) && $payment_gateways[ $order->payment_method ]->supports( 'refunds' ) ) {
				$result = $payment_gateways[ $order->payment_method ]->process_refund( $order_id, $refund_amount, $refund_reason );

				do_action( 'woocommerce_refund_processed', $refund, $result );

				if ( is_wp_error( $result ) ) {
					throw new Exception( $result->get_error_message() );
				} elseif ( ! $result ) {
					throw new Exception( __( 'Refund failed', 'woocommerce' ) );
				}
			} else {
				wp_send_json_error(array('error' => $this->MESSAGE_UNKNOWN));
				return;
			}

			// Restock items
			foreach ( $order_items as $order_item ) {
				$_product   = $order->get_product_from_item( $order_item );

				if ( $_product && $_product->exists() && $_product->managing_stock() ) {
					$old_stock    = wc_stock_amount( $_product->stock );
					$new_quantity = $_product->increase_stock( $order_item['qty'] );

					$order->add_order_note( sprintf( __( 'Item #%s stock increased from %s to %s.', 'woocommerce' ), $order_item['product_id'], $old_stock, $new_quantity ) );

					do_action( 'woocommerce_restock_refunded_item', $_product->id, $old_stock, $new_quantity, $order );
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
				do_action( 'woocommerce_order_partially_refunded', $order_id, $refund->id, $refund->id );
			} else {
				do_action( 'woocommerce_order_fully_refunded', $order_id, $refund->id );
				$order->update_status( apply_filters( 'woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund->id ) );
				$response_data['status'] = 'fully_refunded';
			}

			do_action( 'woocommerce_order_refunded', $order_id, $refund->id );
			
			// Clear transients
			wc_delete_shop_order_transients( $order_id );
			wp_send_json_success();
		} catch (Exception $e) {
			if ( $refund && is_a( $refund, 'WC_Order_Refund' ) ) {
				wp_delete_post( $refund->id, true );
			}
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}
}

$refund = new ASRefund();
?>