<?php
/**
 * Template for displaying the curriculum of a course
 * Actually it prevents this template from being displayed.
 *
 * @author  leehankyeol
 * @package LearnPress/Templates
 * @version 0.0.1
 */

if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
}

global $course;

$curriculum_heading = apply_filters( 'learn_press_curriculum_heading', __( 'Course Curriculum', 'learnpress' ) );
?>

<?php if (false) { ?>
<div class="course-curriculum" id="learn-press-course-curriculum">

        <?php if ( $curriculum_heading ) { ?>

                <h3 class="course-curriculum-title"><?php echo $curriculum_heading; ?></h3>

        <?php } ?>

        <?php do_action( 'learn_press_before_single_course_curriculum' ); ?>

        <?php if ( $curriculum = $course->get_curriculum() ): ?>

                <ul class="curriculum-sections">

                        <?php foreach ( $curriculum as $section ) : ?>

                                <?php learn_press_get_template( 'single-course/loop-section.php', array( 'section' => $section ) ); ?>

                        <?php endforeach; ?>

                </ul>

        <?php else: ?>
                <?php echo apply_filters( 'learn_press_course_curriculum_empty', __( 'Curriculum is empty', 'learnpress' ) ); ?>
        <?php endif; ?>

        <?php do_action( 'learn_press_after_single_course_curriculum' ); ?>

</div>

<?php } ?>