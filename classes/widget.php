<?php

/**
 *  Yellow Pages Reviews
 *
 * @description: The Yellow Pages Reviews
 * @since      : 1.0
 */
class Yellow_Pages_Reviews extends WP_Widget {

	public $options; //Plugin Options from Options Panel
	public $api_key; //Plugin Options from Options Panel


	/**
	 * Array of Private Options
	 *
	 * @since    1.0.0
	 *
	 * @var array
	 */
	public $widget_fields = array(
		'title'              => '',
		'listing_id'         => '',
		'cache'              => '',
		'title_output'       => '',
		'widget_style'       => 'Bare Bones',
		'hide_header'        => '',
		'hide_out_of_rating' => '',
		'target_blank'       => '1',
		'no_follow'          => '1',
	);


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'ypr_widget', // Base ID
			'Yellow Pages Reviews', // Name
			array(
				'classname'   => 'yellow-pages-reviews',
				'description' => __( 'Display user reviews for any location found on Yellow Pages.', 'ypr' )
			)
		);

		$this->options = get_option( 'yellowpagesreviews_options' );
		//API key (muy importante!)
		$this->api_key = $this->options['yellow_pages_api_key'];

		add_action( 'wp_enqueue_scripts', array( $this, 'add_ypr_widget_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_ypr_admin_widget_scripts' ) );
		add_action( 'wp_ajax_clear_widget_cache', array( $this, 'ypr_clear_widget_cache' ) );


	}

	//Load Widget JS Script ONLY on Widget page
	function add_ypr_admin_widget_scripts( $hook ) {

		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		if ( $hook == 'widgets.php' ) {

			//Enqueue
			wp_enqueue_script( 'ypr_widget_admin_tipsy', plugins_url( 'assets/js/ypr-tipsy' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ) );
			wp_enqueue_script( 'ypr_widget_admin_scripts', plugins_url( 'assets/js/admin-widget' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ) );

			// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
			wp_localize_script(
				'ypr_widget_admin_scripts', 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
			);

			wp_enqueue_style( 'ypr_widget_admin_tipsy', plugins_url( 'assets/css/ypr-tipsy' . $suffix . '.css', dirname( __FILE__ ) ) );
			wp_enqueue_style( 'ypr_widget_admin_css', plugins_url( 'assets/css/admin-widget' . $suffix . '.css', dirname( __FILE__ ) ) );


		}

	}


	/**
	 * Adds Yellow Pages Reviews Stylesheets
	 */

	function add_ypr_widget_scripts() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		//Determine whether to display minified scripts/css or not (debugging true sets it)
		$ypr_css = plugins_url( 'assets/css/yellow-pages-reviews' . $suffix . '.css', dirname( __FILE__ ) );
		//$ypr_widget_js           = plugins_url( 'assets/js/yellow-pages-reviews.js', dirname( __FILE__ ) );

		if ( $this->options["disable_css"] !== "on" ) {
			wp_register_style( 'ypr_widget', $ypr_css );
			wp_enqueue_style( 'ypr_widget' );
		}


	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget( $args, $instance ) {

		extract( $args );

		//loop through options array and save variables for usage within function
		foreach ( $instance as $variable => $value ) {
			${$variable} = ! isset( $instance[ $variable ] ) ? $this->widget_fields[ $variable ] : esc_attr( $instance[ $variable ] );
		}

		//Enqueue necessary scripts
		$this->enqueue_widget_theme_scripts( $widget_style );

		//Check for a reference. If none, output error
		if ( $listing_id === 'No location set' ) {
			$this->output_error_message( __( 'No location set yet for this widget.', 'ypr' ), 'error' );

			return false;
		}

		//Title filter
		if ( isset( $title ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}


		// Open link in new window if set
		if ( $target_blank == '1' ) {
			$target_blank = 'target="_blank" ';
		} else {
			$target_blank = '';
		}

		// Add nofollow relation if set
		if ( $no_follow == '1' ) {
			$no_follow = 'rel="nofollow" ';
		} else {
			$no_follow = '';
		}


		// Cache: cache option is enabled
		if ( $cache !== 'None' ) {

			//serialize($instance) sets the transient cache from the $instance variable which can easily bust the cache once options are changed
			$response            = get_transient( 'ypr_widget_api' );
			$widget_options      = get_transient( 'ypr_widget_options' );
			$serialized_instance = serialize( $instance );

			// Check for an existing copy of our cached/transient data
			// also check to see if widget options have updated; this will bust the cache
			if ( $response === false || $serialized_instance !== $widget_options ) {

				// It wasn't there, so regenerate the data and save the transient
				//Get Time to Cache Data
				$expiration = $cache;

				//Assign Time to appropriate Math
				switch ( $expiration ) {
					case "1 Hour":
						$expiration = 3600;
						break;
					case "3 Hours":
						$expiration = 3600 * 3;
						break;
					case "6 Hours":
						$expiration = 3600 * 6;
						break;
					case "12 Hours":
						$expiration = 60 * 60 * 12;
						break;
					case "1 Day":
						$expiration = 60 * 60 * 24;
						break;
					case "2 Days":
						$expiration = 60 * 60 * 48;
						break;
					case "1 Week":
						$expiration = 60 * 60 * 168;
						break;
				}

				// Cache data wasn't there, so regenerate the data and save the transient
				$response = $this->ypr_plugin_curl( $listing_id );
				set_transient( 'ypr_widget_api', $response, $expiration );
				set_transient( 'ypr_widget_options', $serialized_instance, $expiration );

			} //end response


		} else {

			//No Cache option enabled;
			$response = $this->ypr_plugin_curl( $listing_id );

		}

		//Error message
		if ( ! empty( $response["metaProperties"]["errorCode"] ) ) {

			$this->output_error_message( $response["metaProperties"]["message"], 'error' );

			return false;
		}


		//Widget Style
		$style = "ypr-" . sanitize_title( $widget_style ) . "-style";
		// no 'class' attribute - add one with the value of width
		//@see http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
		if ( ! empty( $before_widget ) && strpos( $before_widget, 'class' ) === false ) {
			$before_widget = str_replace( '>', 'class="' . $style . '"', $before_widget );
		} // there is 'class' attribute - append width value to it
		elseif ( ! empty( $before_widget ) && strpos( $before_widget, 'class' ) !== false ) {
			$before_widget = str_replace( 'class="', 'class="' . $style . ' ', $before_widget );
		} //no 'before_widget' at all so wrap widget with div
		else {
			$before_widget = '<div class="yellow-pages-reviews">';
			$before_widget = str_replace( 'class="', 'class="' . $style . ' ', $before_widget );
		}


		/* Before widget */
		echo $before_widget;

		// if the title is set & the user hasn't disabled title output
		if ( ! empty( $title ) ) {
			/* Add class to before_widget from within a custom widget
		 http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget
		 */
			// no 'class' attribute - add one with the value of width
			if ( ! empty( $before_title ) && strpos( $before_title, 'class' ) === false ) {
				$before_title = str_replace( '>', ' class="ypr-widget-title">', $before_title );
			} //widget title has 'class' attribute
			elseif ( ! empty( $before_title ) && strpos( $before_title, 'class' ) !== false ) {
				$before_title = str_replace( 'class="', 'class="ypr-widget-title ', $before_title );
			} //no 'title' at all so wrap widget with div
			else {
				$before_title = '<h3 class="">';
				$before_title = str_replace( 'class="', 'class="ypr-widget-title ', $before_title );
			}
			$after_title = empty( $after_title ) ? '</h3>' : $after_title;

			echo $before_title . $title . $after_title;
		}


		include( YPR_PLUGIN_PATH . '/inc/widget-frontend.php' );


	}


	/**
	 * @DESC: Saves the widget options
	 * @SEE WP_Widget::update
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//loop through options array and save to new instance
		foreach ( $this->widget_fields as $field => $value ) {
			$instance[ $field ] = strip_tags( stripslashes( $new_instance[ $field ] ) );
		}


		return $instance;
	}


	/**
	 * Back-end widget form.
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {

		//API Key Check:
		if ( ! isset( $this->options['yellow_pages_api_key'] ) || empty( $this->options['yellow_pages_api_key'] ) ) {
			$api_key_error = sprintf( __( '<p><strong>Notice: </strong>No Yellow Pages API key detected. You will need to create an API key to use Yellow Pages Reviews. API keys are manage through the <a href="%1$s" class="new-window" target="_blank" class="new-window">Yellow Pages Publisher Center</a>. Sign up is FREE and will provide you access to <a href="%2$s" target="_blank" class="new-window" title="Generate an API Key">generate an API Key</a> once registered.</p> <p>Once you have obtained your API key enter it in the <a href="%3$s" title="Yellow Pages Reviews Plugin Settings">plugin settings page</a>. For step-by-step instructions, please refer to <a href="%4$s" target="_blank" class="new-window" title="Generate a YP API Key">the plugin documentation</a>.</p>', 'ypr' ), esc_url( 'https://publisher.yp.com/register' ), esc_url( 'https://publisher.yp.com/account/sites-apps' ), admin_url( '/options-general.php?page=yellowpagesreviews' ), esc_url( 'https://wordimpress.com/docs/yellow-pages-reviews/#obtaining-a-yellow-pages-api-key' ) );
			$this->output_error_message( $api_key_error, 'error' );

			return;
		}

		//loop through options array and save options to new instance
		foreach ( $this->widget_fields as $field => $value ) {
			${$field} = ! isset( $instance[ $field ] ) ? $value : esc_attr( $instance[ $field ] );
		}
		//Get the widget form
		include( YPR_PLUGIN_PATH . '/inc/widget-form.php' );


	} //end form function


	/**
	 * @DESC: CURLs the Google Places API with our url parameters and returns JSON response
	 */
	function ypr_plugin_curl( $listing_id ) {

		//Add args to
		$yellow_pages_details_url = add_query_arg(
			array(
				'listingid' => $listing_id,
				'key'       => $this->api_key
			),
			'http://api2.yp.com/listings/v1/details'
		);

		// cURL 1: Send API Call using WP's HTTP API
		$location_details_data = wp_remote_get( $yellow_pages_details_url );


		if ( is_wp_error( $location_details_data ) ) {
			$error_message = $location_details_data->get_error_message();
			$this->output_error_message( "Something went wrong: $error_message", 'error' );
		}

		//Use curl only if necessary
		if ( empty( $location_details_data['body'] ) ) {
			$ch = curl_init( $yellow_pages_details_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			$data = curl_exec( $ch ); // Google response
			curl_close( $ch );
			$xml       = simplexml_load_string( $data['body'] );
			$json      = json_encode( $xml );
			$response1 = json_decode( $json, true );

		} else {
			$xml       = simplexml_load_string( $location_details_data['body'] );
			$json      = json_encode( $xml );
			$response1 = json_decode( $json, true );
		}

		$yellow_pages_reviews_url = add_query_arg(
			array(
				'listingid' => $listing_id,
				'key'       => $this->api_key
			),
			'http://api2.yp.com/listings/v1/reviews'
		);

		// cURL 2: Send API Call using WP's HTTP API
		$location_reviews_data = wp_remote_get( $yellow_pages_reviews_url );

		if ( is_wp_error( $yellow_pages_reviews_url ) ) {
			$error_message = $yellow_pages_reviews_url->get_error_message();
			$this->output_error_message( "Something went wrong: $error_message", 'error' );
		}

		//Use curl only if necessary
		if ( empty( $location_reviews_data['body'] ) ) {
			$ch = curl_init( $yellow_pages_reviews_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			$data = curl_exec( $ch ); // Google response
			curl_close( $ch );
			$xml       = simplexml_load_string( $data['body'] );
			$json      = json_encode( $xml );
			$response2 = json_decode( $json, true );

		} else {
			$xml       = simplexml_load_string( $location_reviews_data['body'] );
			$json      = json_encode( $xml );
			$response2 = json_decode( $json, true );
		}

		$response = array_merge( $response1, $response2 );

		//Google response data in JSON format
		return $response;

	}


	/**
	 * Enqueue Widget Theme Scripts
	 *
	 * Outputs the necessary scripts for the widget themes
	 *
	 * @param $widget_style
	 */
	function enqueue_widget_theme_scripts( $widget_style ) {

		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		//Determine which CSS to pull
		$css_raised  = YPR_PLUGIN_URL . '/assets/css/ypr-theme-raised' . $suffix . '.css';
		$css_minimal = YPR_PLUGIN_URL . '/assets/css/ypr-theme-minimal' . $suffix . '.css';
		$css_shadow  = YPR_PLUGIN_URL . '/assets/css/ypr-theme-shadow' . $suffix . '.css';
		$css_inset   = YPR_PLUGIN_URL . '/assets/css/ypr-theme-inset' . $suffix . '.css';

		if ( $widget_style === 'Minimal Light' || $widget_style === 'Minimal Dark' ) {
			//enqueue theme style
			wp_register_style( 'ypr_widget_style_minimal', $css_minimal );
			wp_enqueue_style( 'ypr_widget_style_minimal' );
		}
		if ( $widget_style === 'Shadow Light' || $widget_style === 'Shadow Dark' ) {
			wp_register_style( 'ypr_widget_style_shadow', $css_shadow );
			wp_enqueue_style( 'ypr_widget_style_shadow' );
		}
		if ( $widget_style === 'Inset Light' || $widget_style === 'Inset Dark' ) {
			wp_register_style( 'ypr_widget_style_inset', $css_inset );
			wp_enqueue_style( 'ypr_widget_style_inset' );
		}
		if ( $widget_style === 'Raised Light' || $widget_style === 'Raised Dark' ) {
			wp_register_style( 'ypr_widget_style_raised', $css_raised );
			wp_enqueue_style( 'ypr_widget_style_raised' );
		}

	}


	/**
	 * Output Error Message
	 *
	 * @param $message
	 * @param $style
	 */
	function output_error_message( $message, $style ) {

		switch ( $style ) {
			case 'error' :
				$style = 'ypr-error';
				break;
			case 'warning' :
				$style = 'ypr-warning';
				break;
			default :
				$style = 'ypr-warning';
		}

		$output = '<div class="ypr-alert ' . $style . '">';
		$output .= $message;
		$output .= '</div>';

		echo $output;

	}

	/**
	 * Get Star Rating
	 *
	 * Returns the necessary output for Google Star Ratings
	 *
	 * @param $rating
	 * @param $unix_timestamp
	 */
	function get_star_rating( $rating, $unix_timestamp, $hide_out_of_rating ) {

		//continue with output
		$output = '<div class="rating-wrap">';
		$output .= '<div class="star-rating-wrap">';
		$output .= '<div class="star-rating-size" style="width:' . ( 65 * $rating / 5 ) . 'px;"></div>';
		$output .= '</div>';

		$output .= '<p class="ypr-rating-value" ' . ( ( $hide_out_of_rating === '1' ) ? ' style="display:none;"' : '' ) . '><span itemprop="ratingValue">' . $rating . '</span>' . __( ' out of 5 stars', 'ypr' ) . '</p>';
		if ( $unix_timestamp ) {
			$output .= '<span class="ypr-rating-time">' . $this->get_time_since( $unix_timestamp ) . '</span>';
		}
		$output .= '</div>';

		return $output;

	}

	/**
	 * Time Since
	 * Works out the time since the entry post, takes a an argument in unix time (seconds)
	 */
	static public function get_time_since( $date, $granularity = 1 ) {


		$difference = time() - $date;
		$retval     = '';
		$periods    = array(
			'decade' => 315360000,
			'year'   => 31536000,
			'month'  => 2628000,
			'week'   => 604800,
			'day'    => 86400,
			'hour'   => 3600,
			'minute' => 60,
			'second' => 1
		);

		foreach ( $periods as $key => $value ) {
			if ( $difference >= $value ) {
				$time = floor( $difference / $value );
				$difference %= $value;
				$retval .= ( $retval ? ' ' : '' ) . $time . ' ';
				$retval .= ( ( $time > 1 ) ? $key . 's' : $key );
				$granularity --;
			}
			if ( $granularity == '0' ) {
				break;
			}
		}

		return ' posted ' . $retval . ' ago';
	}

	/**
	 * AJAX Clear Widget Cache
	 */
	function ypr_clear_widget_cache() {

		if ( isset( $_POST['transient_id_1'] ) && isset( $_POST['transient_id_2'] ) ) {

			delete_transient( $_POST['transient_id_1'] );
			delete_transient( $_POST['transient_id_2'] );
			echo "Cache cleared";

		} else {
			echo "Error: Transient ID not set. Cache not cleared.";
		}

		die();

	}


} //end Yellow_Pages_Reviews Class
