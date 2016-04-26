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

?>
<div class="cmsmasters_course_meta_item text-align-center">
<?php
	if ($product->product_type == 'product_bundle') {
		if ($product->stock_status == 'instock') {
			echo do_shortcode('[wc_quick_buy]');
		} else {
			echo '<a href="#" class="cmsmasters_button red">완료되었습니다.</a>';	
		}
	} else {
		if ($is_past) {
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