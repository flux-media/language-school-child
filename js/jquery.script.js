/**
 * @package 	WordPress
 * @subpackage 	Language School Child
 * @version 	0.0.1
 * 
 * Theme Custom Scripts
 * Created by leehankyeol
 * 
 */


"use strict";

jQuery(document).ready(function() { 
	/* Masonry */
	doMasonry();
	// When there're tabs, doMasonry should be called each time a new tab is active.
	jQuery('.cmsmasters_tabs_list_item').on('click', doMasonry);

	/* Pricing */
	var $pricing = jQuery('.cmsmasters_price_wrap'),
		title, price, courseId;
	if ($pricing.length > 0) {
		title = getURLParameter('title');
		price = getURLParameter('price');
		courseId = getURLParameter('course_id');
		if (price === 'Free') {
			price = 0;
		}
		if (!title && !price && !courseId) {
			jQuery('cmsmasters_pricing_table').remove();
		} else {
			$pricing.html('<span class="cmsmasters_price">' + price + '</span><span class="cmsmasters_currency">원</span>');
		}
		jQuery('#avengerschool-course-title').val('강의명: ' + courseId + '. ' + title);
	}

	/* Sticky */
	var $sidebar = jQuery('.cmsmasters_course_sidebar'), width;
	if ($sidebar.length > 0) {
		doSticky();
		jQuery(window).resize(doSticky);
		jQuery(window).scroll(doSticky);
	}
});

function doSticky() {
	var $sidebar = jQuery('.cmsmasters_course_sidebar'),
		$window = jQuery(window),
		windowWidth = $window.width(),
		scrollTop = $window.scrollTop(),
		containerWidth = jQuery('.middle_inner').width(),
		margin = 120;
	if (windowWidth > 950 && scrollTop > 200) {
		if (windowWidth < 1200) {
			margin = 30;
		} else {
			margin = 120;
		}
		$sidebar.addClass('sticky');
		$sidebar.css('right', ((windowWidth - containerWidth) / 2 + margin) + 'px');
	} else {
		$sidebar.removeClass('sticky');
		$sidebar.css('right', 'auto');
	}
}

function doMasonry() {
	var $shortCode = jQuery('.cmsmasters_learnpress_shortcode').masonry({
		itemSelector: '.lpr_course_post'
	});
	$shortCode.imagesLoaded().progress(function() {
		$shortCode.masonry('layout');
	});
}

// http://stackoverflow.com/questions/11582512/how-to-get-url-parameters-with-javascript/11582513#11582513
function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || null;
}