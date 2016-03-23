<?php
/**
 * Template for displaying the price of a course
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
learn_press_prevent_access_directly();
if ( learn_press_is_enrolled_course() ) {
    return;
}
do_action( 'learn_press_before_course_price' );
?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_price"><?php esc_html_e('Price', 'language-school-child');?></span>
	</div>
	<div class="cmsmasters_course_meta_info">
		<span class="course-price">
			<?php do_action( 'learn_press_begin_course_price' );?>
			<?php echo learn_press_get_course_price( null, true );?>
			<?php do_action( 'learn_press_end_course_price' );?>
		</span>
	</div>
</div>
<?php do_action( 'learn_press_after_course_price' );?>