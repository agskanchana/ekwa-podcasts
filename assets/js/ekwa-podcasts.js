/* global ekwaPodcasts */
( function ( $ ) {
	'use strict';

	$( document ).ready( function () {
		$( '.ekwa-podcasts-wrap' ).each( function () {
			initCarousel( $( this ) );
		} );
	} );

	function initCarousel( $wrap ) {
		var $track     = $wrap.find( '.ekwa-episodes-track' );
		var $prev      = $wrap.find( '.ekwa-arrow-prev' );
		var $next      = $wrap.find( '.ekwa-arrow-next' );
		var perSlide   = parseInt( $wrap.data( 'per-slide' ), 10 ) || 3;
		var postsPerPg = parseInt( $wrap.data( 'posts-per-page' ), 10 ) || 9;
		var current    = 0;
		var loading    = false;

		function totalSlides() {
			return $track.find( '.ekwa-slide' ).length;
		}

		function goTo( index ) {
			var max = totalSlides() - 1;
			current = Math.max( 0, Math.min( index, max ) );
			$track.css( 'transform', 'translateX(-' + ( current * 100 ) + '%)' );
			$prev.prop( 'disabled', current === 0 );
			$next.prop( 'disabled', current >= max );
		}

		// Arrow clicks
		$prev.on( 'click', function () {
			goTo( current - 1 );
		} );

		$next.on( 'click', function () {
			goTo( current + 1 );
		} );

		// Touch / swipe support
		var touchStartX = 0;
		var touchEndX   = 0;
		var slider      = $wrap.find( '.ekwa-episodes-slider' )[ 0 ];

		if ( slider ) {
			slider.addEventListener( 'touchstart', function ( e ) {
				touchStartX = e.changedTouches[ 0 ].screenX;
			}, { passive: true } );

			slider.addEventListener( 'touchend', function ( e ) {
				touchEndX = e.changedTouches[ 0 ].screenX;
				var diff  = touchStartX - touchEndX;
				if ( Math.abs( diff ) > 40 ) {
					goTo( diff > 0 ? current + 1 : current - 1 );
				}
			}, { passive: true } );
		}

		// AJAX pagination
		$wrap.on( 'click', '.ekwa-page-btn', function () {
			if ( loading ) {
				return;
			}

			var $btn = $( this );
			var page = parseInt( $btn.data( 'page' ), 10 );

			if ( $btn.hasClass( 'active' ) ) {
				return;
			}

			loading = true;
			$btn.closest( '.ekwa-podcasts-wrap' ).addClass( 'ekwa-loading' );

			$.ajax( {
				url:  ekwaPodcasts.ajaxUrl,
				type: 'POST',
				data: {
					action:         'ekwa_load_episodes',
					nonce:          ekwaPodcasts.nonce,
					page:           page,
					posts_per_page: postsPerPg,
					per_slide:      perSlide,
				},
				success: function ( response ) {
					if ( response.success && response.data.html ) {
						$track.html( response.data.html );
						current = 0;
						goTo( 0 );

						$wrap.find( '.ekwa-page-btn' ).removeClass( 'active' );
						$btn.addClass( 'active' );

						// Scroll into view smoothly
						var offset = $wrap.offset().top - 80;
						$( 'html, body' ).animate( { scrollTop: offset }, 300 );
					}
				},
				complete: function () {
					loading = false;
					$btn.closest( '.ekwa-podcasts-wrap' ).removeClass( 'ekwa-loading' );
				},
			} );
		} );

		// Initial state
		goTo( 0 );
	}
} )( jQuery );
