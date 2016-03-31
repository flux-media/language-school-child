<?php
/**
 * Template for displaying the number of student to enroll for a course.
 * @modified    leehankyeol
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
 
learn_press_prevent_access_directly();

global $course;
do_action( 'learn_press_before_course_number_of_student' );
// $button_text = apply_filters( 'learn_press_take_course_button_text', esc_html__( 'Take this course', 'language-school-child' ) );
$button_text = '무통장입금';
?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_students">인원</span>
	</div>
	<div class="cmsmasters_course_meta_info">
		<select id="number-of-students">
			<?php for ($i = 0; $i < 10; $i++) {
				echo '<option value="' . ($i + 1) . '">' . ($i + 1) . '명</option>';
			} ?>
		</select>
	</div>
</div>
<?php do_action( 'learn_press_after_course_number_of_student' );?>