<?php
/**
 * Template for displaying the tags of a course
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */

learn_press_prevent_access_directly();
do_action( 'learn_press_before_course_tags' );
$tags = get_the_term_list( get_the_ID(), 'course_tag', '', ', ', '' );
if( $tags ) {
?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_tag"><?php esc_html_e('Tags', 'language-school-child'); ?></span>
	</div>
	<div class="cmsmasters_course_meta_info">
<?php
	printf(
		'<span class="tags-links">%s</span>',	
		$tags
	);
?>
	</div>
</div>
<?php
}	
do_action( 'learn_press_after_course_tags' );
