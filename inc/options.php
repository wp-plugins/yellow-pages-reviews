<?php
$yellow_pages_reviews_options = array(
	array( 'name' => __( 'About', $yellow_pages_reviews->textdomain ), 'type' => 'opentab' ),
	array( 'type' => 'about' ),
	array(
		'name'  => __( 'Yellow Pages API Key', $yellow_pages_reviews->textdomain ),
		'desc'  => sprintf( __( 'API keys are manage through the <a href="%1$s" class="new-window" target="_blank" class="new-window">Yellow Pages Publisher Center</a>. Sign up is FREE and will provide you access to <a href="%2$s" target="_blank" class="new-window" title="Generate an API Key">generate an API Key</a> once registered. For further instructions please see this <a href="%3$s" target="_blank" class="new-window" title="How to obtain a YP API Key">helpful article</a>.', $yellow_pages_reviews->textdomain ), esc_url( 'https://publisher.yp.com/register' ), esc_url( 'https://publisher.yp.com/account/sites-apps' ), esc_url( 'https://wordimpress.com/docs/yellow-pages-reviews/#obtaining-a-yellow-pages-api-key' ) ),
		'std'   => '',
		'id'    => 'yellow_pages_api_key',
		'type'  => 'text',
		'label' => __( 'Yes', $yellow_pages_reviews->textdomain )
	),
	array( 'type' => 'closetab', 'actions' => true ),
	//Widgets Default Tab
	//    array(
	//        'name' => __('Widget Defaults', $yellow_pages_reviews->textdomain),
	//        'type' => 'opentab'
	//    ),
	//
	//    array('type' => 'closetab'),

	//Advanced Options
	array(
		'name' => __( 'Advanced Options', $yellow_pages_reviews->textdomain ),
		'type' => 'opentab'
	),
	array(
		'name'  => __( 'Disable Plugin CSS', $yellow_pages_reviews->textdomain ),
		'desc'  => __( 'Useful to style your own widget and for theme integration and optimization.', $yellow_pages_reviews->textdomain ),
		'std'   => '',
		'id'    => 'disable_css',
		'type'  => 'checkbox',
		'label' => __( 'Yes', $yellow_pages_reviews->textdomain )
	),
	array( 'type' => 'closetab' ),

//	BUndle Tab
	    array(
	        'name' => __('Get More Reviews', $yellow_pages_reviews->textdomain),
	        'type' => 'opentab'
	    ),
		array( 'type' => 'bundle' ),
	    array('type' => 'closetab'),
);