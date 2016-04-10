<?php
/**
 * @package 	WordPress
 * @subpackage 	Language School Child
 * @version 	0.0.1
 * 
 * Website Header Template
 * Created by leehankyeol
 * 
 */

$cmsmasters_option = language_school_get_global_options();
?><!DOCTYPE html>
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8)]><!-->
<html <?php language_attributes(); ?> class="cmsmasters_html">
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="google-site-verification" content="CBeJ1mnatITQVLhBCIbFBKzuLbi7T4BaXVKDcfMlAa8" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php esc_url(bloginfo('pingback_url')); ?>" />
<?php 
if (is_singular() && get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
}

wp_head();
?>
<script type="text/javascript" src="https://service.iamport.kr/js/iamport.payment-1.1.1.js"></script>
</head>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-75720588-1', 'auto');
  ga('send', 'pageview');
</script>
<body <?php body_class(); ?>>

<div class="iamport-payment-box hidden" id="iamport-dialog">
	<h5>수강료 결제</h5>
	<p>아래 정보를 기입 후 결제진행해주세요.</p>
	<p>
		<label>결제수단</label>
		<select name="pay_method">
			<option value="card">신용카드</option>
			<option value="vbank">가상계좌</option>
			<option value="phone">휴대폰소액결제</option>
		</select>
	</p>
	<p>
		<label>결제자 Email</label>
		<input type="email" name="buyer_email">
	</p>
	<p>
		<label>결제자 성함</label>
		<input type="text" name="buyer_name">
	</p>
	<p>
		<label>결제자 전화번호</label>
		<input type="tel" name="buyer_tel">
	</p>									
	<p class="button-holder" style="text-align:center">
		<a href="#" class="iamport-payment-submit">결제하기</a>
	</p>
</div>

<div class="iamport-result-box hidden" id="iamport-result-box" style="">
	<h5 class="title">결제결과</h5>
	<p class="content"></p>
</div>
	
<!-- _________________________ Start Page _________________________ -->
<div id="page" class="<?php language_school_get_page_classes($cmsmasters_option); ?>hfeed site">

<!-- _________________________ Start Main _________________________ -->
<div id="main">
	
<!-- _________________________ Start Header _________________________ -->
<header id="header">
	<?php 
	language_school_header_top($cmsmasters_option);
	
	
	language_school_header_mid($cmsmasters_option);
	
	
	language_school_header_bot($cmsmasters_option);
	?>
</header>
<!-- _________________________ Finish Header _________________________ -->

	
<!-- _________________________ Start Middle _________________________ -->
<div id="middle"<?php echo (is_404()) ? ' class="error_page"' : ''; ?>>
<?php 
if (!is_404() && !is_home()) {
	language_school_page_heading();
} else {
	echo "<div class=\"headline\">
		<div class=\"headline_outer cmsmasters_headline_disabled\"></div>
	</div>";
}


list($cmsmasters_layout, $cmsmasters_page_scheme) = language_school_theme_page_layout_scheme();


echo '<div class="middle_inner' . (($cmsmasters_page_scheme != 'default') ? ' cmsmasters_color_scheme_' . $cmsmasters_page_scheme : '') . '">' . "\n" . 
	'<div class="content_wrap ' . $cmsmasters_layout . 
	((is_singular('project')) ? ' project_page' : '') . 
	((is_singular('profile')) ? ' profile_page' : '') . 
	'">' . "\n\n";

