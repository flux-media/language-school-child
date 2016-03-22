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
$course_status = learn_press_get_user_course_status();
// only show register button if user had not enrolled

if ( ( '' == $course_status ) ) {

do_action( 'learn_press_before_course_register_button' );
$button_text = apply_filters( 'learn_press_take_course_button_text', esc_html__( 'Take this course', 'learn_press' ) );
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<a href="<?php echo esc_url(site_url('register-for-courses?title=' . urlencode(get_the_title()) . '&price=' . urlencode(learn_press_get_course_price( null, true )) . '&course_id=' . urlencode(get_the_ID()) )); ?>" class="btn take-course cmsmasters_button"><?php echo $button_text;?></a>
</div>
<?php do_action( 'learn_press_after_course_register_button' );?>

<?php } ?>