/**
 * CFHG Option A: front-end behaviour. Plain JavaScript, no dependencies.
 *
 * 1. Menu panel: open and close, Escape key, click outside, focus trap.
 * 2. Header: hides when scrolling down, returns when scrolling up.
 * 3. Reveal: sections fade up as they arrive.
 * 4. Photo strip: drag sideways with a mouse.
 */
( function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* 1. Menu panel ------------------------------------------------------- */
	var header = doc.querySelector( '[data-cfhg-header]' );
	var toggle = doc.querySelector( '[data-cfhg-menu-toggle]' );
	var panel = doc.querySelector( '[data-cfhg-menu]' );
	var lastFocus = null;

	function focusables() {
		var nodes = [ toggle ].concat( Array.prototype.slice.call( panel.querySelectorAll( 'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])' ) ) );
		return nodes.filter( function ( el ) {
			return el && el.offsetParent !== null;
		} );
	}

	function openMenu() {
		lastFocus = doc.activeElement;
		toggle.setAttribute( 'aria-expanded', 'true' );
		panel.classList.add( 'is-open' );
		header.classList.add( 'is-menu-open' );
		header.classList.remove( 'is-hidden' );
		doc.body.classList.add( 'cfhg-menu-open' );
		var first = panel.querySelector( 'a[href]' );
		if ( first ) {
			window.setTimeout( function () {
				first.focus( { preventScroll: true } );
			}, reduceMotion ? 0 : 150 );
		}
	}

	function closeMenu( returnFocus ) {
		toggle.setAttribute( 'aria-expanded', 'false' );
		panel.classList.remove( 'is-open' );
		header.classList.remove( 'is-menu-open' );
		doc.body.classList.remove( 'cfhg-menu-open' );
		if ( returnFocus !== false ) {
			( lastFocus && lastFocus.focus ? lastFocus : toggle ).focus( { preventScroll: true } );
		}
	}

	function isOpen() {
		return toggle.getAttribute( 'aria-expanded' ) === 'true';
	}

	if ( header && toggle && panel ) {
		toggle.addEventListener( 'click', function () {
			if ( isOpen() ) {
				closeMenu();
			} else {
				openMenu();
			}
		} );

		doc.addEventListener( 'keydown', function ( event ) {
			if ( ! isOpen() ) {
				return;
			}
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				closeMenu();
				return;
			}
			if ( event.key === 'Tab' ) {
				var items = focusables();
				if ( ! items.length ) {
					return;
				}
				var first = items[ 0 ];
				var last = items[ items.length - 1 ];
				if ( event.shiftKey && doc.activeElement === first ) {
					event.preventDefault();
					last.focus();
				} else if ( ! event.shiftKey && doc.activeElement === last ) {
					event.preventDefault();
					first.focus();
				}
			}
		} );

		doc.addEventListener( 'click', function ( event ) {
			if ( isOpen() && ! panel.contains( event.target ) && ! toggle.contains( event.target ) ) {
				closeMenu( false );
			}
		} );

		/* Following an in-page link (such as #next-meeting) closes the panel. */
		panel.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( 'a[href]' );
			if ( link && link.hash && link.pathname === window.location.pathname ) {
				closeMenu( false );
			}
		} );
	}

	/* 2. Header hides on the way down, returns on the way up -------------- */
	if ( header ) {
		var lastY = window.scrollY;
		var ticking = false;

		window.addEventListener(
			'scroll',
			function () {
				if ( ticking ) {
					return;
				}
				ticking = true;
				window.requestAnimationFrame( function () {
					var y = window.scrollY;
					var down = y > lastY;
					if ( ! header.classList.contains( 'is-menu-open' ) ) {
						header.classList.toggle( 'is-hidden', down && y > 160 );
					}
					lastY = y;
					ticking = false;
				} );
			},
			{ passive: true }
		);

		/* Keyboard users tabbing into the header always see it. */
		header.addEventListener( 'focusin', function () {
			header.classList.remove( 'is-hidden' );
		} );
	}

	/* 3. Reveal on scroll ------------------------------------------------- */
	var autoTargets = '.cfhg-story, .cfhg-partners__list > li, .cfhg-steps > li, .cfhg-places > li';
	Array.prototype.forEach.call( doc.querySelectorAll( autoTargets ), function ( el ) {
		el.setAttribute( 'data-reveal', '' );
	} );

	var revealables = doc.querySelectorAll( '[data-reveal]' );

	if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
		Array.prototype.forEach.call( revealables, function ( el ) {
			el.classList.add( 'is-revealed' );
		} );
	} else {
		/* Anything already on screen is shown straight away, so nothing flickers. */
		var viewport = window.innerHeight;
		Array.prototype.forEach.call( revealables, function ( el ) {
			if ( el.getBoundingClientRect().top < viewport ) {
				el.classList.add( 'is-revealed' );
			}
		} );

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-revealed' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -8% 0px' }
		);

		Array.prototype.forEach.call( revealables, function ( el ) {
			if ( ! el.classList.contains( 'is-revealed' ) ) {
				observer.observe( el );
			}
		} );

		root.classList.add( 'cfhg-reveal-ready' );
	}

	/* 4. Photo strip: drag sideways with a mouse -------------------------- */
	Array.prototype.forEach.call( doc.querySelectorAll( '.cfhg-strip' ), function ( strip ) {
		var startX = 0;
		var startScroll = 0;
		var dragging = false;
		var moved = false;

		strip.addEventListener( 'pointerdown', function ( event ) {
			if ( event.pointerType !== 'mouse' || event.button !== 0 ) {
				return;
			}
			dragging = true;
			moved = false;
			startX = event.clientX;
			startScroll = strip.scrollLeft;
			strip.setPointerCapture( event.pointerId );
		} );

		strip.addEventListener( 'pointermove', function ( event ) {
			if ( ! dragging ) {
				return;
			}
			var delta = event.clientX - startX;
			if ( Math.abs( delta ) > 4 ) {
				moved = true;
				strip.classList.add( 'is-dragging' );
			}
			strip.scrollLeft = startScroll - delta;
		} );

		function stop( event ) {
			if ( ! dragging ) {
				return;
			}
			dragging = false;
			strip.classList.remove( 'is-dragging' );
			if ( strip.hasPointerCapture( event.pointerId ) ) {
				strip.releasePointerCapture( event.pointerId );
			}
		}

		strip.addEventListener( 'pointerup', stop );
		strip.addEventListener( 'pointercancel', stop );

		/* A drag should not count as a click on anything inside. */
		strip.addEventListener(
			'click',
			function ( event ) {
				if ( moved ) {
					event.preventDefault();
					event.stopPropagation();
					moved = false;
				}
			},
			true
		);

		/* Arrow keys scroll the strip when it has focus. */
		strip.addEventListener( 'keydown', function ( event ) {
			var step = strip.clientWidth * 0.6;
			if ( event.key === 'ArrowRight' ) {
				strip.scrollBy( { left: step, behavior: reduceMotion ? 'auto' : 'smooth' } );
				event.preventDefault();
			} else if ( event.key === 'ArrowLeft' ) {
				strip.scrollBy( { left: -step, behavior: reduceMotion ? 'auto' : 'smooth' } );
				event.preventDefault();
			}
		} );
	} );
}() );
