<?php
/** 
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<?php do_action( 'learn_press_before_course_landing_content' ); ?>

<?php 
	learn_press_get_template( 'course/students_single.php' );
	
	add_action( 'learn_press_course_landing_content', 'learn_press_course_payment_form', 40 );
	
	learn_press_get_template( 'course/tags_single.php' );

	learn_press_get_template( 'course/course_duration_single.php' );

	learn_press_get_template( 'course/classroom_single.php' );

	learn_press_get_template( 'course/number_of_student_single.php' );

	learn_press_get_template( 'course/price_single.php' );

	// TODO: Get rid of register_button_single
	learn_press_get_template( 'course/quick_buy_single.php' );
	// learn_press_get_template( 'course/add_to_cart_single.php' );
	learn_press_get_template( 'course/register_button_single.php' );

	learn_press_get_template( 'course/contact_button_single.php' );
	
	remove_action( 'learn_press_course_landing_content', 'learn_press_course_content', 60 );
	remove_action( 'learn_press_course_landing_content', 'learn_press_course_curriculum', 70 );
	remove_action( 'learn_press_course_landing_content', 'learn_press_print_review', 80 );
	do_action( 'learn_press_course_landing_content' ); 
?>

<?php do_action( 'learn_press_after_course_landing_content' ); ?>