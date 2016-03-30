<?php
/**
 * Template for displaying the duration of a course
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */

learn_press_prevent_access_directly();
do_action( 'learn_press_before_course_duration' );

?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_duration">강연시간</span>
	</div>
	<div class="cmsmasters_course_meta_info">2시간</div>
</div>
<?php 
do_action( 'learn_press_after_course_duration' );
?>