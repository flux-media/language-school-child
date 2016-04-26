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
if (get_post_type(get_the_ID()) == 'product') {
	$price = $product->get_price_including_tax(1, get_post_meta(get_the_ID(), '_price', true));
	if ($product->product_type == 'product_bundle') {
		if ($product->stock_status == 'instock') {

		} else {
			$visible = false;
		}
	} else {
		// 2016.04.30 PM 7:30 -> 2016.04.30 7:30 PM
		$thedate = get_post_meta(get_the_ID(), 'as_date', true);
		$thedate = preg_replace("/([0-9.]+) ([ap]m) ([0-9:]+)/i", "$1 $3 $2", $thedate);
		$start_at = DateTime::createFromFormat('Y.m.d g:i A', $thedate, new DateTimeZone('Asia/Seoul'));
		$now = new DateTime('now', new DateTimeZone('Asia/Seoul'));
		$is_past = false;
		if ($start_at === false) {
			// Let $is_past be false.
		} else {
			$interval_in_sec = $start_at->getTimeStamp() - $now->getTimeStamp();
		}
		// TODO: 7200?
		if ($interval_in_sec < 7200) {
			$is_past = true;
		}

		if ($is_past) {
			$visible = false;
		} else {
			if ($product->stock_status == 'instock') {

			} else {
				$page_slug = 'stand-by';
				$button_text = '대기자 등록';		
			}
		}
	}
} else {
	if (get_post_meta(get_the_ID(), '_lpr_course_final', true) == 'yes') {
		$page_slug = 'stand-by';
		$button_text = '대기자 등록';
	} else {
		$page_slug = 'register-for-courses';
		$button_text = '무통장입금';
	}
	$visible = (get_post_meta(get_the_ID(), '_lpr_course_condition', true) != 100);
	$price = get_post_meta(get_the_ID(), '_lpr_course_price', true);
}

if ($visible) {
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<a id="register-course" href="<?php echo esc_url(site_url($page_slug . '?title=' . urlencode(get_the_title()) . '&price=' . urlencode($price) . '&course_id=' . urlencode(get_the_ID()) . '&num_to_enroll=1' )); ?>"
	class="btn take-course cmsmasters_button cmsmasters_but_bg_slide_top"><?php echo $button_text;?></a>
</div>
<?php } ?>
<?php do_action( 'learn_press_after_course_register_button' );?>
