<?php
/**
 * Template for displaying the price of a course
 * 
 * @cmsmasters_package 	Language School Child
 * @cmsmasters_version 	0.0.1
 *
 */
learn_press_prevent_access_directly();
global $product;
do_action( 'learn_press_before_course_price' );
?>
<div class="cmsmasters_course_meta_item">
	<div class="cmsmasters_course_meta_title">
		<span class="cmsmasters_theme_icon_lpr_price"><?php esc_html_e('Price', 'language-school-child');?></span>
	</div>
	<div class="cmsmasters_course_meta_info">
		<span class="course-price">
			<?php
			do_action( 'learn_press_begin_course_price' );

			$regular_price = get_post_meta(get_the_ID(), '_price', true);
			$sale_price = '';
			if (get_post_meta(get_the_ID(), '_sale_price', true)) {
				$sale_price = get_post_meta(get_the_ID(), '_regular_price', true); 
			}
			?>
			<?php if ($sale_price): ?>
				<span class="line-through float-right">₩ <span id="before-original-price"><?php echo number_format($product->get_price_including_tax(1, $sale_price)); ?></span></span>
				<br/> →
			<?php endif; ?>
			₩ <span id="original-price"><?php echo number_format($product->get_price_including_tax(1, $regular_price)); ?></span>
			<?php do_action( 'learn_press_end_course_price' );?>
		</span>
	</div>
</div>
<?php do_action( 'learn_press_after_course_price' );?>