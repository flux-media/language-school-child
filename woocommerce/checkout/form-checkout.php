<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<!-- Terms -->

<div class="col2-set" id="customer_details">
	<div class="middle_content entry" role="main">
		<h2 class="p1"><span class="s1">취소 및 환불 규정</span></h2>
		<p>1. 수강신청 취소/환불은 전화(010-8368-2018) 또는 e-mail(slave@avengerschool.com)을 통해 가능합니다.</p>
		<p>2. 업무시간 외 또는 주말/공휴일에는 e-mail을 통해 환불요청을 남겨주실 경우에만 해당 일로 취소/환불 접수가 처리됩니다.</p>
		<p>3. 환불 신청에서 실제 환불까지는 평균 3영업일 정도가 소요됩니다.(당일 결제한 카드 취소의 경우는 당일)</p>
		<p>4. 양도를 진행할 경우에는 강연 시작 전까지만 가능합니다.</p>
		<p>어벤져스쿨 환불 규정은 어벤져스쿨 이용약관 및 법령에서 정한 절차에 따라 진행됩니다.</p>
		<p>■ 단일 강연 환불 기준<br>
			* 강연 개시 1주 전까지: 100%<br>
			* 강연 개시 72시간 전까지: 70%<br>
			* 강연 개시 24시간 전까지: 50%<br>
			* 강연 개시 24시간 이내: 0%</p>
		<p>■ 패키지 상품 환불 기준<br>
			* 첫 강연 개시 1주 전까지: 100%<br>
			* 첫 강연 개시 24시간 전까지: 90%<br>
			* 전체 강연 1/4 경과까지: 65%<br>
			* 전체 강연 1/2 경과까지: 40%<br>
			* 전체 강연 3/4 경과 후: 0%</p>
		<div class="cl"></div>
	</div>
</div>