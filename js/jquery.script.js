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
});

function doMasonry() {
	jQuery('.cmsmasters_learnpress_shortcode').masonry({
		itemSelector: '.lpr_course_post'
	});
}

// http://stackoverflow.com/questions/11582512/how-to-get-url-parameters-with-javascript/11582513#11582513
function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [, ""])[1].replace(/\+/g, '%20')) || null;
}