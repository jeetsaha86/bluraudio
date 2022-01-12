<?php
/*This file is part of PinkFader, yootheme child theme.

All functions of this file will be loaded before of parent theme functions.
Learn more at https://codex.wordpress.org/Child_Themes.

Note: this function loads the parent stylesheet before, then child theme stylesheet
(leave it in place unless you know what you are doing.)
*/

// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';

add_action( 'wp_enqueue_scripts', function () {
	#CSS
	wp_enqueue_style( 'theme-style', get_stylesheet_directory_uri() . '/assets/styles/css/theme.css', array(), filemtime( get_stylesheet_directory() . '/assets/styles/css/theme.css' ), false );

	#JS
	wp_enqueue_script( 'theme-favorite', get_stylesheet_directory_uri() . '/assets/scripts/favorite.js', false, '1.0.0', true );
	wp_enqueue_script( 'theme-cart', get_stylesheet_directory_uri() . '/assets/scripts/add-to-cart.js', false, '1.0.0', true );
	wp_enqueue_script( 'theme-audio', get_stylesheet_directory_uri() . '/assets/scripts/audio.js', false, '1.0.0', true );
	wp_enqueue_script( 'theme-script', get_stylesheet_directory_uri() . '/assets/scripts/theme.js', false, '1.0.0', true );

	# LOCALIZE
	wp_localize_script( 'theme-script', 'pinkFader', [
		'ajaxURL' => admin_url( 'admin-ajax.php' ),
	] );
} );

/*
 * Remove <p> Tag From Contact Form 7
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/*
 * The WordPress Core woocommerce register post type product hook
 * Modify rest_base slug to ensure it displays as YooTheme source
 */
add_filter( 'woocommerce_register_post_type_product', function ( $args ) {
	// insert the slug to rest_base argument
	$args['rest_base']          = 'products';
	$args['publicly_queryable'] = false;

	// return the arguments
	return $args;
} );

/*
 * Remove Dashboard Tabs from My Account menu
 */
add_filter( 'woocommerce_account_menu_items', function ( $menu_links ) {
	unset( $menu_links['dashboard'] );

	// return menu items
	return $menu_links;
} );

/*
 * Remove product permalink on cart
 */
add_filter( 'woocommerce_cart_item_permalink', function ( $permalink, $cart_item, $cart_item_key ) {
	return '';
}, 10, 3 );

/*
 * Remove product permalink on orders
 */
add_filter( 'woocommerce_order_item_permalink', function ( $permalink, $item, $order ) {
	return '';
}, 10, 3 );

/*
 * Remove product url from the downloads array
 */
add_filter( 'woocommerce_order_get_downloadable_items', function ( $downloads, $instance ) {
	array_walk_recursive( $downloads, function ( &$value, $key ) {
		$value = $key == 'product_url' ? '' : $value;
	} );

	// return the array
	return $downloads;
}, 10, 2 );

/*
 * Detect the Dashboard Page and Redirect to the Orders
 */
add_action( 'template_redirect', function () {
	if ( is_account_page() && is_user_logged_in() && empty( WC()->query->get_current_endpoint() ) ) {
		wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
		exit;
	}
} );

/*
 * Add extra class to body when on woocommerce account page
 * and the user is not logged in
 */
add_filter( 'body_class', function ( $classes ) {
	if ( is_account_page() && ! is_user_logged_in() && empty( WC()->query->get_current_endpoint() ) ) {
		$classes[] = 'woocommerce-login';
	}

	// return the $classes array
	return $classes;
} );

/*
 * Update the ACF field with Post ID when saving the post
 */
add_filter( 'acf/save_post', function ( $post_id ) {

	// if this is not a product post type then do nothing
	// and return the post id
	if ( 'product' !== get_post_type( $post_id ) ) {
		return $post_id;
	}

	$field_value = $post_id;

	update_field( 'pf_product_id', $field_value, $post_id );

	// return the post id
	return $post_id;
}, 20 );

/**
 * Show cart contents / total Ajax
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	// start the output buffer
	ob_start();

	// load the markup partial
	get_template_part( '/template-parts/navigation/element-cart', 'icon' );

	// add the template code to the fragment
	$fragments['a.cart-contents'] = ob_get_clean();

	// return the cart fragment
	return $fragments;
} );

/*
 * Filter posts for FacetWP to only show favorites
 */
add_filter( 'facetwp_pre_filtered_post_ids', function ( $post_ids, $class ) {

	// return the original post ids if favorite listing is not active
	if ( ! isset( $_COOKIE['__fav_cookie'] ) || ! $_COOKIE['__fav_cookie'] ) {
		return $post_ids;
	}

	// get user data
	$user_favorites = get_user_favorites();

	// return the original post ids if no favorite posts were found
	if ( empty( $user_favorites ) || ! $user_favorites ) {
		return $post_ids;
	}

	// set post ids to favorite posts only
	$post_ids = $user_favorites;

	// return the post ids
	return $post_ids;
}, 10, 2 );

