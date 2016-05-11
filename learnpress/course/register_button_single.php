<?php
/**
 * Template for displaying the register button of a course
 * @modified    leehankyeol
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
 
learn_press_prevent_access_directly();

global $product;
do_action( 'learn_press_before_course_register_button' );

$page_slug = 'register-for-courses';
$button_text = '무통장입금';
$visible = true;
$price = $product->get_price_including_tax(1, get_post_meta(get_the_ID(), '_price', true));
if ($product->product_type == 'bundle') {
	if ($product->stock_status == 'instock') {

	} else {
		$visible = false;
	}
} else {
	$as_product = new ASProduct( $product );

	if ($as_product->is_past() || $as_product->is_reservation_over()) {
		$visible = false;
	} else {
		if ($product->stock_status == 'instock') {

		} else {
			$page_slug = 'stand-by';
			$button_text = '대기자 등록';
		}
	}
}
if ($visible) {
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<a id="register-course" href="<?php echo esc_url(site_url($page_slug . '?title=' . urlencode(get_the_title()) . '&price=' . urlencode($price) . '&course_id=' . urlencode(get_the_ID()) . '&num_to_enroll=1' )); ?>"
	class="btn take-course cmsmasters_button cmsmasters_but_bg_slide_top"><?php echo $button_text; ?></a>
</div>
<?php } ?>
<?php do_action( 'learn_press_after_course_register_button' );?>
