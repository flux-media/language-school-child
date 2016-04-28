<?php
/**
 * @package 	WordPress
 * @subpackage 	Language School Child
 * @version		0.0.1
 * 
 * Main Theme Functions File
 * Created by leehankyeol
 * 
 */

add_action('after_setup_theme', 'child_theme_setup');
function child_theme_setup() {
    load_child_theme_textdomain('language-school-child', get_stylesheet_directory().'/');
}

// https://docs.woothemes.com/document/third-party-custom-theme-compatibility/
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts');
function theme_enqueue_scripts() {
	wp_enqueue_script('masonry');
	wp_enqueue_script('imagesLoaded', get_stylesheet_directory_uri(). '/js/imagesloaded.pkgd.min.js', array('masonry'), false, true);
	wp_enqueue_script('jquery-ui', get_stylesheet_directory_uri() . '/js/jquery-ui.min.js', array('jquery'), false, true);
	wp_enqueue_script('child_script', get_stylesheet_directory_uri() . '/js/jquery.script.js', array('jquery'), false, true);
}

add_action('after_setup_theme', 'my_child_shortcodes_setup');
function my_child_shortcodes_setup() {
	remove_shortcode('cmsmasters_learnpress');
	add_shortcode('cmsmasters_learnpress', 'my_cmsmasters_learnpress');
	add_shortcode('woocommerce_learnpress', 'my_woocommerce_learnpress');
}

add_action('init', 'wpcodex_add_author_support_to_product');
function wpcodex_add_author_support_to_product() {
	add_post_type_support('product', 'author');
}

add_action( 'add_meta_boxes', 'add_as_metaboxes' );
// Add the custom meta boxes.
function add_as_metaboxes() {
	add_meta_box('as_location', '코스 장소', 'as_location', 'lpr_course', 'normal', 'high');
	add_meta_box('as_duration', '코스 시간(X시간)', 'as_duration', 'product', 'normal', 'high');
	add_meta_box('as_location', '코스 장소', 'as_location', 'product', 'normal', 'high');
	add_meta_box('as_date', '코스 날짜(YYYY.MM.DD PM H:mm)', 'as_date', 'product', 'normal', 'high');
	add_meta_box('as_max_number_of_students', '최대 수강 인원', 'as_max_number_of_students', 'product', 'normal', 'high');
	add_meta_box('as_display_thumbnail', '특성 이미지 본문 표시 여부(true, false)', 'as_display_thumbnail', 'product', 'normal', 'high');
}
// The meta boxes
function as_duration() {
	global $post;
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	$duration = get_post_meta($post->ID, 'as_duration', true);
	echo '<input type="text" name="as_duration" value="' . htmlspecialchars($duration)  . '" class="widefat" />';	
}
function as_location() {
	global $post;
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	// Get the location data if its already been entered
	$location = get_post_meta($post->ID, 'as_location', true);
	// Echo out the field
	echo '<input type="text" name="as_location" value="' . htmlspecialchars($location)  . '" class="widefat" />';
}
function as_date() {
	global $post;
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	$date = get_post_meta($post->ID, 'as_date', true);
	echo '<input type="text" name="as_date" value="' . htmlspecialchars($date)  . '" class="widefat" />';	
}
function as_max_number_of_students() {
	global $post;
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	$max_number = get_post_meta($post->ID, 'as_max_number_of_students', true);
	echo '<input type="text" name="as_max_number_of_students" value="' . htmlspecialchars($max_number)  . '" class="widefat" />';	
}
function as_display_thumbnail() {
	global $post;
	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	$display_thumbnail = get_post_meta($post->ID, 'as_display_thumbnail', true);
	echo '<input type="text" name="as_display_thumbnail" value="' . htmlspecialchars($display_thumbnail)  . '" class="widefat" />';	
}
// Save the Metabox Data
function wpt_save_as_meta($post_id, $post) {
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}
	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	$events_meta['as_location'] = $_POST['as_location'];
	$events_meta['as_duration'] = $_POST['as_duration'];
	$events_meta['as_date'] = $_POST['as_date'];
	$events_meta['as_max_number_of_students'] = $_POST['as_max_number_of_students'];
	$events_meta['as_display_thumbnail'] = $_POST['as_display_thumbnail'];
	// Add values of $events_meta as custom fields
	foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}
}
add_action('save_post', 'wpt_save_as_meta', 1, 2); // save the custom fields

