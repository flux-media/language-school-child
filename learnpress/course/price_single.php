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

			$regular_price = get_post_meta(get_the_ID(), '_lpr_course_price', true); 
			$sale_price = '';

			if ($regular_price == '') {
				$regular_price = get_post_meta(get_the_ID(), '_price', true);
				if (get_post_meta(get_the_ID(), '_sale_price', true)) {
					$sale_price = get_post_meta(get_the_ID(), '_regular_price', true); 
				}
			} else {
				$sale_price = get_post_meta(get_the_ID(), '_lpr_course_duration', true );
			}
			?>
			<?php if ($sale_price): ?>
				<?php if ($product): ?>
					<span class="line-through float-right">₩ <span id="before-original-price"><?php echo number_format($product->get_price_including_tax(1, $sale_price)); ?></span></span>
				<?php else: ?>
					<span class="line-through float-right">₩ <span id="before-original-price"><?php echo number_format($sale_price); ?></span></span>
				<?php endif; ?>
				<br/> →
			<?php endif; ?>
				<?php if ($product): ?>
					₩ <span id="original-price"><?php echo number_format($product->get_price_including_tax(1, $regular_price)); ?></span>
				<?php else: ?>
					₩ <span id="original-price"><?php echo number_format($regular_price); ?></span>
				<?php endif; ?>
			<?php do_action( 'learn_press_end_course_price' );?>
		</span>
	</div>
</div>
<?php do_action( 'learn_press_after_course_price' );?>