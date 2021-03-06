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
<meta name="naver-site-verification" content="65d459042d092543cca4ce0d8a8664f057689179"/>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php esc_url(bloginfo('pingback_url')); ?>" />
<?php 
if (is_singular() && get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
}

wp_head();
?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');

fbq('init', '1034933649928556');
fbq('track', 'PageView');
<?php if (get_post_type() == 'product'): ?>
fbq('track', 'ViewContent');
<?php elseif (is_page('checkout')): ?>
fbq('track', 'AddToCart');
fbq('track', 'InitiateCheckout');
<?php endif; ?>
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1034933649928556&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
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