// Change wp_mail sender.
apply_filters('wp_mail_from', 'get_admin_email');
apply_filters('wp_mail_from_name', 'get_site_name');
function get_admin_email($email) {
	return get_option('admin_email');
}
function get_site_name($name) {
	return '어벤져스쿨';
}

// Iamport success callback (old)
add_action('wp_ajax_send_registration_feedback', 'send_registration_feedback');
add_action('wp_ajax_nopriv_send_registration_feedback', 'send_registration_feedback');
// Iamport success callback (new)
add_action('woocommerce_order_status_completed', 'on_order_complete');

function on_order_complete( $order_id ) {
	$admin_email = get_option('admin_email');

	$order = new WC_Order( $order_id );

	// Initialize SMS module.
	include(dirname(__FILE__) . '/api.class.php');
	$api = new gabiaSmsApi();
	$admin_tel = $api->getAdminTel();

	$merchant_uid = get_post_meta( $order_id, '_transaction_id', true);
	$email = get_post_meta( $order_id, '_billing_email', true); 
	$name = get_post_meta( $order_id, '_billing_last_name', true);
	$tel = get_post_meta( $order_id, '_billing_phone', true); 
	$amount = get_post_meta( $order_id, '_order_total', true);
	$course_titles = '';
	foreach($order->get_items() as $item) {
		$course_titles .= '<' . $item['name'] . '> ' ;
	}
	$message = $course_titles . '강연 입금 및 등록이 완료되었습니다. 감사합니다. - 어벤져스쿨';
	if (mb_strlen($message, 'UTF-8') > 45) {
		$result = $api->lms_send($tel, $admin_tel, $message);
	} else {
		$result = $api->sms_send($tel, $admin_tel, $message);
	}
	if ($result == gabiaSmsApi::$RESULT_OK) {
	} else {
		wp_mail($admin_email, '[어벤져스쿨] 문자 전송 실패. (아임포트)',
			'Merchant Uid: ' . $merchant_uid . "\n" .
			$api->getResultCode() . " : " . $api->getResultMessage() . "\n" .
			'Order ID: ' . $order_id );
	}
}

