<?php
/**
 * Template Name: Lecturers List Page
 *
 * @package WordPress
 * @subpackage Language School Child
 * @author Lee Han Kyeol
 */

get_header();
?>
    <div
        class="cmsmasters_row cmsmasters_color_scheme_default full-width main-slider cmsmasters_row_top_default cmsmasters_row_bot_default cmsmasters_row_boxed">
        <div class="cmsmasters_row_outer_parent">
            <div class="cmsmasters_row_outer">
                <div class="cmsmasters_row_inner cmsmasters_row_no_margin">
                    <div class="cmsmasters_row_margin cmsmasters_11">
                        <div class="cmsmasters_column one_first">
                            <?php do_shortcode('[display_as_lecturers]'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
?>