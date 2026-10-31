/**
 * CFHG Option B: UI polish pass. Plain JavaScript, no dependencies.
 *
 * Effects are opt-in through data attributes, so they can be attached to
 * Elementor widgets later (Advanced > Attributes):
 *
 *   data-cfhg-mark       a highlighter stroke that draws in when seen
 *   data-cfhg-count      a number that counts up once, keeping any suffix
 *   data-cfhg-track      giant words that open out as they scroll past
 *   data-cfhg-parallax   the image inside drifts more slowly than the page
 *   data-cfhg-carousel   (existing) gains a progress bar
 *
 * Nothing is hidden unless html.cfhg-ui is present; the head script removes
 * that class again if this file never runs.
 */
( function () {
	'use strict';

	window.cfhgUi = true;

	var doc = document;
	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var hasIO = 'IntersectionObserver' in window;
	var each = function ( sel, fn ) {
		Array.prototype.forEach.call( doc.querySelectorAll( sel ), fn );
	};

	/* Run fn once, the first time el comes into view. */
	function once( el, fn, margin ) {
		if ( reduce || ! hasIO ) {
			fn( el );
			return;
		}
		var io = new IntersectionObserver( function ( entries ) {
			if ( entries[ 0 ].isIntersecting ) {
				io.disconnect();
				fn( el );
			}
		}, { rootMargin: margin || '0px 0px -15% 0px' } );
		io.observe( el );
	}

	/* 1. Highlighter strokes. */
	each( '[data-cfhg-mark]', function ( el ) {
		once( el, function () {
			el.classList.add( 'is-in' );
		} );
	} );

	/* 2. Counters: count up from zero over 1.1s, easing out. The finished
	   number is in the HTML, so without this script nothing changes. */
	each( '[data-cfhg-count]', function ( el ) {
		var text = el.textContent;
		var match = text.match( /^(\D*)(\d+)(.*)$/ );
		if ( reduce || ! match ) {
			return;
		}
		var target = parseInt( match[ 2 ], 10 );
		once( el, function () {
			var start = null;
			function frame( now ) {
				if ( ! start ) {
					start = now;
				}
				var t = Math.min( 1, ( now - start ) / 1100 );
				var eased = 1 - Math.pow( 1 - t, 3 );
				el.textContent = match[ 1 ] + Math.round( target * eased ) + match[ 3 ];
				if ( t < 1 ) {
					window.requestAnimationFrame( frame );
				}
			}
			window.requestAnimationFrame( frame );
		}, '0px 0px -20% 0px' );
	} );

	/* 3. Carousel progress bar, in place of the old "1/3" counters. */
	each( '[data-cfhg-carousel]', function ( carousel ) {
		var trackEl = carousel.querySelector( '.cfhg-carousel__track' );
		var nav = carousel.querySelector( '.cfhg-carousel__nav' );
		if ( ! trackEl || ! nav ) {
			return;
		}
		var bar = doc.createElement( 'div' );
		bar.className = 'ui-progress';
		bar.setAttribute( 'aria-hidden', 'true' );
		bar.appendChild( doc.createElement( 'span' ) );
		nav.insertBefore( bar, nav.firstChild );
		var queued = false;
		function update() {
			queued = false;
			var max = trackEl.scrollWidth - trackEl.clientWidth;
			var seen = ( trackEl.clientWidth + trackEl.scrollLeft ) / trackEl.scrollWidth;
			bar.style.setProperty( '--cfhg-p', ( max > 0 ? seen : 1 ).toFixed( 3 ) );
		}
		trackEl.addEventListener( 'scroll', function () {
			if ( ! queued ) {
				queued = true;
				window.requestAnimationFrame( update );
			}
		}, { passive: true } );
		window.addEventListener( 'resize', update, { passive: true } );
		update();
	} );

	/* 4. Scroll-linked type and imagery. Browsers with scroll-driven
	   animations do this in CSS; others get a rAF fallback that only runs
	   while the element is on screen. */
	if ( reduce ) {
		return;
	}
	var cssScroll = window.CSS && CSS.supports && CSS.supports( 'animation-timeline: view()' );
	var live = [];
	var ticking = false;

	function update() {
		ticking = false;
		var vh = window.innerHeight;
		live.forEach( function ( el ) {
			var r = el.getBoundingClientRect();
			var p = ( vh - r.top ) / ( vh + r.height );
			el.style.setProperty( '--cfhg-p', Math.max( 0, Math.min( 1, p ) ).toFixed( 3 ) );
		} );
	}

	function request() {
		if ( ! ticking && live.length ) {
			ticking = true;
			window.requestAnimationFrame( update );
		}
	}

	each( '[data-cfhg-track], [data-cfhg-parallax]', function ( el ) {
		if ( cssScroll ) {
			el.classList.add( 'is-css-scroll' );
			return;
		}
		if ( ! hasIO ) {
			live.push( el );
			return;
		}
		new IntersectionObserver( function ( entries ) {
			var i = live.indexOf( el );
			if ( entries[ 0 ].isIntersecting && i < 0 ) {
				live.push( el );
				request();
			} else if ( ! entries[ 0 ].isIntersecting && i > -1 ) {
				live.splice( i, 1 );
			}
		} ).observe( el );
	} );

	window.addEventListener( 'scroll', request, { passive: true } );
	window.addEventListener( 'resize', request, { passive: true } );
	request();
}() );