function send_registration_feedback() {
	// Get parameters.
	$admin_email = get_option('admin_email');
	$merchant_uid = $_POST['merchant_uid'];

	// Initialize SMS module.
	include(dirname(__FILE__) . '/api.class.php');
	$api = new gabiaSmsApi();
	$admin_tel = $api->getAdminTel();

	// From Iamport.
	if ($merchant_uid) {
		// TODO: Double-check the price of order.

		$status = $_POST['status']; // Either 'success' or 'failure'.
		$thankyou_url = $_POST['thankyou_url'];

		// Get contacts by merchant_uid.
		$args = array(
			'post_type' => 'iamport_payment',
			'meta_query' => array(
				array(
					'key' => 'order_uid',
					'value' => $merchant_uid,
				)
			)
		);
		$posts_list = get_posts($args);
		if ($posts_list) {
			$post_id = $posts_list[0]->ID;
			$email = get_post_meta($post_id, 'buyer_email', true);
			$tel = get_post_meta($post_id, 'buyer_tel', true);
			$name = get_post_meta($post_id, 'buyer_name', true);
			$amount = get_post_meta($post_id, 'order_amount', true);
			$course_title = get_the_title($post_id);
		} else {
			// TODO: Why no postious?
			wp_mail($admin_email, '[어벤져스쿨] merchant_uid가 존재하지 않음.', '이유는 모름. 아임포트에게 연락해봐야 함. Merchant Uid: ' . $merchant_uid);
			return;
		}

		// Send e-mail and SMS.
		if ($status == 'success') {
			wp_mail($email, '[어벤져스쿨] 성공적으로 강연 입금 및 등록이 완료되었습니다.',
				'결제 번호: ' . $merchant_uid .  "\n" .
				'강의 제목: ' . $course_title . "\n" .
				'수강자 이름: ' . $name .  "\n" .
				'결제 금액: ' . $amount .  "\n" . 
				'감사합니다.' . "\n" .
				'- 어벤져스쿨.' . "\n");
			$message = '<' . $course_title . '> 강연 입금 및 등록이 완료되었습니다. 감사합니다. - 어벤져스쿨';
			if (mb_strlen($message, 'UTF-8') > 45) {
				$result = $api->lms_send($tel, $admin_tel, $message);
			} else {
				$result = $api->sms_send($tel, $admin_tel, $message);
			}
			if ($result == gabiaSmsApi::$RESULT_OK) {
			} else {
				wp_mail($admin_email, '[어벤져스쿨] 문자 전송 실패. (아임포트)', 'Merchant Uid: ' . $merchant_uid . "\n" .
					$api->getResultCode() . " : " . $api->getResultMessage() . ', Payment ID: ' . $post_id );
			}
		} else if ($status == 'failure') {
			wp_mail($email, '[어벤져스쿨] 결제에 실패했습니다.',
				'결제 번호: ' . $merchant_uid .  "\n" .
				'강의 제목: ' . get_post_title($post_id) .  "\n" .
				'- 어벤져스쿨.' . "\n");
			wp_mail($admin_email, '[어벤져스쿨] 결제에 실패했습니다.',
				'결제 번호: ' . $merchant_uid .  "\n" .
				'강의 제목: ' . get_post_title($post_id) .  "\n" .
				'- 어벤져스쿨.' . "\n");
		}
	}
	// From contact form.
	else {
		$email = $_POST['email'];
		$tel = $_POST['tel'];
		$course_title = $_POST['course_title'];
		$name = $_POST['name'];
		$amount = $_POST['amount'];

		wp_mail($email, '[어벤져스쿨] 성공적으로 강연 등록이 완료되었습니다.',
			'강의 제목: ' . $course_title . "\n" .
			'수강자 이름: ' . $name .  "\n" .
			'결제 금액: ' . $amount .  "\n" . 
			$api->getAdminAccount() . "\n" .
			'입급 전 정원 초과시 자동 취소되오니 빠른 결제 부탁드립니다.' . "\n" . 
			'- 어벤져스쿨.');
		$message = '<' . $course_title . '> 강연 등록이 완료되었습니다. 입급 전 정원 초과시 자동 취소되오니 빠른 결제 부탁드립니다. - 어벤져스쿨';
		$result = $api->lms_send($tel, $admin_tel, $message);
		if ($result == gabiaSmsApi::$RESULT_OK) {
			echo($p . " : " . $api->getResultMessage() . "<br>");
			echo("이전 : " . $api->getBefore() . "<br>");
			echo("이후 : " . $api->getAfter() . "<br>");
		} else {
			echo("error : " . $p . " - " . $api->getResultCode() . " - " . $api->getResultMessage() . "<br>");
			wp_mail($admin_email, '[어벤져스쿨] 문자 전송 실패. (무통장입금)',
					$api->getResultCode() . " : " . $api->getResultMessage()  . ', Payment ID: ' . $post_id);
		}
	}
}

/**
 * Hook: Empty cart before adding a new product to cart WITHOUT throwing woocommerce_cart_is_empty
 * https://wordpress.org/support/topic/how-to-empty-cart-before-adding-the-new-product-to-the-cart-in-woocommerce-1
 */
add_action ('woocommerce_add_to_cart', 'woocommerce_empty_cart_before_add', 0);
function woocommerce_empty_cart_before_add() {
    global $woocommerce;

    // Get 'product_id' and 'quantity' for the current woocommerce_add_to_cart operation
    if (isset($_GET["add-to-cart"])) {
        $prodId = (int)$_GET["add-to-cart"];
    } else if (isset($_POST["add-to-cart"])) {
        $prodId = (int)$_POST["add-to-cart"];
    } else {
        $prodId = null;
    }
    if (isset($_GET["quantity"])) {
        $prodQty = (int)$_GET["quantity"] ;
    } else if (isset($_POST["quantity"])) {
        $prodQty = (int)$_POST["quantity"];
    } else {
        $prodQty = 1;
    }

    // If cart is empty
    if ($woocommerce->cart->get_cart_contents_count() == 0) {

        // Simply add the product (nothing to do here)

    // If cart is NOT empty
    } else {

        $cartQty = $woocommerce->cart->get_cart_item_quantities();
        $cartItems = $woocommerce->cart->cart_contents;

        // Check if desired product is in cart already
        if (array_key_exists($prodId,$cartQty)) {

            // Then first adjust its quantity
            foreach ($cartItems as $k => $v) {
                if ($cartItems[$k]['product_id'] == $prodId) {
                    $woocommerce->cart->set_quantity($k,$prodQty);
                }
            }

            // And only after that, set other products to zero quantity
            foreach ($cartItems as $k => $v) {
                if ($cartItems[$k]['product_id'] != $prodId) {
                    $woocommerce->cart->set_quantity($k,'0');
                }
            }
        }
    }
}