/*
 * Need to merge the fav items from cookies to user meta on login
 * Note : Will only update is user meta is empty
 */
add_action( 'wp_login', function ( $user_login, $user ) {
	// get logged in user
	$user_id = $user->ID;

	// get user fav meta
	$user_favorites_meta = get_user_meta( $user_id, 'user_favorites', true );

	// fav array
	$user_favorites_from_wp = $user_favorites_meta ? json_decode( $user_favorites_meta, true ) : [];

	if ( empty( $user_favorites_from_wp ) ) {
		// get cookies
		$cookie_value = isset( $_COOKIE['user_favorites'] ) ? $_COOKIE['user_favorites'] : false;

		// fav array
		$user_favorites_from_cookies = $cookie_value ? json_decode( stripcslashes( $cookie_value ), true ) : [];

		if ( ! empty( $user_favorites_from_cookies ) ) {
			// generate JSON data
			$user_favorites = json_encode( array_values( $user_favorites_from_cookies ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT );

			// update user meta
			$status = update_user_meta( $user_id, 'user_favorites', $user_favorites );
		}
	}
}, 10, 2 );


/*
 * Shortcode to add action to the theme navbar
 */
add_shortcode( 'tm_navbar', function () {
	// start the output buffer
	ob_start();

	// run action
	do_action( 'tm_navbar' );

	// Returns the contents of the output buffer as shortcode output
	// and end output buffering
	return ob_get_clean();
} );

/*
 * Hook to display the account icon in navbar
 */
add_action( 'tm_navbar', function () {
	get_template_part( '/template-parts/navigation/element-account', 'icon' );
} );

/*
 * Hook to display the cart icon in navbar
 */
add_action( 'tm_navbar', function () {
	get_template_part( '/template-parts/navigation/element-cart', 'icon' );
} );

/*
 * Hook to display the audio player
 */
add_action( 'wp_footer', function () {
	if ( ! is_checkout() ) {
		get_template_part( '/template-parts/player/element', 'markup' );
	}
} );

/*
 * Continue Shopping Button
 */
add_action( 'woocommerce_proceed_to_checkout', function () {
	echo '<a href="#" class="__open_playlist uk-button uk-button-secondary">' . __( 'Continue Shopping', 'yootheme' ) . '</a>';
} );

/*
 * Change the 'Billing details' checkout label to 'Contact Information'
 */
add_filter( 'gettext', function ( $translated_text, $text, $domain ) {
	if ( $translated_text == 'Billing address' && $domain == 'woocommerce' ) {
		$translated_text = __( 'Billing Details', 'woocommerce' );
	}

	// return
	return $translated_text;
}, 20, 3 );

/*
 * Order License Emails
 */
add_filter( 'woocommerce_email_attachments', function ( $attachments, $status, $order ) {
	// Avoiding errors and problems
	if ( ! is_a( $order, 'WC_Order' ) || ! isset( $status ) ) {
		return $attachments;
	}

	// we only add the file on completed order mails
	$allowed_statuses = array( 'customer_completed_order', 'customer_processing_order' );

	if ( in_array( $status, $allowed_statuses ) ) {
		$upload_dir       = wp_upload_dir();
		$pdf_cache_folder = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'cache';
		$pdf_store_folder = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'store';

		$license_directory_path = get_stylesheet_directory() . '/assets/licenses/';

		// Get the order address
		$order_address_array[] = $order->get_billing_address_1();
		$order_address_array[] = $order->get_billing_address_2();
		$order_address_array[] = $order->get_billing_city();
		$order_address_array[] = $order->get_billing_state();
		$order_address_array[] = $order->get_billing_country() && $order->get_billing_postcode() ? ( WC()->countries->countries[ $order->get_billing_country() ] . ' ' . $order->get_billing_postcode() ) : '';
		$order_address_array   = array_filter( $order_address_array );

		// Order data
		$order_data['name']    = $order->get_formatted_billing_full_name();
		$order_data['date']    = $order->get_date_completed()->date( 'F j, Y, g:i:s A T' );
		$order_data['address'] = join( ', ', $order_address_array );

		// get all products in the order
		$order_items = $order->get_items();

		// loop through all items in the order
		foreach ( $order_items as $item_id => $item ) {
			$product_id = $item->get_product_id();

			$variation_id         = $item->get_variation_id();
			$variation            = wc_get_product( $variation_id );
			$variation_attributes = $variation->get_variation_attributes();

			$order_data['product_name']    = get_the_title( $product_id );
			$order_data['product_license'] = $variation_attributes['attribute_pa_license'];
			$order_data['subtotal']        = wc_price( $item->get_subtotal() );

			$license_template_content = file_get_contents( $license_directory_path . $order_data['product_license'] . '.php' );

			foreach ( $order_data as $order_data_key => $order_data_value ) {
				$license_template_content = str_replace( '{{' . $order_data_key . '}}', $order_data_value, $license_template_content );
			}

			// PDF File data
			$filename        = 'license' . '-' . $order_data['product_license'] . '-' . $order->get_id() . '-' . $item_id . '.pdf';
			$upload_filepath = $pdf_store_folder . DIRECTORY_SEPARATOR . $filename;

			$mpdf = new \Mpdf\Mpdf( [ 'tempDir' => $pdf_cache_folder ] );
			$mpdf->WriteHTML( $license_template_content );
			$mpdf->Output( $upload_filepath, 'F' );

			$attachments[] = $upload_filepath;
		}
	}

	// return the array with attachments
	return $attachments;
}, 10, 3 );

/*
 * Shortcode to display countdown clock
 */
add_shortcode( 'tm_countdown', function ( $atts ) {
	// Helper functions for setting and fetching default attributes
	$atts = shortcode_atts( array(
		'date' => '2020-08-01',
	), $atts );

	// set query var
	set_query_var( 'date', $atts['date'] );

	// start the output buffer
	ob_start();

	// load the player controls markup partial
	get_template_part( '/template-parts/element', 'countdown' );

	// Returns the contents of the output buffer as shortcode output
	// and end output buffering
	return ob_get_clean();
} );

/*
 * Shortcode to display product links and actions
 */
add_shortcode( 'tm_product_actions', function ( $atts ) {
	// Helper functions for setting and fetching default attributes
	$atts = shortcode_atts( array(
		'id'   => false,
		'type' => 'overlay',
	), $atts );

	// bail if no id set
	if ( ! $atts['id'] ) {
		return false;
	}

	// set query var
	set_query_var( 'track_id', $atts['id'] );

	// start the output buffer
	ob_start();

	// load the player controls markup partial
	get_template_part( '/partials/woocommerce/actions/action', $atts['type'] );

	// Returns the contents of the output buffer as shortcode output
	// and end output buffering
	return ob_get_clean();
} );

/*
 * Get user favorites based on user status
 */
if ( ! function_exists( 'get_user_favorites' ) ) {
	function get_user_favorites() {
		// get cookies
		$cookie_value = isset( $_COOKIE['user_favorites'] ) ? $_COOKIE['user_favorites'] : false;

		// fav array
		// return fav array of post ids
		return $cookie_value ? json_decode( stripcslashes( $cookie_value ), true ) : [];
	}
	// add_action( 'wp_head', 'get_user_favorites' );
}

if ( ! function_exists( 'set_user_favorites' ) ) {
	function set_user_favorites( $post_id, $type = 'add' ) {
		// defaults
		$user_favorites = get_user_favorites();

		// remove or set
		if ( $type == 'add' ) {
			// push the post id in the array
			$user_favorites[] = $post_id;
		} elseif ( $type == 'remove' ) {
			$user_favorites = array_diff( $user_favorites, [ $post_id ] );
		}

		// json code the array
		$user_favorites = json_encode( array_values( $user_favorites ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT );

		// set cookies
		// return status
		return setcookie( 'user_favorites', $user_favorites, time() + 60 * 60 * 24 * 30, '/' );
	}
}

/*
 * Get the tracks data by ID
 */
if ( ! function_exists( 'get_track_data' ) ) {
	function get_track_data() {
		// get the passed parameter
		$product_id = intval( $_POST['productID'] );

		// bail if product ID missing
		if ( ! $product_id ) {
			// this is required to terminate immediately and return a proper response
			wp_die();
		}

		// user data
		$user_favorites = get_user_favorites();

		// defaults
		$track_array = [];

		$product_tags             = wc_get_product_terms( $product_id, 'pa_mood' );
		$product_title            = get_the_title( $product_id );
		$product_content          = get_the_content( null, false, $product_id );
		$product_in_user_favorite = in_array( $product_id, $user_favorites );

		if ( ! is_wp_error( $product_tags ) && ! empty( $product_tags ) ) :
			$product_tags = wp_list_pluck( $product_tags, 'name' );
			$product_tags = '<span class="uk-label">#' . implode( '</span> <span class="uk-label">#', $product_tags ) . '</span>';
		endif;

		// product post data
		$product_image          = [];
		$product_image['thumb'] = get_the_post_thumbnail_url( $product_id, 'thumbnail' );
		$product_image['large'] = get_the_post_thumbnail_url( $product_id, 'large' );

		// bail if no mp3 uploaded
		if ( ! $product_preview_audio_id = get_field( 'pf_preview_audio', $product_id ) ) {
			// this is required to terminate immediately and return a proper response
			wp_die();
		}

		$product_preview_audio          = [];
		$product_preview_audio_metadata = wp_get_attachment_metadata( $product_preview_audio_id );

		$product_preview_audio['url']    = wp_get_attachment_url( $product_preview_audio_id );
		$product_preview_audio['bpm']    = $product_preview_audio_metadata['bpm'];
		$product_preview_audio['artist'] = $product_preview_audio_metadata['artist'];
		$product_preview_audio['length'] = $product_preview_audio_metadata['length_formatted'];

		$track_array['id']       = $product_id;
		$track_array['tags']     = $product_tags;
		$track_array['title']    = $product_title;
		$track_array['track']    = $product_preview_audio;
		$track_array['artwork']  = $product_image;
		$track_array['content']  = apply_filters( 'the_content', $product_content );
		$track_array['favorite'] = $product_in_user_favorite;

		// return the tracks in json format
		wp_send_json( $track_array );
	}

	add_action( 'wp_ajax_get_track_data', 'get_track_data' );
	add_action( 'wp_ajax_nopriv_get_track_data', 'get_track_data' );
}

if ( ! function_exists( 'add_variation_to_cart' ) ) {
	function add_variation_to_cart() {
		// get woo data
		global $woocommerce;

		// defaults
		$in_cart    = false;
		$product_id = intval( $_POST['id'] );

		// get the product object
		$product = wc_get_product( $product_id );

		// get product variation id
		$product_variation_ids = $product->get_children();

		// bail if no variations found
		if ( empty( $product_variation_ids ) ) {
			echo false;
			wp_die();
		}

		// check if in cart
		foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
			if ( $cart_item['product_id'] === $product_id ) {
				$in_cart = true;
				break;
			} else {
				$in_cart = false;
			}
		}

		// the call partial
		get_template_part( '/partials/woocommerce/popups/popup', 'start' );

		foreach ( $product_variation_ids as $product_variation_id ) {

			// variable to store the variation data
			$variation_data = [];

			// get variation object
			$variation = new WC_Product_Variation( $product_variation_id );

			// get variation attribute name
			$variation_attribute = $variation->get_attribute( 'pa_license' );

			// get the term
			$variation_taxonomy_term = get_term_by( 'name', $variation_attribute, 'pa_license' );

			// get the title
			$variation_data['name']            = $variation->get_title();
			$variation_data['price']           = $variation->get_price();
			$variation_data['title']           = $variation_taxonomy_term->name;
			$variation_data['tagline']         = get_field( 'license_tagline', $variation_taxonomy_term );
			$variation_data['in_cart']         = $in_cart;
			$variation_data['list_points']     = get_field( 'license_list_points', $variation_taxonomy_term );
			$variation_data['add_tp_cart_url'] = '/cart/?add-to-cart=' . $product_variation_id;

			// set query var for usage in template partial
			set_query_var( 'variation_data', $variation_data );

			// the call partial
			get_template_part( '/partials/woocommerce/popups/popup', 'loop' );
		}

		// the call partial
		get_template_part( '/partials/woocommerce/popups/popup', 'end' );

		// Kill WordPress execution
		wp_die();
	}

	add_action( 'wp_ajax_add_variation_to_cart', 'add_variation_to_cart' );
	add_action( 'wp_ajax_nopriv_add_variation_to_cart', 'add_variation_to_cart' );

	add_action( 'wp_footer', function () {
		get_template_part( '/partials/woocommerce/popups/popup', 'body' );
	} );
}

if ( ! function_exists( 'add_user_favorite' ) ) {
	function add_user_favorite() {
		$product_id = intval( $_POST['id'] );
		$setType    = $_POST['type'];

		// bail if no $product_id sent
		if ( ! $product_id ) {
			echo false;
			wp_die();
		}

		// set user favorite
		$status = set_user_favorites( $product_id, $setType );

		// echo status
		echo $status;

		// die
		wp_die();
	}

	add_action( 'wp_ajax_add_user_favorite', 'add_user_favorite' );
	add_action( 'wp_ajax_nopriv_add_user_favorite', 'add_user_favorite' );
}

if ( ! function_exists( 'toggle_favorites_only' ) ) {
	function toggle_favorites_only() {
		// get current status of cookie from the JS
		$status = ! $_POST['status'];

		// set the cookies
		setcookie( '__fav_cookie', $status, time() + 60 * 60 * 24 * 30, '/' );
		$_COOKIE['__fav_cookie'] = $status;

		echo $_COOKIE['__fav_cookie'];

		// die
		wp_die();
	}

	add_action( 'wp_ajax_toggle_favorites_only', 'toggle_favorites_only' );
	add_action( 'wp_ajax_nopriv_toggle_favorites_only', 'toggle_favorites_only' );
}