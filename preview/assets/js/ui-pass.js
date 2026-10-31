/**
 * CFHG Option A: UI polish pass. Plain JavaScript, no dependencies.
 *
 * Every effect is opt-in through a data attribute, so it can be attached to an
 * Elementor widget later through Advanced > Attributes or CSS Classes:
 *
 *   data-cfhg-split      split a heading into words for the load animation
 *   data-cfhg-reveal     "wipe" or "grow": revealed once, as it scrolls in
 *   data-cfhg-ink        words ink in from pale to full as the block is read
 *   data-cfhg-parallax   a number: how fast the image inside drifts on scroll
 *   data-cfhg-marquee    a slow, looping row of names (pauses on hover or tap)
 *
 * Nothing is hidden unless html.cfhg-ui is present, and the head script removes
 * that class again if this file never runs.
 */
( function () {
	'use strict';

	window.cfhgUi = true;

	var doc = document;
	var root = doc.documentElement;
	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var each = function ( sel, fn ) {
		Array.prototype.forEach.call( doc.querySelectorAll( sel ), fn );
	};

	/* Wrap every word of an element's text in spans, leaving child elements
	   (such as the inline photo tile) untouched. Returns the word spans. */
	function splitWords( el, outerClass, innerTag ) {
		var words = [];
		var walker = doc.createTreeWalker( el, NodeFilter.SHOW_TEXT );
		var nodes = [];
		while ( walker.nextNode() ) {
			nodes.push( walker.currentNode );
		}
		nodes.forEach( function ( node ) {
			var frag = doc.createDocumentFragment();
			node.nodeValue.split( /(\s+)/ ).forEach( function ( part ) {
				if ( ! part ) {
					return;
				}
				if ( /^\s+$/.test( part ) ) {
					frag.appendChild( doc.createTextNode( part ) );
					return;
				}
				var outer = doc.createElement( 'span' );
				outer.className = outerClass;
				if ( innerTag ) {
					var inner = doc.createElement( 'span' );
					inner.textContent = part;
					outer.appendChild( inner );
				} else {
					outer.textContent = part;
				}
				frag.appendChild( outer );
				words.push( outer );
			} );
			node.parentNode.replaceChild( frag, node );
		} );
		return words;
	}

	/* 1. Hero heading, word by word. */
	each( '[data-cfhg-split]', function ( el ) {
		if ( ! reduce ) {
			splitWords( el, 'cfhg-word', true ).forEach( function ( w, i ) {
				w.firstChild.style.setProperty( '--i', i );
			} );
		}
		el.classList.add( 'is-split' );
	} );

	/* 2. One-off reveals. */
	var reveals = doc.querySelectorAll( '[data-cfhg-reveal]' );
	if ( reduce || ! ( 'IntersectionObserver' in window ) ) {
		Array.prototype.forEach.call( reveals, function ( el ) {
			el.classList.add( 'is-in' );
		} );
	} else {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				if ( e.isIntersecting ) {
					e.target.classList.add( 'is-in' );
					io.unobserve( e.target );
				}
			} );
		}, { rootMargin: '0px 0px -12% 0px' } );
		Array.prototype.forEach.call( reveals, function ( el ) {
			io.observe( el );
		} );
	}

	/* 3. Scroll-linked work: one rAF loop that only runs while something that
	   needs it is on screen. */
	var active = [];
	var ticking = false;
	var vh = window.innerHeight;

	function update() {
		ticking = false;
		active.forEach( function ( item ) {
			item.run( item.el.getBoundingClientRect() );
		} );
	}

	function request() {
		if ( ! ticking && active.length ) {
			ticking = true;
			window.requestAnimationFrame( update );
		}
	}

	function track( el, run ) {
		var item = { el: el, run: run };
		if ( ! ( 'IntersectionObserver' in window ) ) {
			active.push( item );
			return;
		}
		new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				var i = active.indexOf( item );
				if ( e.isIntersecting && i < 0 ) {
					active.push( item );
					request();
				} else if ( ! e.isIntersecting && i > -1 ) {
					active.splice( i, 1 );
				}
			} );
		}, { rootMargin: '10% 0px' } ).observe( el );
	}

	if ( ! reduce ) {
		window.addEventListener( 'scroll', request, { passive: true } );
		window.addEventListener( 'resize', function () {
			vh = window.innerHeight;
			request();
		}, { passive: true } );

		/* 3.1 Ink: words darken from the first to the last as the block moves
		   from low on the screen to just above the middle. */
		each( '[data-cfhg-ink]', function ( el ) {
			var words = splitWords( el, 'cfhg-ink-word', false );
			var n = words.length;
			var spread = 4;
			el.classList.add( 'is-inking' );
			track( el, function ( r ) {
				var start = vh * 0.88;
				var end = vh * 0.42 - r.height;
				var p = Math.min( 1, Math.max( 0, ( start - r.top ) / ( start - end ) ) );
				var head = p * ( n + spread );
				words.forEach( function ( w, i ) {
					var v = Math.min( 1, Math.max( 0, ( head - i ) / spread ) );
					w.style.setProperty( '--ink', v.toFixed( 3 ) );
				} );
			} );
		} );

		/* 3.2 Parallax inside photographs. Browsers with scroll-driven
		   animations do this in CSS; this is the fallback for the rest. */
		var cssScroll = window.CSS && CSS.supports && CSS.supports( 'animation-timeline: view()' );
		each( '[data-cfhg-parallax]', function ( el ) {
			el.style.setProperty( '--cfhg-speed', parseFloat( el.getAttribute( 'data-cfhg-parallax' ) ) || 1 );
			if ( cssScroll ) {
				el.classList.add( 'is-css-scroll' );
				return;
			}
			track( el, function ( r ) {
				var p = ( r.top + r.height / 2 - vh / 2 ) / vh;
				el.style.setProperty( '--cfhg-p', Math.max( -1, Math.min( 1, p ) ).toFixed( 3 ) );
			} );
		} );
		request();
	}

	/* 4. Marquee: clone the names once so the loop is seamless, run it only
	   while it is on screen, and let a tap pause it on touch screens. */
	each( '[data-cfhg-marquee]', function ( el ) {
		if ( reduce ) {
			return;
		}
		var trackEl = el.firstElementChild;
		Array.prototype.slice.call( trackEl.children ).forEach( function ( child ) {
			var clone = child.cloneNode( true );
			clone.setAttribute( 'data-cfhg-clone', '' );
			trackEl.appendChild( clone );
		} );
		el.style.setProperty( '--ui-marquee-dur', Math.round( trackEl.scrollWidth / 2 / 55 ) + 's' );
		el.addEventListener( 'click', function () {
			el.classList.toggle( 'is-paused' );
		} );
		if ( 'IntersectionObserver' in window ) {
			new IntersectionObserver( function ( entries ) {
				el.classList.toggle( 'is-running', entries[ 0 ].isIntersecting );
			} ).observe( el );
		} else {
			el.classList.add( 'is-running' );
		}
	} );

	/* 5. Header: a paper background once the page has scrolled. */
	var header = doc.querySelector( '[data-cfhg-header]' );
	if ( header ) {
		var scrolled = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 40 );
		};
		window.addEventListener( 'scroll', scrolled, { passive: true } );
		scrolled();
	}

	root.classList.add( 'cfhg-ui-ready' );
}() );
