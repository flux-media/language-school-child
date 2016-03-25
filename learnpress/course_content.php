<?php
/** 
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
	$cmsmasters_lpr_course_title = get_post_meta(get_the_ID(), 'cmsmasters_lpr_course_title', true);
	$cmsmasters_lpr_course_image = get_post_meta(get_the_ID(), 'cmsmasters_lpr_course_image', true);
	$user_url = get_the_author_meta('user_url');
	$facebook = get_the_author_meta('facebook');
	$twitter = get_the_author_meta('twitter');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/CreativeWork">
	<div class="cmsmasters_course_content">
		<?php do_action( 'learn_press_before_course_header' ); ?>
		<header class="entry-header">
			<?php
			do_action( 'learn_press_before_the_title' );
			
			if ($cmsmasters_lpr_course_title == 'true') {
				the_title( '<h2 class="entry-title cmsmasters_course_title">', '</h2>' );
			}
			
			remove_action( 'learn_press_after_the_title', 'learn_press_course_thumbnail');
			
			if ($cmsmasters_lpr_course_image == 'true' && has_post_thumbnail()) {
				language_school_thumb(get_the_ID(), 'cmsmasters-post-thumbnail', false, true, true, false, true, true, false);
			}
			
			remove_action('learn_press_after_the_title', 'learn_press_print_rate');
			do_action( 'learn_press_after_the_title' );	
			?>
		</header>
		<!-- .entry-header -->
		<?php do_action( 'learn_press_before_course_content' ); ?>
		<div class="entry-content">
			<?php
			do_action( 'learn_press_before_the_content' );		
			if ( learn_press_is_enrolled_course() ) {
				learn_press_get_template_part( 'course_content', 'learning_page' );
			} else {
				learn_press_get_template_part( 'course_content', 'landing_page' );
			}
			do_action( 'learn_press_after_the_content' );
			?>

			<!-- Author -->
			<div class="cmsmasters_profile vertical">
				<article class="profile">
					<div class="pl_img">
						<figure>
							<a><?php echo get_avatar( get_the_author_meta( 'user_email' ), 360 ); ?></a>
						</figure>
					</div>
					<div class="pl_content">
						<h2 class="entry-title">
							<a>강연자: <?php echo get_the_author_meta('last_name') . get_the_author_meta('first_name'); ?></a>
							<div class="pl_social">
								<ul class="pl_social_list">
									<?php if ($user_url): ?>
									<li>
										<a href="<?php echo $user_url; ?>" class="cmsmasters-icon-custom-blogger-1" title="Blog" target="_blank"></a>
									</li>
									<?php endif; ?>
									<?php if ($twitter): ?>
									<li>
										<a href="https://twitter.com/@<?php echo $twitter; ?>" class="cmsmasters-icon-custom-twitter-6" title="Twitter" target="_blank"></a>
									</li>
									<?php endif; ?>
									<?php if ($facebook): ?>
									<li>
										<a href="<?php echo $facebook; ?>" class="cmsmasters-icon-custom-facebook-6" title="Facebook" target="_blank"></a>
									</li>
									<?php endif; ?>
								</ul>
							</div>
						</h2>
						<div class="entry-content"><?php the_author_meta( 'description' ); ?></div>
					</div>
					<div class="cl"></div>
				</article>
			</div>
		</div>
		<!-- .entry-content -->

		<?php do_action( 'learn_press_before_course_footer' ); ?>
		<footer class="entry-footer">
			<?php
			edit_post_link( esc_html__( 'Edit', 'learn_press' ), '<span class="edit-link">', '</span>' );
			?>
		</footer>
		<!-- .entry-footer -->
	</div>
	<div class="cmsmasters_course_sidebar">
		<?php
			if ( learn_press_is_enrolled_course() ) {
				learn_press_get_template_part( 'course_content', 'learning_sidebar' );
			} else {
				learn_press_get_template_part( 'course_content', 'landing_sidebar' );
			}
		?>
	</div>
</article><!-- #post-## -->
