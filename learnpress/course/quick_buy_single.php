<?php
/**
 * Template for displaying the quick buy button.
 * @modified    leehankyeol
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
 
learn_press_prevent_access_directly();

global $product;
do_action( 'learn_press_before_quick_buy_button' );
$as_product = new ASProduct( $product );
?>
<div class="cmsmasters_course_meta_item text-align-center">
<?php
	if ($product->product_type == 'bundle') {
		if ($product->stock_status == 'instock') {
			echo do_shortcode('[wc_quick_buy]');
		} else {
			echo '<a href="#" class="cmsmasters_button red">완료되었습니다.</a>';	
		}
	} else {
		if ($as_product->is_reservation_over()) {
			echo '<a href="#" class="cmsmasters_button red">현장 등록 바랍니다.</a>';
		} else if ($as_product->is_past()) {
			echo '<a href="#" class="cmsmasters_button red">완료되었습니다.</a>';
		} else {
			if ($product->stock > 0) {
				echo do_shortcode('[wc_quick_buy]');
			} else {
				echo '<a href="#" class="cmsmasters_button red">만석입니다.</a>';
			}
		}
	}
?>
</div>
<?php do_action( 'learn_press_after_quick_buy_button' );?>