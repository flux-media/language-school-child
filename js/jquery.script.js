/**
 * @package    WordPress
 * @subpackage    Language School Child
 * @version    0.0.1
 *
 * Theme Custom Scripts
 * Created by leehankyeol
 *
 */

var domain = 'https://avengerschool.com/';
var youShouldAgree = '약관에 동의하셔야 합니다.';
var processingRefund = '환불 처리 중... 잠시만 기다려주세요.';
var successOnRefund = '환불이 정상적으로 처리되었습니다.';

"use strict";

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
function replaceUrlParam(url, paramName, paramValue) {
    var pattern = new RegExp('\\b(' + paramName + '=).*?(&|$)')
    if (url.search(pattern) >= 0) {
        return url.replace(pattern, '$1' + paramValue + '$2');
    }
    return url + (url.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue
}

(function ($) {

    $(document).ready(function () {
        /* Masonry */
        doMasonry();
        // When there're tabs, doMasonry should be called each time a new tab is active.
        $('.cmsmasters_tabs_list_item').on('click', doMasonry);

        /* Pricing */
        var $pricing = $('.cmsmasters_price_wrap'),
            title, price, courseId;
        if ($pricing.length > 0) {
            title = decodeHtml(getURLParameter('title'));
            price = getURLParameter('price');
            courseId = getURLParameter('course_id');
            if (price === 'Free') {
                price = 0;
            }
            if (!title && !price && !courseId) {
                $('.cmsmasters_pricing_table').remove();
            } else {
                $pricing.html('<span class="cmsmasters_price">' + price + '</span><span class="cmsmasters_currency">원</span>');
            }
            $('#avengerschool-course-title').val(title);
            $('#avengerschool-num-to-enroll').val(getURLParameter('num_to_enroll'));
        }

        /* Sticky */
        var $sidebar = $('.cmsmasters_course_sidebar'), width;
        if ($sidebar.length > 0) {
            doSticky();
            $(window).resize(doSticky);
            $(window).scroll(doSticky);
        }

        /* Form */
        $('.wpcf7').on('wpcf7:submit', function (event) {
            if (window.location.href.indexOf('register-for-courses') > -1) {
                // Send
                $.ajax({
                    method: 'POST',
                    url: domain + 'wp-admin/admin-ajax.php',
                    data: {
                        action: 'send_registration_feedback',
                        amount: $('.cmsmasters_price').text(),
                        course_title: $('#avengerschool-course-title').val(),
                        tel: $('input[name=your-phone-number]').val(),
                        email: $('input[name=your-email]').val(),
                        name: $('input[name=your-name]').val()
                    }
                });
            }
        });

        /* Agree? */
        $('form.register').on('submit', function (e) {
            var $agree = $('#agree');
            if ($agree.is(':checked')) {
                return true;
            } else {
                alert(youShouldAgree);
                $agree.focus();
                return false;
            }
        });

        /* Agree on register-for-course? */
        $('#agree-on-register-for-courses').find('.wpcf7-list-item-label')
            .html('본인은 <a href="https://avengerschool.com/terms/" target="_blank">이용약관</a>을 읽고 수락합니다');

        /* Number of students event listener */
        var $numberOfStudents = $('#number-of-students'),
            $beforeOriginalPrice = $('#before-original-price'),
            $originalPrice = $('#original-price'),
            $registerCourse = $('#register-course'),
            numberOfStudents, beforeOriginalPrice, originalPrice, href;

        if ($beforeOriginalPrice.length > 0) {
            beforeOriginalPrice = parseInt($beforeOriginalPrice.text().replace(',', ''), 10);
        }
        originalPrice = parseInt($originalPrice.text().replace(',', ''), 10);
        href = $registerCourse.attr('href');
        numberOfStudents = parseInt($numberOfStudents.val(), 10);

        $numberOfStudents.change(function () {
            numberOfStudents = parseInt($numberOfStudents.val(), 10);

            // Update texts.
            if ($beforeOriginalPrice.length > 0) {
                $beforeOriginalPrice.text(numberWithCommas(numberOfStudents * beforeOriginalPrice));
            }
            $originalPrice.text(numberWithCommas(numberOfStudents * originalPrice));

            // Woocommerce
            $('.add_to_cart_button').data('quantity', numberOfStudents);
            $('.wc_quick_buy_form').find('input[name=quantity]').val(numberOfStudents);

            // Update href to registration for the course.
            if ($registerCourse.length) {
                var newHref = replaceUrlParam(href, 'price', numberOfStudents * originalPrice);
                newHref = replaceUrlParam(newHref, 'num_to_enroll', numberOfStudents);
                $registerCourse.prop('href', newHref);
            }
        });

        /* Assigning hashtags to main tabs */
        var $mainTabContainer = $('.cmsmasters_tabs.main-woocommerce'),
            hashValue = window.location.hash.substr(1),
            slugInKorean = '';
        if ($mainTabContainer.length > 0) {
            // Update hash.
            $mainTabContainer.find('li>a').on('click', function (e) {
                var $this = $(this), innerText = $.trim($this.text()), slug = '';
                switch (innerText) {
                    case '전체':
                        slug = '';
                        break;
                    case '경제':
                        slug = 'economy';
                        break;
                    case '글쓰기':
                        slug = 'writing';
                        break;
                    case '마케팅':
                        slug = 'marketing';
                        break;
                    case '문화기획':
                        slug = 'culture-planning';
                        break;
                    case '미디어':
                        slug = 'media';
                        break;
                    case '병원':
                        slug = 'hospital-management';
                        break;
                    case '비즈니스':
                        slug = 'business';
                        break;
                    case '콘텐츠 마케팅':
                        slug = 'contents';
                        break;
                    case '패키지':
                        slug = 'package';
                        break;
                    case '헬조선':
                        slug = 'hell-chosun';
                        break;
                    case 'SEO':
                        slug = 'seo';
                        break;
                }
                if (slug) {
                    parent.location.hash = '#' + slug;
                } else {
                    return true;
                }
            });

            // Set a tab according to hash.
            if (hashValue != '') {
                switch (hashValue) {
                    case 'business':
                        slugInKorean = '비즈니스';
                        break;
                    case 'contents':
                        slugInKorean = '컨텐츠 마케팅';
                        break;
                    case 'culture-planning':
                        slugInKorean = '문화기획';
                        break;
                    case 'economy':
                        slugInKorean = '경제';
                        break;
                    case 'hell-chosun':
                        slugInKorean = '헬조선';
                        break;
                    case 'hospital-management':
                        slugInKorean = '병원';
                        break;
                    case 'marketing':
                        slugInKorean = '마케팅';
                        break;
                    case 'media':
                        slugInKorean = '미디어';
                        break;
                    case 'seo':
                        slugInKorean = 'SEO';
                        break;
                    case 'package':
                        slugInKorean = '패키지';
                        break;
                    case 'writing':
                        slugInKorean = '글쓰기';
                        break;

                }
                $mainTabContainer.find('li>a').each(function () {
                    var $this = $(this), innerText = $.trim($this.text());
                    if (innerText == slugInKorean) {
                        // From language-school's jquery.script.js onClick
                        var tabs_parent = $this.parents('.cmsmasters_tabs'),
                            tabs = tabs_parent.find('.cmsmasters_tabs_wrap'),
                            index = $this.parents('li').index();

                        tabs_parent.find('.cmsmasters_tabs_list > .current_tab').removeClass('current_tab');
                        $this.parents('li').addClass('current_tab');
                        tabs.find('.cmsmasters_tab').not(':eq(' + index + ')').slideUp('fast', function () {
                            $this.removeClass('active_tab');
                        });
                        tabs.find('.cmsmasters_tab:eq(' + index + ')').slideDown('fast', function () {
                            $this.addClass('active_tab');
                        });
                        return false;
                    }
                });
            }
        }

        /* Refund by customers */
        $('.button-product-refund').on('click', function (e) {
            e.preventDefault();

            var $this = $(this), orderId = $this.data('order-id'),
                $processing = $('<span/>');
            $processing.text(processingRefund);

            $this.parent().append($processing);
            $this.hide();

            $.ajax({
                method: 'POST',
                url: domain + 'wp-admin/admin-ajax.php',
                data: {
                    action: 'refund_order',
                    order_id: orderId
                },

                success: function (resp) {
                    if (resp.success == true) {
                        alert(successOnRefund);
                        document.location.reload(true);
                    } else {
                        alert(resp.data.error);
                    }
                }
            });
        });
    });

    function doSticky() {
        var $sidebar = $('.cmsmasters_course_sidebar'),
            $window = $(window),
            $content = $('.cmsmasters_course_content'),
            windowWidth = $window.width(),
            scrollTop = $window.scrollTop(),
            containerWidth = $('.middle_inner').width(),
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
        var $shortCode = $('.cmsmasters_learnpress_shortcode').masonry({
            itemSelector: '.lpr_course_post'
        });
        $shortCode.imagesLoaded().progress(function () {
            $shortCode.masonry('layout');
        });
    }
})(jQuery);