// http://www.wpbeginner.com/wp-themes/how-to-show-different-menus-to-logged-in-users-in-wordpress/
function my_wp_nav_menu_args( $args = '' ) {
	if( is_user_logged_in() ) { 
		$args['menu'] = 'logged-in';
	} else { 
		$args['menu'] = 'logged-out';
	} 
	return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

// TODO: Get rid of it.
function my_cmsmasters_learnpress($atts, $content = null) {
	$new_atts = apply_filters('cmsmasters_learnpress_atts_filter', array( 
		'orderby' => 		'', 
		'order' => 			'', 
		'categories' => 	'', 
		'count' => 			'', 
		'columns' => 		'', 
		'classes' => 		'' 
	));
	
	$shortcode_name = 'learnpress';
	$shortcode_path = CMSMASTERS_CONTENT_COMPOSER_TEMPLATE_DIR . '/cmsmasters-' . $shortcode_name . '.php';
	
	if (locate_template($shortcode_path)) {
		$template_out = cmsmasters_composer_load_template($shortcode_path, array( 
			'atts' => 		$atts, 
			'new_atts' => 	$new_atts, 
			'content' => 	$content 
		) );	
		return $template_out;
	}
	
	extract(shortcode_atts($new_atts, $atts));

	$unique_id = uniqid();
	
	if ($columns == '4' || $columns == '5') {
		$course_thumb = 'cmsmasters-square-thumb';
	} else {
		$course_thumb = 'cmsmasters-project-thumb';
	}
		
	$out = '<div id="cmsmasters_learnpress_shortcode_' . $unique_id . '" class="cmsmasters_learnpress_shortcode' . 
	(($columns != '') ? ' cmsmasters_' . $columns : '') . 
	(($classes != '') ? ' ' . $classes : '') . 
	'">';
	
	$args = array( 
		'post_type' => 				'lpr_course', 
		'orderby' => 				$orderby, 
		'order' => 					$order, 
		'posts_per_page' => 		$count 
	);
	
	if ($categories != '') {
		$cat_array = explode(",", $categories);
		
		$args['tax_query'] = array( 
			array( 
				'taxonomy' => 'course_category', 
				'field' => 'slug', 
				'terms' => $cat_array 
			)
		);
	}
	
	$query = new WP_Query($args);
	
	
	if ($query->have_posts()) : 
		while ($query->have_posts()) : $query->the_post();
	
		$course_id = get_the_ID();
		
		$course_duration = get_post_meta( $course_id, '_lpr_course_duration', true );
		$term_list = get_the_term_list( $course_id, 'course_category', '', ', ', '' );
		$cmsmasters_title = cmsmasters_child_title($course_id, false);
		$posttags = get_the_term_list( $course_id, 'course_tag', '', ', ', '' );

		$out .= "<article class=\"lpr_course_post\">" . "\n" .
			 "<a href=" . get_the_permalink( $course_id ) . ">" .
			  "<img class=\"full-width\" src=\"" . wp_get_attachment_image_url(get_post_thumbnail_id($course_id), 'medium') .
			  "\" title=\"" . $cmsmasters_title . "\" alt=\"" . $cmsmasters_title . "\" " . wp_get_attachment_image_srcset(get_post_thumbnail_id($course_id)) . "/>" . "\n" .
			"<div class=\"lpr_course_inner\">" . "\n" . 
			"<header class=\"entry-header lpr_course_header\">
			<h6 class=\"entry-title lpr_course_title\"><a href=" . get_the_permalink( $course_id ) . ">" . get_the_title( $course_id ) . "</a></h6>
			</header>" . "\n";

		$out .= "<div><div class=\"lpr_course_author\">" . get_the_author_meta('nickname') . "</div>";
		if ($posttags != '') {
			$out .= "<div class=\"lpr_course_date\">" . $posttags . "</div>";
		} else {
			$out .= "<div class=\"lpr_course_date\">날짜 미정</div>";
		}
		$out .= "<div class=\"lpr_course_subtitle\">" . nl2br(get_the_excerpt( $course_id )) . "</div>";
		$out .= "</div>";

		$sale_price = get_post_meta($course_id, '_lpr_course_duration', true);
			
		if ( !learn_press_is_free_course( $course_id ) ) {
			$out .= "<div class=\"cmsmasters_course_price\">" . learn_press_get_currency_symbol() . number_format(floatval( get_post_meta( $course_id, '_lpr_course_price', true ) ) ) . "</div>";
			if ($sale_price) {
				$out .= "<div class=\"cmsmasters_course_price original_price\"><span class=\"line-through\">₩". number_format($sale_price) . "</span> →</div>";
			}
		} else {
			$out .= "<div class=\"cmsmasters_course_free\">" . esc_html__('Free', 'language-school') . "</div>";
		}

		if ($term_list != '') {
			$out .= "<div class=\"entry-meta cmsmasters_cource_cat\">" . $term_list . "</div>";
		}
		
		// Removed rate.
		$out .= "</div>" . "\n";
		
		// Removed footer.	
		$out .= "</article>" . "\n";
	
		endwhile;
	endif;
	
	
	wp_reset_postdata();
	
	wp_reset_query();
	
	
	$out .= '</div>';
	
	
	return $out;
}

function my_woocommerce_learnpress($atts, $content = null) {
	$new_atts = apply_filters('cmsmasters_learnpress_atts_filter', array( 
		'orderby' => 		'', 
		'order' => 			'', 
		'categories' => 	'', 
		'count' => 			'', 
		'columns' => 		'', 
		'classes' => 		'' 
	) );
	
	
	$shortcode_name = 'learnpress';
	
	$shortcode_path = CMSMASTERS_CONTENT_COMPOSER_TEMPLATE_DIR . '/cmsmasters-' . $shortcode_name . '.php';
	
	
	if (locate_template($shortcode_path)) {
		$template_out = cmsmasters_composer_load_template($shortcode_path, array( 
			'atts' => 		$atts, 
			'new_atts' => 	$new_atts, 
			'content' => 	$content 
		) );
		
		
		return $template_out;
	}
	
	
	extract(shortcode_atts($new_atts, $atts));
	
	
	$unique_id = uniqid();
	
	if ($columns == '4' || $columns == '5') {
		$course_thumb = 'cmsmasters-square-thumb';
	} else {
		$course_thumb = 'cmsmasters-project-thumb';
	}
	
	
	$out = '<div id="cmsmasters_learnpress_shortcode_' . $unique_id . '" class="cmsmasters_learnpress_shortcode' . 
	(($columns != '') ? ' cmsmasters_' . $columns : '') . 
	(($classes != '') ? ' ' . $classes : '') . 
	'">';
	
	
	$args = array( 
		'post_type' => 				'product', 
		'orderby' => 				'date', 
		'order' => 					$order, 
		'posts_per_page' => 		$count 
	);
	
	if ($categories != '') {
		$cat_array = explode(",", $categories);
		
		$args['tax_query'] = array( 
			array( 
				'taxonomy' => 'product_cat', 
				'field' => 'slug', 
				'terms' => $cat_array 
			)
		);
	}
	
	$query = new WP_Query($args);
	
	if ($query->have_posts()) : 
		while ($query->have_posts()) : $query->the_post();
	
		$product_id = get_the_ID();
		$product = new WC_Product( $product_id );
		
		$categories = get_the_term_list( $product_id, 'product_cat', '', ', ', '' );
		$cmsmasters_title = cmsmasters_child_title( $product_id, false );

		$out .= "<article class=\"lpr_course_post\">" . "\n" .
			 "<a href=" . get_the_permalink( $product_id ) . ">" .
			  "<img class=\"full-width heyho\" src=\"" . wp_get_attachment_image_url(get_post_thumbnail_id($product_id), 'medium') .
			  "\" title=\"" . $cmsmasters_title . "\" alt=\"" . $cmsmasters_title . "\" " . wp_get_attachment_image_srcset(get_post_thumbnail_id($product_id)) . "/>" . "\n" .
			"<div class=\"lpr_course_inner\">" . "\n" . 
			"<header class=\"entry-header lpr_course_header\">
			<h6 class=\"entry-title lpr_course_title\"><a href=" . get_the_permalink( $product_id ) . ">" . get_the_title( $product_id ) . "</a></h6>
			</header>" . "\n";

		$out .= "<div><div class=\"lpr_course_author\">" . get_the_author_meta('nickname') . "</div>";

		$date = get_post_meta( $product_id, 'as_date', true );
		if ($date != '') {
			$out .= "<div class=\"lpr_course_date\">" . $date . "</div>";
		} else {
			$out .= "<div class=\"lpr_course_date\">날짜 미정</div>";
		}
		$out .= "<div class=\"lpr_course_subtitle\">" . nl2br(get_the_excerpt( $product_id )) . "</div>";
		$out .= "</div>";

		// 2016.04.30 PM 7:30 -> 2016.04.30 7:30 PM
		$thedate = $date;
		$thedate = preg_replace("/([0-9.]+) ([ap]m) ([0-9:]+)/i", "$1 $3 $2", $thedate);
		$start_at = DateTime::createFromFormat('Y.m.d g:i A', $thedate, new DateTimeZone('Asia/Seoul'));
		$now = new DateTime('now', new DateTimeZone('Asia/Seoul'));
		$is_past = false;
		if ($start_at === false) {
			// Let $is_past be false.
		} else {
			$interval_in_sec = $start_at->getTimeStamp() - $now->getTimeStamp();
			// TODO: 7200?
			if ($interval_in_sec < 7200) {
				$is_past = true;
			}
		}

		$regular_price = get_post_meta( $product_id, '_regular_price', true );
		$sale_price = get_post_meta( $product_id, '_sale_price', true );
		$on_sale = $sale_price != '';

		// Product bundles don't have the exact datetime format.
		if ($product->product_type == 'bundle') {
			if ($product->stock_status == 'instock') {
				if ($regular_price != 0) {
					$out .= '<div class="cmsmasters_course_price">₩' . number_format($product->get_price_including_tax(1, get_post_meta($product_id, '_price', true))) . '</div>';
					if ($on_sale) {
						$out .= '<div class="cmsmasters_course_price original_price"><span class="line-through">₩'. number_format($product->get_price_including_tax(1, $regular_price)) . "</span> →</div>";
					}
				} else {
					$out .= '<div class=\"cmsmasters_course_free\">무료</div>';
				}
			} else {
				$out .= '<div class="cmsmasters_course_free">마감</div>';
			}
		} else {
			if ($is_past) {
				$out .= '<div class="cmsmasters_course_free">완료</div>';
			} else {
				if ($product->stock > 0) {
					if ($regular_price != 0) {
						$out .= '<div class="cmsmasters_course_price">₩' . number_format($product->get_price_including_tax(1, get_post_meta($product_id, '_price', true))) . '</div>';
						if ($on_sale) {
							$out .= '<div class="cmsmasters_course_price original_price"><span class="line-through">₩'. number_format($product->get_price_including_tax(1, $regular_price)) . "</span> →</div>";
						}
					} else {
						$out .= '<div class=\"cmsmasters_course_free\">무료</div>';
					}
				} else {
					$out .= '<div class="cmsmasters_course_free">마감</div>';
				}
			}
		}

		if ($categories != '') {
			$out .= '<div class="entry-meta cmsmasters_cource_cat">' . $categories . "</div>";
		}
		
		// Removed rate.
		$out .= '</div>' . "\n";
		
		// Removed footer.	
		$out .= '</article>' . "\n";
	
		endwhile;
	endif;
	
	wp_reset_postdata();
	wp_reset_query();
	
	$out .= '</div>';
	
	return $out;
}

/* Get Title Function */
function cmsmasters_child_title($cmsmasters_id, $show = true) { 
	$cmsmasters_heading = get_post_meta($cmsmasters_id, 'cmsmasters_heading', true);
	
	$cmsmasters_heading_title = get_post_meta($cmsmasters_id, 'cmsmasters_heading_title', true);
	
	$out = '';
	
	if ($cmsmasters_heading == 'custom' && $cmsmasters_heading_title != '') {
		$out .= esc_attr($cmsmasters_heading_title);
	} else {
		$out .= esc_attr(strip_tags(get_the_title($cmsmasters_id) ? get_the_title($cmsmasters_id) : $cmsmasters_id));
	} 
    
	
    if ($show) {
        echo $out;
    } else {
        return $out;
    }
}

?>