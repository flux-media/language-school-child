<?php
/** 
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
		do_action( 'learn_press_before_course_header' ); 
		
		language_school_thumb(get_the_ID(), 'cmsmasters-square-thumb', true, false, true, false, true, true, false);
	?>
	<div class="cmsmasters_lrp_archive_content">
		<header class="entry-header">
			<?php
			do_action( 'learn_press_before_the_title' );
			the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			
			remove_action('learn_press_after_the_title', 'learn_press_print_rate', 10 , 1);
			remove_action( 'learn_press_after_the_title', 'learn_press_course_thumbnail', 10 );
			do_action( 'learn_press_after_the_title' );
			?>
		</header>
		<!-- .entry-header -->
		<?php do_action( 'learn_press_before_course_content' ); ?>
		<div class="entry-content">
			<?php
			do_action( 'learn_press_before_the_content' );
			the_excerpt();
			do_action( 'learn_press_after_the_content' );
			?>
		</div>
		<!-- .entry-content -->
		<?php do_action( 'learn_press_before_course_footer' ); ?>
		<footer class="entry-footer">
			<?php
				remove_action( 'learn_press_entry_footer_archive', 'learn_press_course_price', 10 );
				remove_action( 'learn_press_entry_footer_archive', 'learn_press_course_wishlist_button', 10 );
				remove_action( 'learn_press_entry_footer_archive', 'learn_press_course_students', 20 );
				remove_action( 'learn_press_entry_footer_archive', 'learn_press_course_instructor', 30 );
				remove_action( 'learn_press_entry_footer_archive', 'learn_press_course_categories', 40 );
				remove_action( 'learn_press_entry_footer_archive', 'learn_press_course_tags', 50 );
				do_action( 'learn_press_entry_footer_archive' );
				
				learn_press_get_template( 'course/price_archive.php' );
				learn_press_get_template( 'course/categories_archive.php' );
			?>
		</footer>
		<!-- .entry-footer -->
	</div>
</article><!-- #post-## -->
