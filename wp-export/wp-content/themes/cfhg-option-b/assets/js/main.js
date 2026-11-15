/**
 * CFHG Option B: front-end behaviour. Plain JavaScript, no dependencies.
 *
 * 1. Side-panel menu: open and close, Escape, click outside, focus trap.
 * 2. Header: hides when scrolling down, returns when scrolling up, and takes
 *    a cream background once the page has scrolled.
 * 3. Reveal: sections rise in; snapshots and notes settle into their tilt.
 * 4. Story carousel: previous and next buttons, arrow keys, end states.
 */
( function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* 1. Side-panel menu -------------------------------------------------- */
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

	function isOpen() {
		return toggle.getAttribute( 'aria-expanded' ) === 'true';
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
			}, reduceMotion ? 0 : 120 );
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
		header.classList.toggle( 'is-scrolled', lastY > 40 );
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
					if ( ! header.classList.contains( 'is-menu-open' ) ) {
						header.classList.toggle( 'is-hidden', y > lastY && y > 160 );
					}
					header.classList.toggle( 'is-scrolled', y > 40 );
					lastY = y;
					ticking = false;
				} );
			},
			{ passive: true }
		);

		header.addEventListener( 'focusin', function () {
			header.classList.remove( 'is-hidden' );
		} );
	}

	/* 3. Reveal ----------------------------------------------------------- */
	var auto = '.cfhg-note, .cfhg-steps > li, .cfhg-places > li, .cfhg-partners__list > li';
	Array.prototype.forEach.call( doc.querySelectorAll( auto ), function ( el, i ) {
		if ( ! el.hasAttribute( 'data-reveal' ) ) {
			el.setAttribute( 'data-reveal', '' );
		}
	} );

	var revealables = doc.querySelectorAll( '[data-reveal]' );

	if ( reduceMotion || ! ( 'IntersectionObserver' in window ) ) {
		Array.prototype.forEach.call( revealables, function ( el ) {
			el.classList.add( 'is-revealed' );
		} );
	} else {
		/* Stagger items that share a parent, so notes and tiles arrive one by one. */
		Array.prototype.forEach.call( revealables, function ( el ) {
			var siblings = el.parentElement ? Array.prototype.filter.call( el.parentElement.children, function ( c ) {
				return c.hasAttribute( 'data-reveal' );
			} ) : [];
			var index = siblings.indexOf( el );
			if ( siblings.length > 1 && index > 0 ) {
				el.style.transitionDelay = Math.min( index * 90, 450 ) + 'ms';
			}
		} );

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
			{ rootMargin: '0px 0px -10% 0px' }
		);

		Array.prototype.forEach.call( revealables, function ( el ) {
			if ( ! el.classList.contains( 'is-revealed' ) ) {
				observer.observe( el );
			}
		} );

		root.classList.add( 'cfhg-reveal-ready' );
	}

	/* 4. Story carousel --------------------------------------------------- */
	Array.prototype.forEach.call( doc.querySelectorAll( '[data-cfhg-carousel]' ), function ( carousel ) {
		var track = carousel.querySelector( '.cfhg-carousel__track' );
		var prev = carousel.querySelector( '[data-cfhg-prev]' );
		var next = carousel.querySelector( '[data-cfhg-next]' );
		if ( ! track || ! prev || ! next ) {
			return;
		}

		function step() {
			var slide = track.querySelector( '.cfhg-slide' );
			if ( ! slide ) {
				return track.clientWidth * 0.8;
			}
			var gap = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
			return slide.getBoundingClientRect().width + gap;
		}

		function update() {
			var max = track.scrollWidth - track.clientWidth - 2;
			prev.disabled = track.scrollLeft <= 2;
			next.disabled = track.scrollLeft >= max;
		}

		function go( direction ) {
			track.scrollBy( { left: direction * step(), behavior: reduceMotion ? 'auto' : 'smooth' } );
		}

		prev.addEventListener( 'click', function () {
			go( -1 );
		} );
		next.addEventListener( 'click', function () {
			go( 1 );
		} );
		track.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'ArrowRight' ) {
				go( 1 );
				event.preventDefault();
			} else if ( event.key === 'ArrowLeft' ) {
				go( -1 );
				event.preventDefault();
			}
		} );
		track.addEventListener( 'scroll', function () {
			window.requestAnimationFrame( update );
		}, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	} );
}() );
