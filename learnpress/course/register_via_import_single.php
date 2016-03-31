<?php
/**
 * Template for displaying the register button of a course via Import.
 * @modified    leehankyeol
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
 
learn_press_prevent_access_directly();

global $course;
do_action( 'learn_press_before_course_register_import_button' );
?>
<div class="cmsmasters_course_meta_item text-align-center">
	<a href="#" id="register-via-iamport" class="take-course cmsmasters_button">카드결제</a>
</div>
<?php do_action( 'learn_press_after_course_register_import_button' );?>