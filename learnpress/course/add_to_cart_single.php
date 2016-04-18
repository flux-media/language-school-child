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
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<?php if ($product->stock == 0) { ?>
		<a id="register-course" href="<?php echo esc_url(site_url('stand-by' . '?title=' . urlencode(get_the_title()) . '&price=' . 
		urlencode(get_post_meta(get_the_ID(), '_price', true)) . '&course_id=' . urlencode(get_the_ID()) . '&num_to_enroll=1' )); ?>"
	class="btn take-course cmsmasters_button cmsmasters_but_bg_slide_top">대기자 등록</a>
	<?php } else { ?>
		<?php echo do_shortcode('[add_to_cart id="' . get_the_ID() . '" show_price="false" style="padding: 0;"]'); ?>
	<?php } ?>
</div>
<?php do_action( 'learn_press_after_course_register_button' );?>
