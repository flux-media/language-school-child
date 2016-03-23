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
	learn_press_get_template( 'course/price_single.php' );
	
	learn_press_get_template( 'course/students_single.php' );
	
	// learn_press_get_template( 'course/duration_single.php' );
	
	// learn_press_get_template( 'course/wishlist_button_single.php' );
	
	add_action( 'learn_press_course_landing_content', 'learn_press_course_payment_form', 40 );
	
	learn_press_get_template( 'course/categories_single.php' );
	
	learn_press_get_template( 'course/tags_single.php' );
	
	// if ( function_exists( 'learn_press_print_rate' ) ) {
		// learn_press_get_template( 'course/rate_single.php' );
	// }

	learn_press_get_template( 'course/register_button_single.php');

	learn_press_get_template( 'course/contact_button_single.php');
	
	// learn_press_get_template( 'course/enroll_button_single.php' );
	
	remove_action( 'learn_press_course_landing_content', 'learn_press_course_content', 60 );
	remove_action( 'learn_press_course_landing_content', 'learn_press_course_curriculum', 70 );
	remove_action( 'learn_press_course_landing_content', 'learn_press_print_review', 80 );
	do_action( 'learn_press_course_landing_content' ); 
?>

<?php do_action( 'learn_press_after_course_landing_content' ); ?>