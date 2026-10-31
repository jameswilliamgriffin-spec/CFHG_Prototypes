/**
 * CFHG Option C: UI polish pass. Plain JavaScript, no dependencies.
 *
 * Opt-in hooks, so each effect can later be attached to an Elementor widget
 * (Advanced > Attributes or CSS Classes):
 *
 *   data-cfhg-draw       the logo-colour rule draws in when seen
 *   data-cfhg-focus      on a scroller track: centre card steps forward,
 *                        images drift inside their frames, progress bar
 *   data-cfhg-timeline   on a list: a rule fills as it is read
 *   data-cfhg-stick      on the navigation bar: stays at the top, with a
 *                        small logo once the header above has gone
 *   .cfhg-c-way__media   photographs open like a shutter
 *
 * Nothing is hidden unless html.cfhg-ui is present; the head script removes
 * that class again if this file never runs.
 */
( function () {
	'use strict';

	window.cfhgUi = true;

	var doc = document;
	var root = doc.documentElement;
	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var hasIO = 'IntersectionObserver' in window;
	var each = function ( sel, fn ) {
		Array.prototype.forEach.call( doc.querySelectorAll( sel ), fn );
	};
	var clamp = function ( v, a, b ) {
		return Math.max( a, Math.min( b, v ) );
	};

	/* 1. One-off reveals: the colour rule and the way-in photographs. */
	var revealIO = ! reduce && hasIO ? new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( e ) {
			if ( e.isIntersecting ) {
				e.target.classList.add( 'is-in' );
				revealIO.unobserve( e.target );
			}
		} );
	}, { rootMargin: '0px 0px -10% 0px' } ) : null;

	each( '[data-cfhg-draw], .cfhg-c-way__media', function ( el ) {
		if ( revealIO ) {
			revealIO.observe( el );
		} else {
			el.classList.add( 'is-in' );
		}
	} );

	/* 2. Scroll work shared by the timeline: one rAF, only while visible. */
	var live = [];
	var ticking = false;

	function update() {
		ticking = false;
		live.forEach( function ( item ) {
			item.run();
		} );
	}

	function request() {
		if ( ! ticking && live.length ) {
			ticking = true;
			window.requestAnimationFrame( update );
		}
	}

	function watch( el, run ) {
		var item = { run: run };
		if ( ! hasIO ) {
			live.push( item );
			return;
		}
		new IntersectionObserver( function ( entries ) {
			var i = live.indexOf( item );
			if ( entries[ 0 ].isIntersecting && i < 0 ) {
				live.push( item );
				request();
			} else if ( ! entries[ 0 ].isIntersecting && i > -1 ) {
				live.splice( i, 1 );
			}
		} ).observe( el );
	}

	window.addEventListener( 'scroll', request, { passive: true } );
	window.addEventListener( 'resize', request, { passive: true } );

	/* 3. Programme timeline. */
	each( '[data-cfhg-timeline]', function ( list ) {
		var items = list.querySelectorAll( '.cfhg-c-programme__item' );
		function paint() {
			var r = list.getBoundingClientRect();
			var p = reduce ? 1 : clamp( ( window.innerHeight * 0.62 - r.top ) / r.height, 0, 1 );
			var reach = r.top + p * r.height;
			list.style.setProperty( '--cfhg-p', p.toFixed( 3 ) );
			Array.prototype.forEach.call( items, function ( item ) {
				item.classList.toggle( 'is-past', item.getBoundingClientRect().top + 24 <= reach );
			} );
		}
		paint();
		if ( ! reduce ) {
			watch( list, paint );
		}
	} );

	/* 4. Island scroller: focus on the card nearest the middle, parallax
	   inside the images, and a progress bar beside the arrows. */
	each( '[data-cfhg-focus]', function ( track ) {
		var cards = track.querySelectorAll( '.cfhg-c-scroller__item' );
		var imgs = track.querySelectorAll( '.cfhg-c-island-card__media img' );
		var controls = track.closest( '[data-cfhg-c-scroller]' );
		controls = controls && controls.querySelector( '.cfhg-c-scroller__controls' );
		var bar = null;
		if ( controls ) {
			bar = doc.createElement( 'div' );
			bar.className = 'ui-progress';
			bar.setAttribute( 'aria-hidden', 'true' );
			bar.appendChild( doc.createElement( 'span' ) );
			controls.appendChild( bar );
		}
		var queued = false;
		function paint() {
			queued = false;
			var t = track.getBoundingClientRect();
			var mid = t.left + t.width / 2;
			var best = null;
			var bestDist = Infinity;
			Array.prototype.forEach.call( cards, function ( card, i ) {
				var c = card.getBoundingClientRect();
				var offset = ( c.left + c.width / 2 - mid ) / t.width;
				if ( Math.abs( offset ) < bestDist ) {
					bestDist = Math.abs( offset );
					best = card;
				}
				if ( ! reduce && imgs[ i ] ) {
					imgs[ i ].style.setProperty( '--cfhg-x', clamp( offset, -1, 1 ).toFixed( 3 ) );
				}
			} );
			Array.prototype.forEach.call( cards, function ( card ) {
				card.classList.toggle( 'is-focus', card === best );
			} );
			if ( bar ) {
				var seen = ( track.clientWidth + track.scrollLeft ) / track.scrollWidth;
				bar.style.setProperty( '--cfhg-p', clamp( seen, 0, 1 ).toFixed( 3 ) );
			}
		}
		function queue() {
			if ( ! queued ) {
				queued = true;
				window.requestAnimationFrame( paint );
			}
		}
		track.addEventListener( 'scroll', queue, { passive: true } );
		window.addEventListener( 'resize', queue, { passive: true } );
		paint();
	} );

	/* 5. Sticky navigation bar. The header sits at a negative top equal to
	   its upper row, so only the blue bar stays in view; a small logo slides
	   in once the main logo has gone. */
	var header = doc.querySelector( '.cfhg-c-header' );
	var top = header && header.querySelector( '.cfhg-c-header__top' );
	var nav = header && header.querySelector( '[data-cfhg-stick]' );
	if ( header && top && nav ) {
		var inner = nav.querySelector( '.cfhg-c-container' ) || nav;
		var logo = doc.createElement( 'a' );
		logo.className = 'ui-nav-logo';
		logo.href = 'index.html';
		logo.tabIndex = -1;
		logo.setAttribute( 'aria-hidden', 'true' );
		logo.innerHTML = '<img src="assets/images/cfhg-c-logo-light.webp" alt="" width="271" height="80" loading="lazy" decoding="async">';
		inner.insertBefore( logo, inner.firstChild );

		var topH = 0;
		var measure = function () {
			topH = top.offsetHeight;
			header.style.setProperty( '--ui-top-h', topH + 'px' );
		};
		var list = nav.querySelector( '.cfhg-c-nav__list' );
		var stuck = function () {
			var on = header.getBoundingClientRect().top <= -topH + 1 && topH > 0;
			header.classList.toggle( 'is-stuck', on );
			/* Only show the small logo where it clears the first menu item,
			   which it may not at the largest text sizes. */
			if ( on && list && list.firstElementChild ) {
				var room = list.firstElementChild.getBoundingClientRect().left - inner.getBoundingClientRect().left;
				header.classList.toggle( 'has-logo-room', room > logo.querySelector( 'img' ).offsetWidth + 40 );
			}
		};
		measure();
		stuck();
		if ( 'ResizeObserver' in window ) {
			new ResizeObserver( function () {
				measure();
				stuck();
			} ).observe( top );
		}
		window.addEventListener( 'scroll', stuck, { passive: true } );
	}

	/* 6. Text size: confirm the new size beside the buttons for a moment.
	   The theme's own live region already announces it to screen readers. */
	each( '[data-cfhg-c-textsize]', function ( group ) {
		var note = doc.createElement( 'span' );
		note.className = 'ui-size-note';
		note.setAttribute( 'aria-hidden', 'true' );
		group.appendChild( note );
		var timer = null;
		group.addEventListener( 'click', function ( event ) {
			if ( ! event.target.closest( '[data-cfhg-c-size]' ) ) {
				return;
			}
			window.requestAnimationFrame( function () {
				var pct = Math.round( parseFloat( window.getComputedStyle( root ).fontSize ) / 16 * 100 );
				note.textContent = 'Text size ' + pct + '%';
				note.classList.add( 'is-shown' );
				window.clearTimeout( timer );
				timer = window.setTimeout( function () {
					note.classList.remove( 'is-shown' );
				}, 1600 );
			} );
		} );
	} );

	request();
}() );
