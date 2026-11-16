/**
 * CFHG Option B: living photographs. Plain JavaScript, no dependencies.
 *
 * A few of the theme's pictures have a short, silent, looping film of the
 * same scene (assets/video). When one of those pictures is on the page, the
 * film is laid over it and plays only while it is on screen. The picture
 * stays underneath, so nothing changes for anyone who prefers reduced
 * motion, is saving data, or whose browser will not play it.
 *
 * It works from the picture's file name, so it needs no setting in the
 * theme or in Elementor: swap the picture for another and the film goes.
 * window.cfhgLoops (inc/enqueue.php) maps picture names to films.
 */
( function () {
	'use strict';

	var loops = window.cfhgLoops || {};
	var names = Object.keys( loops );
	var conn = navigator.connection || {};
	if ( ! names.length || conn.saveData || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	Array.prototype.forEach.call( document.querySelectorAll( 'img' ), function ( img ) {
		var from = ( img.getAttribute( 'src' ) || '' ) + ' ' + ( img.getAttribute( 'srcset' ) || '' );
		var name = names.filter( function ( n ) {
			return from.indexOf( n ) !== -1;
		} )[ 0 ];
		if ( ! name || img.closest( '.cfhg-loop-wrap, .cfhg-avatars, .cfhg-footer' ) ) {
			return;
		}
		/* Wait until the picture is on screen: its size isn't known before then. */
		var seen = new IntersectionObserver( function ( entries ) {
			if ( entries[ 0 ].isIntersecting ) {
				seen.disconnect();
				/* Only the large pictures: never a thumbnail of the same photograph. */
				if ( img.clientWidth >= 240 ) {
					addFilm( img, name );
				}
			}
		} );
		seen.observe( img );
	} );

	function addFilm( img, name ) {
		var look = window.getComputedStyle( img );
		var wrap = document.createElement( 'span' );
		wrap.className = 'cfhg-loop-wrap';
		wrap.style.borderRadius = look.borderRadius;
		img.parentNode.insertBefore( wrap, img );
		wrap.appendChild( img );

		var film = document.createElement( 'video' );
		film.className = 'cfhg-loop';
		film.muted = true;
		film.loop = true;
		film.playsInline = true;
		film.preload = 'none';
		film.tabIndex = -1;
		film.setAttribute( 'muted', '' );
		film.setAttribute( 'playsinline', '' );
		film.setAttribute( 'aria-hidden', 'true' );
		film.setAttribute( 'disablepictureinpicture', '' );
		film.style.objectPosition = look.objectPosition;
		film.src = loops[ name ];
		film.addEventListener( 'playing', function () {
			film.classList.add( 'is-playing' );
		}, { once: true } );
		wrap.appendChild( film );

		new IntersectionObserver( function ( entries ) {
			if ( entries[ 0 ].isIntersecting ) {
				var started = film.play();
				if ( started && started.catch ) {
					started.catch( function () {} );
				}
			} else {
				film.pause();
			}
		}, { threshold: 0.2 } ).observe( wrap );
	}
}() );
