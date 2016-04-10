/**
 * @package 	WordPress
 * @subpackage 	Language School Child
 * @version 	0.0.1
 * 
 * Theme Custom Scripts
 * Created by leehankyeol
 * 
 */

 // var domain = 'http://localhost:8888/';
 var domain = 'http://avengerschool.com/';

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
		title = decodeHtml(getURLParameter('title'));
		price = getURLParameter('price');
		courseId = getURLParameter('course_id');
		if (price === 'Free') {
			price = 0;
		}
		if (!title && !price && !courseId) {
			jQuery('.cmsmasters_pricing_table').remove();
		} else {
			$pricing.html('<span class="cmsmasters_price">' + price + '</span><span class="cmsmasters_currency">원</span>');
		}
		jQuery('#avengerschool-course-title').val(title);
		jQuery('#avengerschool-num-to-enroll').val(getURLParameter('num_to_enroll'));
	}

	/* Sticky */
	var $sidebar = jQuery('.cmsmasters_course_sidebar'), width;
	if ($sidebar.length > 0) {
		doSticky();
		jQuery(window).resize(doSticky);
		jQuery(window).scroll(doSticky);
	}

	/* Form */
	jQuery('.wpcf7').on('wpcf7:submit', function(event) {
		if (window.location.href.indexOf('register-for-courses') > -1) {
			// Send 
			jQuery.ajax({
				method: 'POST',
				url: domain + 'wp-admin/admin-ajax.php',
				data: {
					action: 'send_registration_feedback',
					amount: jQuery('.cmsmasters_price').text(),
					course_title: jQuery('#avengerschool-course-title').val(),
					tel: jQuery('input[name=your-phone-number]').val(),
					email: jQuery('input[name=your-email]').val(),
					name: jQuery('input[name=your-name]').val()
				}
			});
		}
	});

	/* Number of students event listener */
	var $numberOfStudents = jQuery('#number-of-students'),
		$beforeOriginalPrice = jQuery('#before-original-price'),
		$originalPrice = jQuery('#original-price'),
		$registerCourse = jQuery('#register-course'),
		$registerViaIamport = jQuery('#register-via-iamport'),
		$iamportPaymentBox = jQuery('#iamport-dialog'),
		$iamportResultBox = jQuery('#iamport-result-box'),
		numberOfStudents, beforeOriginalPrice, originalPrice, href,
		iamport_dialog = $iamportPaymentBox.dialog({
			autoOpen: false, modal: true, draggable: false, close: function () {
				jQuery(this).find('.iamport-payment-submit').attr('data-progress', null).text('결제하기');
			}
		}),
		result_dialog = $iamportResultBox.dialog({
			autoOpen: false, modal: true, draggable: false
		});
	$iamportPaymentBox.removeClass('hidden');
	$iamportResultBox.removeClass('hidden');

	$iamportPaymentBox.prop('id', 'fake-id');
	jQuery('.take-course.fake-button').hide();
	if ($beforeOriginalPrice.length > 0) {
		beforeOriginalPrice = parseInt($beforeOriginalPrice.text().replace(',', ''), 10);
	}
	originalPrice = parseInt($originalPrice.text().replace(',', ''), 10);
	href = $registerCourse.attr('href');
	numberOfStudents = parseInt($numberOfStudents.val(), 10);

	$numberOfStudents.change(function() {
		numberOfStudents = parseInt($numberOfStudents.val(), 10);

		// Update texts.
		if ($beforeOriginalPrice.length > 0) {
			$beforeOriginalPrice.text(numberWithCommas(numberOfStudents * beforeOriginalPrice));
		}
		$originalPrice.text(numberWithCommas(numberOfStudents * originalPrice));

		// Update href to registration for the course.
		var newHref = replaceUrlParam(href, 'price', numberOfStudents * originalPrice);
		newHref = replaceUrlParam(newHref, 'num_to_enroll', numberOfStudents);
		$registerCourse.prop('href', newHref);
	});
	$registerViaIamport.on('click', function(e) {
		e.preventDefault();
		iamport_dialog.dialog('open');
		IMP.init('imp37043335');
		return false;
	});
	$iamportPaymentBox.on('click', 'a.iamport-payment-submit', function() {
		var $this = jQuery(this);
		if ($this.attr('data-progress') == 'true') {
			return false;
		}

		$this.attr('data-progress', 'true').text('결제 중입니다...');
		var box = $this.closest('.iamport-payment-box'),
			pay_method = box.find('select[name="pay_method"]').val(),
			buyer_name = box.find('input[name="buyer_name"]').val() || '',
			buyer_email = box.find('input[name="buyer_email"]').val() || '',
			buyer_tel = box.find('input[name="buyer_tel"]').val() || '';

		if (!buyer_email) {
			alert('결제자 Email을 입력해주세요.');
			return false;
		}
		if (!buyer_name) {
			alert('결제자 성함을 입력해주세요.');
			return false;
		}
		if (!buyer_tel) {
			alert('결제자 전화번호를 입력해주세요.');
			return false;
		}

		var order_amount = parseInt(numberOfStudents * originalPrice),
			order_title = jQuery('h2.cmsmasters_course_title').text();
		jQuery.ajax({
			method: 'POST',
			url: domain + 'wp-admin/admin-ajax.php',
			data: {
				action: 'get_order_uid',
				order_title: order_title,
				pay_method: pay_method,
				buyer_name: buyer_name,
				buyer_email: buyer_email,
				buyer_tel: buyer_tel,
				order_amount: order_amount
			}
		}).done(function (rsp) {
			iamport_dialog.dialog('close');

			IMP.request_pay({
				pay_method: pay_method,
				merchant_uid: rsp.order_uid,
				name: order_title,
				amount: order_amount,
				buyer_name: buyer_name,
				buyer_email: buyer_email,
				buyer_tel: buyer_tel,
				m_redirect_url: rsp.thankyou_url
			}, function (callback) {
				if (callback.success) {
					// Send 
					jQuery.ajax({
						method: 'POST',
						url: domain + 'wp-admin/admin-ajax.php',
						data: {
							action: 'send_registration_feedback',
							status: 'success',
							merchant_uid: rsp.order_uid,
							thankyou_url: rsp.thankyou_url
						}
					});

					//TODO : 다음버전에서 결제완료처리는 ajax로 하기
					result_dialog.find('.title').text('결제완료 처리중');
					result_dialog.find('.content').text('잠시만 기다려주세요. 결제완료 처리중입니다.');
					result_dialog.find('.iamport-payment-link').attr('href', rsp.thankyou_url);
					result_dialog.dialog('open');

					location.href = rsp.thankyou_url;
				} else {
					jQuery.ajax({
						method: 'POST',
						url: domain + 'wp-admin/admin-ajax.php',
						data: {
							action: 'send_registration_feedback',
							status: 'failure',
							merchant_uid: rsp.order_uid
						}
					});

					result_dialog.find('.title').text('결제실패');
					result_dialog.find('.content').html('다음과 같은 사유로 결제에 실패하였습니다.<br>' + callback.error_msg);
					result_dialog.dialog('open');
					//location.href = rsp.thankyou_url;
				}
			});
		});

		return false;
	});
});

