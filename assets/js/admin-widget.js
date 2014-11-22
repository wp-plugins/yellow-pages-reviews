/**
 *  Yellow Pages Reviews JS: WP Admin
 *
 *  @description: JavaScripts for the admin side of the widget
 *  @author: Devin Walker
 *  @since: 1.0
 */

(function ( $ ) {
	"use strict";


	$( function () {
		/*
		 * Initialize the API Request Method widget radio input toggles
		 */
		ypr_widget_toogles();
		ypr_tipsy();

	} );

	$( document ).on( 'click', '.ypr-clear-cache', function ( e ) {
		e.preventDefault();
		var $this = $( this );
		$this.next( '.cache-clearing-loading' ).fadeIn( 'fast' );
		var data = {
			action        : 'clear_widget_cache',
			transient_id_1: $( this ).data( 'transient-id-1' ),
			transient_id_2: $( this ).data( 'transient-id-2' )
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post( ajaxurl, data, function ( response ) {
			$( '.cache-clearing-loading' ).hide();
			$this.prev( '.cache-message' ).text( response ).fadeIn( 'fast' ).delay( 2000 ).fadeOut();
		} );

	} );


	/**
	 * Function to Refresh jQuery toggles for Yelp Widget Pro upon saving specific widget
	 */
	$( document ).ajaxSuccess( function ( e, xhr, settings ) {
		ypr_widget_toogles();
		ypr_tipsy();
	} );


	function ypr_widget_toogles() {


		//Advanced Options Toggle (Bottom-gray panels)
		$( '.ypr-widget-toggler:not("clickable")' ).each( function () {

			$( this ).addClass( "clickable" ).unbind( "click" ).click( function () {
				$( this ).toggleClass( 'toggled' );
				$( this ).next().slideToggle();
			} )

		} );


	}

	function ypr_tipsy() {
		//Tooltips for admins
		$( '.tooltip-info' ).tipsy( {
			fade    : true,
			html    : true,
			gravity : 's',
			delayOut: 1000,
			delayIn : 500
		} );
	}

})( jQuery );