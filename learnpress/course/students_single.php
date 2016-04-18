<?php
/**
 * Template for displaying the students, as well as limit, of a course
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
learn_press_prevent_access_directly();

global $product;
$max_number = get_post_meta(get_the_ID(), 'as_max_number_of_students', true);
if ($max_number) {
	$count = $max_number - $product->stock;
} else {
	$max_number = learn_press_get_limit_student_enroll_course();
	$count = learn_press_count_students_enrolled();
	if (!$count) {
		$count = 0;
	}
}
?>
<?php do_action( 'learn_press_before_course_students' );?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_students"><?php esc_html_e('Students', 'language-school-child'); ?></span>
	</div>
	<div class="cmsmasters_course_meta_info">
		<span class="course-students">
			<?php do_action( 'learn_press_begin_course_students' );?>
			<?php echo $count . ' / ' . $max_number; ?>
			<?php do_action( 'learn_press_end_course_students' );?>
		</span>
		<?php do_action( 'learn_press_after_course_students' );?>
	</div>
</div>