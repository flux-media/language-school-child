<?php
/**
 * Template for displaying the register button of a course via Iamport.
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
<?php if (get_post_meta(get_the_ID(), '_lpr_course_condition', true) == 100) { ?>
	<a href="#" class="cmsmasters_button red">완료되었습니다.</a>
<?php } else if (get_post_meta(get_the_ID(), '_lpr_course_final', true) == 'yes') { ?>
	<a href="#" class="cmsmasters_button red">마감되었습니다.</a>
<?php } else { ?>
	<a href="#" id="register-via-iamport" class="take-course cmsmasters_button">카드결제</a>
<?php } ?>
</div>
<?php do_action( 'learn_press_after_course_register_import_button' );?>