<?php
/**
 * @package 	WordPress
 * @subpackage 	Language School Child
 * @version		0.0.1
 * 
 * Main Theme Functions File
 * Created by leehankyeol
 * 
 */

add_action('after_setup_theme', 'child_theme_setup');
function child_theme_setup() {
        load_child_theme_textdomain('language-school-child', get_stylesheet_directory().'/');
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts');
function theme_enqueue_scripts() {
	wp_enqueue_script('masonry');
	wp_enqueue_script('imagesLoaded', get_stylesheet_directory_uri(). '/js/imagesloaded.pkgd.min.js', array('masonry'), false, true);
	wp_enqueue_script('child_script', get_stylesheet_directory_uri() . '/js/jquery.script.js', array('jquery'), false, true);
}

add_action('after_setup_theme', 'my_child_shortcodes_setup');
function my_child_shortcodes_setup() {
	remove_shortcode('cmsmasters_learnpress');
	add_shortcode('cmsmasters_learnpress', 'my_cmsmasters_learnpress');
}

function my_cmsmasters_learnpress($atts, $content = null) {
	$new_atts = apply_filters('cmsmasters_learnpress_atts_filter', array( 
		'orderby' => 		'', 
		'order' => 			'', 
		'categories' => 	'', 
		'count' => 			'', 
		'columns' => 		'', 
		'classes' => 		'' 
	) );
	
	
	$shortcode_name = 'learnpress';
	
	$shortcode_path = CMSMASTERS_CONTENT_COMPOSER_TEMPLATE_DIR . '/cmsmasters-' . $shortcode_name . '.php';
	
	
	if (locate_template($shortcode_path)) {
		$template_out = cmsmasters_composer_load_template($shortcode_path, array( 
			'atts' => 		$atts, 
			'new_atts' => 	$new_atts, 
			'content' => 	$content 
		) );
		
		
		return $template_out;
	}
	
	
	extract(shortcode_atts($new_atts, $atts));
	
	
	$unique_id = uniqid();
	
	if ($columns == '4' || $columns == '5') {
		$course_thumb = 'cmsmasters-square-thumb';
	} else {
		$course_thumb = 'cmsmasters-project-thumb';
	}
	
	
	$out = '<div id="cmsmasters_learnpress_shortcode_' . $unique_id . '" class="cmsmasters_learnpress_shortcode' . 
	(($columns != '') ? ' cmsmasters_' . $columns : '') . 
	(($classes != '') ? ' ' . $classes : '') . 
	'">';
	
	
	$args = array( 
		'post_type' => 				'lpr_course', 
		'orderby' => 				$orderby, 
		'order' => 					$order, 
		'posts_per_page' => 		$count 
	);
	
	if ($categories != '') {
		$cat_array = explode(",", $categories);
		
		$args['tax_query'] = array( 
			array( 
				'taxonomy' => 'course_category', 
				'field' => 'slug', 
				'terms' => $cat_array 
			)
		);
	}
	
	
	$query = new WP_Query($args);
	
	
	if ($query->have_posts()) : 
		while ($query->have_posts()) : $query->the_post();
	
		$course_id = get_the_ID();
		
		$course_duration = get_post_meta( $course_id, '_lpr_course_duration', true );
		$term_list = get_the_term_list( $course_id, 'course_category', '', ', ', '' );
		$cmsmasters_title = cmsmasters_child_title($course_id, false);
		$posttags = get_the_term_list( $course_id, 'course_tag', '', ', ', '' );
		
		$out .= "<article class=\"lpr_course_post\">" . "\n" .
			 "<a href=" . get_the_permalink( $course_id ) . ">" .
				 get_the_post_thumbnail($course_id, (($type) ? $type : 'full'), array( 
					'class' => 'full-width', 
					'alt' => $cmsmasters_title, 
					'title' => $cmsmasters_title 
				)) . "</a>" . "\n" . 
			"<div class=\"lpr_course_inner\">" . "\n" . 
			"<header class=\"entry-header lpr_course_header\">
				<h6 class=\"entry-title lpr_course_title\"><a href=" . get_the_permalink( $course_id ) . ">" . get_the_title( $course_id ) . "</a></h6>
			</header>" . "\n";

		$out .= "<div><div class=\"lpr_course_author\">" . get_the_author_meta('nickname') . "</div>";
		if ($posttags != '') {
			$out .= "<div class=\"lpr_course_date\">" . $posttags . "</div>";
		} else {
			$out .= "<div class=\"lpr_course_date\">날짜 미정</div>";
		}
		$out .= "<div class=\"lpr_course_subtitle\">" . nl2br(get_the_excerpt( $course_id )) . "</div>";
		$out .= "</div>";
			
		if ( !learn_press_is_free_course( $course_id ) ) {
			$out .= "<div class=\"cmsmasters_course_price\">" . learn_press_get_currency_symbol() . number_format(floatval( get_post_meta( $course_id, '_lpr_course_price', true ) ) ) . "</div>";
		} else {
			$out .= "<div class=\"cmsmasters_course_free\">" . esc_html__('Free', 'language-school') . "</div>";
		}

		if ($term_list != '') {
			$out .= "<div class=\"entry-meta cmsmasters_cource_cat\">" . $term_list . "</div>";
		}
		
		// Removed rate.
		$out .= "</div>" . "\n";
		
		// Removed footer.	
		$out .= "</article>" . "\n";
	
		endwhile;
	endif;
	
	
	wp_reset_postdata();
	
	wp_reset_query();
	
	
	$out .= '</div>';
	
	
	return $out;
}

/* Get Title Function */
function cmsmasters_child_title($cmsmasters_id, $show = true) { 
	$cmsmasters_heading = get_post_meta($cmsmasters_id, 'cmsmasters_heading', true);
	
	$cmsmasters_heading_title = get_post_meta($cmsmasters_id, 'cmsmasters_heading_title', true);
	
	$out = '';
	
	if ($cmsmasters_heading == 'custom' && $cmsmasters_heading_title != '') {
		$out .= esc_attr($cmsmasters_heading_title);
	} else {
		$out .= esc_attr(strip_tags(get_the_title($cmsmasters_id) ? get_the_title($cmsmasters_id) : $cmsmasters_id));
	} 
    
	
    if ($show) {
        echo $out;
    } else {
        return $out;
    }
}

?>