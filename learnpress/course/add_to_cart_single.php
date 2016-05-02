<?php
/**
 * Template for displaying the register button of a product
 * @modified    leehankyeol
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
 
learn_press_prevent_access_directly();

global $product;
do_action( 'learn_press_before_course_register_button' );
$as_date = new ASDate(get_post_meta(get_the_ID(), 'as_date', true));
?>
<?php if ($product->product_type == 'bundle'): ?>
<div class="cmsmasters_course_meta_item text-align-center">
	<?php
		if ($product->stock_status == 'instock') {
			echo do_shortcode('[add_to_cart id="' . get_the_ID() . '" show_price="false" style="padding: 0;"]');
		} else {
			echo '<a href="#" class="cmsmasters_button red">완료되었습니다.</a>';
		} ?>
</div>
<?php elseif (!$as_date->is_past()): ?>
<div class="cmsmasters_course_meta_item text-align-center">
	<?php 
		if ($product->stock > 0):
			echo do_shortcode('[add_to_cart id="' . get_the_ID() . '" show_price="false" style="padding: 0;"]');
		else: ?>
			<a id="register-course" href="<?php echo esc_url(site_url('stand-by' . '?title=' . urlencode(get_the_title()) . '&price=' . urlencode(get_post_meta(get_the_ID(), '_price', true)) . '&course_id=' . urlencode(get_the_ID()) . '&num_to_enroll=1' )); ?>" class="btn take-course cmsmasters_button cmsmasters_but_bg_slide_top">대기자 등록</a>
	<?php
		endif; ?>
</div>
<?php endif; ?>
<?php do_action( 'learn_press_after_course_register_button' );?>