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
?>
<div class="cmsmasters_course_meta_item text-align-center">
<?php if ($product->stock == 0) { ?>
	<a href="#" class="cmsmasters_button red">만석입니다.</a>
<?php } else { ?>
	<?php echo do_shortcode('[wc_quick_buy]'); ?>
<?php } ?>
</div>
<?php do_action( 'learn_press_after_quick_buy_button' );?>