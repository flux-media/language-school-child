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

global $course;
do_action( 'learn_press_before_course_register_button' );
// $button_text = apply_filters( 'learn_press_take_course_button_text', esc_html__( 'Take this course', 'language-school-child' ) );

if (get_post_meta(get_the_ID(), '_lpr_course_final', true) == 'yes') {
	$button_text = '대기자 등록';
} else {
	$button_text = '무통장입금';
}
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<a id="register-course" href="<?php echo esc_url(site_url('register-for-courses?title=' . urlencode(get_the_title()) . '&price=' . 
		urlencode(get_post_meta(get_the_ID(), '_lpr_course_price', true)) . '&course_id=' . urlencode(get_the_ID()) )); ?>"
	class="btn take-course cmsmasters_button cmsmasters_but_bg_slide_top"><?php echo $button_text;?></a>
</div>
<?php do_action( 'learn_press_after_course_register_button' );?>