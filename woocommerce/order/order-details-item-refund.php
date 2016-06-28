<?php
/**
 * Order Item Refund Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see    http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
    return;
}

$as_product = new ASProduct($product);
$refundable = $as_product->get_refund_rate() * $order->get_line_subtotal($item, true);
?>
<tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
    <td class="product-name">
        <?php
        $is_visible = $product && $product->is_visible();

        echo apply_filters('woocommerce_order_item_name', $is_visible ? sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']) : $item['name'], $item, $is_visible);
        echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times; %s', $item['qty']) . '</strong>', $item);

        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order);

        $order->display_item_meta($item);
        $order->display_item_downloads($item);

        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order);
        ?>
    </td>
    <td class="product-total">
        <?php if ($order->get_line_subtotal($item, true) > 0) {
            echo '₩ ' . $refundable;
        } ?>
    </td>
    <td class="product-refund">
        <?php if ($refundable > 0): ?>
            <?php if (is_user_logged_in()): ?>
                <a class="button-product-refund" href="#" data-order-id="<?php echo $order->id; ?>">환불요청</a>
            <?php else: ?>
                <a class="button-product-refund" href="<?php echo esc_url(site_url('contact-us')); ?>">환불요청</a>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($order->get_line_subtotal($item, true) > 0): ?>
                환불불가
            <?php endif; ?>
        <?php endif; ?>
    </td>
</tr>