<?php
/**
 * Template for displaying the contact button of a course
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

do_action( 'learn_press_before_course_contact_button' );
$button_text = apply_filters( 'learn_press_contact_button_text', esc_html__( 'Contact', 'language-school-child' ) );
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<a href="<?php echo esc_url(site_url('contact-us')); ?>"
		class="btn contact-button cmsmasters_button cmsmasters_but_bg_slide_left"><?php echo $button_text;?></a>
</div>
<?php do_action( 'learn_press_after_course_contact_button' );?>

<?php } ?>