function doSticky() {
	var $sidebar = jQuery('.cmsmasters_course_sidebar'),
		$window = jQuery(window),
		$content = jQuery('.cmsmasters_course_content'),
		windowWidth = $window.width(),
		scrollTop = $window.scrollTop(),
		containerWidth = jQuery('.middle_inner').width(),
		// http://stackoverflow.com/questions/12749844/finding-the-position-of-bottom-of-a-div-with-jquery
		contentBottom = 170 + $content.outerHeight(true),
		sidebarHeight = $sidebar.height(),
		margin;
	if (windowWidth > 950 && scrollTop > 200 && scrollTop < contentBottom - sidebarHeight) {
		if (windowWidth < 1200) {
			margin = 30;
		} else if (windowWidth <= 1600) {
			margin = 120;
		} else {
			margin = 221;
		}
		$sidebar.addClass('sticky');
		$sidebar.css('right', ((windowWidth - containerWidth) / 2 + margin) + 'px');
		$sidebar.css('top', '5%');
	} else if (windowWidth > 950 && scrollTop >= contentBottom - sidebarHeight) {
		$sidebar.removeClass('sticky');
		$sidebar.css('right', 0);
		$sidebar.css('top', contentBottom - sidebarHeight - 200 + 'px');
	} else {
		$sidebar.removeClass('sticky');
		$sidebar.css('right', 'auto');
		$sidebar.css('top', 'auto');
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

// http://stackoverflow.com/questions/7394748/whats-the-right-way-to-decode-a-string-that-has-special-html-entities-in-it?lq=1
function decodeHtml(html) {
	var txt = document.createElement("textarea");
	txt.innerHTML = html;
	return txt.value;
}

// http://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript
function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// http://stackoverflow.com/questions/7171099/how-to-replace-url-parameter-with-javascript-jquery
function replaceUrlParam(url, paramName, paramValue){
	var pattern = new RegExp('\\b('+paramName+'=).*?(&|$)')
	if(url.search(pattern)>=0){
		return url.replace(pattern,'$1' + paramValue + '$2');
	}
	return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue 
}