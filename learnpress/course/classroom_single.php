<?php
/**
 * Template for displaying the classroom of a course
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */

learn_press_prevent_access_directly();
do_action( 'learn_press_before_course_classroom' );

$classroom = get_post_meta(get_the_ID(), 'as_location', true);
?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_tag"><?php esc_html_e('Classroom', 'language-school-child'); ?></span>
	</div>
	<div class="cmsmasters_course_meta_info">
		<?php echo $classroom; ?>		
	</div>
</div>
<?php 
do_action( 'learn_press_after_course_classroom' );
?>