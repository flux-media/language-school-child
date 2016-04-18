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
do_action( 'learn_press_before_course_number_of_student' );

global $product;

if ($product) {
	$max_number = $product->stock;
	if ($max_number > 10) {
		$max_number = 10;
	}	
} else {
	$max_number = 10;
}
?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_students">인원</span>
	</div>
	<div class="cmsmasters_course_meta_info">
		<select id="number-of-students">
			<?php for ($i = 0; $i < $max_number; $i++) {
				echo '<option value="' . ($i + 1) . '">' . ($i + 1) . '명</option>';
			} ?>
		</select>
	</div>
</div>
<?php do_action( 'learn_press_after_course_number_of_student' );?>