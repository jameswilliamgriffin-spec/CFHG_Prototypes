/**
 * CFHG Site: the surname filter, the parish explorer and "Where should I
 * look?". Plain JavaScript, no dependencies. Each part finds its own markup
 * (data-cfhg-*), so anything not on the page is skipped. Without this file
 * everything still reads: every parish is listed in full and the record
 * sources are on the page.
 */
( function () {
	'use strict';

	var each = function ( sel, fn, root ) {
		Array.prototype.forEach.call( ( root || document ).querySelectorAll( sel ), fn );
	};
	var esc = function ( s ) {
		var d = document.createElement( 'div' );
		d.textContent = String( s == null ? '' : s );
		return d.innerHTML;
	};
	var fill = function ( text ) {
		var args = Array.prototype.slice.call( arguments, 1 );
		var i = 0;
		return String( text ).replace( /%(\d\$)?s/g, function ( m, pos ) {
			return pos ? args[ parseInt( pos, 10 ) - 1 ] : args[ i++ ];
		} );
	};

	/* 1. Surnames: filter the table as you type. */
	each( '[data-cfhg-surnames]', function ( box ) {
		var input = box.querySelector( '[data-cfhg-surnames-filter]' );
		var none = box.querySelector( '.cfhg-surnames__none' );
		if ( ! input ) {
			return;
		}
		input.addEventListener( 'input', function () {
			var q = input.value.trim().toLowerCase();
			var shown = 0;
			each( 'tbody tr', function ( row ) {
				var hit = ! q || row.textContent.toLowerCase().indexOf( q ) !== -1;
				row.hidden = ! hit;
				shown += hit ? 1 : 0;
			}, box );
			if ( none ) {
				none.hidden = shown > 0;
			}
		} );
	} );

	/* 2. Parish explorer: one parish open at a time, chosen from the list or the map. */
	each( '[data-cfhg-parishes]', function ( box ) {
		var buttons = box.querySelectorAll( '.cfhg-parishes__btn' );
		var panels = box.querySelectorAll( '.cfhg-parish' );
		var paths = box.querySelectorAll( '[data-parish-path]' );
		box.classList.add( 'is-ready' );

		function choose( slug, focusPanel ) {
			Array.prototype.forEach.call( panels, function ( p ) {
				p.hidden = p.getAttribute( 'data-parish' ) !== slug;
				if ( ! p.hidden && focusPanel ) {
					p.setAttribute( 'tabindex', '-1' );
					p.focus( { preventScroll: true } );
				}
			} );
			Array.prototype.forEach.call( buttons, function ( b ) {
				b.setAttribute( 'aria-expanded', b.getAttribute( 'data-parish' ) === slug ? 'true' : 'false' );
			} );
			Array.prototype.forEach.call( paths, function ( p ) {
				p.classList.toggle( 'is-active', p.getAttribute( 'data-parish-path' ) === slug );
			} );
		}

		Array.prototype.forEach.call( buttons, function ( b ) {
			b.addEventListener( 'click', function () {
				choose( b.getAttribute( 'data-parish' ), false );
			} );
		} );
		Array.prototype.forEach.call( paths, function ( p ) {
			var slug = p.getAttribute( 'data-parish-path' );
			p.addEventListener( 'click', function () {
				choose( slug, true );
			} );
			p.addEventListener( 'mouseenter', function () {
				box.querySelector( '.cfhg-parishes__btn[data-parish="' + slug + '"]' ).classList.add( 'is-hover' );
			} );
			p.addEventListener( 'mouseleave', function () {
				box.querySelector( '.cfhg-parishes__btn[data-parish="' + slug + '"]' ).classList.remove( 'is-hover' );
			} );
		} );
		Array.prototype.forEach.call( buttons, function ( b ) {
			var slug = b.getAttribute( 'data-parish' );
			b.addEventListener( 'mouseenter', function () {
				each( '[data-parish-path="' + slug + '"]', function ( p ) {
					p.classList.add( 'is-hover' );
				}, box );
			} );
			b.addEventListener( 'mouseleave', function () {
				each( '[data-parish-path="' + slug + '"]', function ( p ) {
					p.classList.remove( 'is-hover' );
				}, box );
			} );
		} );

		/* Shade the parishes where members are researching families. */
		each( '.cfhg-parishes__btn', function ( b ) {
			if ( b.querySelector( '.cfhg-parishes__count' ) ) {
				each( '[data-parish-path="' + b.getAttribute( 'data-parish' ) + '"]', function ( p ) {
					p.classList.add( 'has-names' );
				}, box );
			}
		}, box );

		/* Open the first parish with surnames, or the first parish. */
		var first = box.querySelector( '.cfhg-parishes__btn .cfhg-parishes__count' );
		choose( ( first ? first.parentNode : buttons[ 0 ] ).getAttribute( 'data-parish' ), false );
	} );

	/* 2b. Records timeline: the bars draw out once, when the chart is reached. */
	each( '[data-cfhg-timeline]', function ( box ) {
		if ( ! ( 'IntersectionObserver' in window ) ) {
			return;
		}
		box.classList.add( 'is-ready' );
		var io = new IntersectionObserver( function ( entries ) {
			if ( entries[ 0 ].isIntersecting ) {
				io.disconnect();
				box.classList.add( 'is-in' );
			}
		}, { rootMargin: '0px 0px -20% 0px' } );
		io.observe( box );
	} );

	/* 3. "Where should I look?" */
	each( '[data-cfhg-finder]', function ( box ) {
		var raw = box.querySelector( '.cfhg-finder__data' );
		var form = box.querySelector( 'form' );
		var out = box.querySelector( '.cfhg-finder__results' );
		var data;
		try {
			data = JSON.parse( raw.textContent );
		} catch ( e ) {
			return;
		}
		var t = data.text;

		form.addEventListener( 'submit', function ( ev ) {
			ev.preventDefault();
			var looking = form.querySelector( 'input[type="radio"]:checked' );
			if ( ! looking ) {
				out.innerHTML = '<p class="cfhg-finder__need">' + esc( t.needChoice ) + '</p>';
				return;
			}
			var period = data.periods.filter( function ( p ) {
				return p.id === form.elements.when.value;
			} )[ 0 ];
			var parish = data.parishes.filter( function ( p ) {
				return p.slug === form.elements.parish.value;
			} )[ 0 ];
			var surname = form.elements.surname.value.trim();

			var hits = data.records.filter( function ( r ) {
				return r.for.indexOf( looking.value ) !== -1 && r.from <= period.to && r.to >= period.from;
			} ).sort( function ( a, b ) {
				return a.rank - b.rank;
			} );

			var html = '';

			/* Someone in the group researching that surname? */
			if ( surname ) {
				var q = surname.toLowerCase();
				data.people.forEach( function ( person ) {
					var match = person.names.some( function ( n ) {
						return n.toLowerCase().replace( /\(|\)/g, '' ) === q || n.toLowerCase().split( /[ (]/ )[ 0 ] === q;
					} );
					if ( match ) {
						html += '<p class="cfhg-finder__match"><strong>' + esc( fill( t.match, person.label, person.parish || t.matchAny ) ) + '</strong> '
							+ ( data.mail ? '<a href="' + esc( data.mail ) + '">' + esc( t.matchAsk ) + '</a>' : '' ) + '</p>';
					}
				} );
			}

			if ( ! hits.length ) {
				html += '<p class="cfhg-finder__none">' + esc( t.none ) + '</p>';
			} else {
				html += '<ol class="cfhg-finder__list">';
				hits.forEach( function ( r, i ) {
					html += '<li class="cfhg-finder__item' + ( i === 0 ? ' is-first' : '' ) + '">'
						+ ( i === 0 ? '<span class="cfhg-finder__badge">' + esc( t.tryFirst ) + '</span>' : '' )
						+ '<h3 class="cfhg-finder__title">' + esc( r.title ) + '</h3>'
						+ '<p>' + esc( r.why ) + '</p>'
						+ '<p class="cfhg-finder__where"><strong>' + esc( t.where ) + '</strong> ' + esc( r.where ) + '</p>'
						+ ( r.url ? '<p class="cfhg-finder__link"><a href="' + esc( r.url ) + '" rel="noopener">' + esc( t.open + ' ' + r.title ) + '</a></p>' : '' )
						+ '</li>';
				} );
				html += '</ol>';
			}

			/* Old parish names for anything before the 1867 merger. */
			if ( parish && parish.old.length && period.from < 1867 ) {
				html += '<p class="cfhg-finder__tip">' + esc( fill( t.oldNames, parish.old.join( ', ' ) ) ) + '</p>';
			}
			html += '<p class="cfhg-finder__always">' + esc( data.always ) + '</p>';

			out.innerHTML = html;
			out.setAttribute( 'tabindex', '-1' );
			out.focus( { preventScroll: true } );
			out.scrollIntoView( { behavior: window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 'auto' : 'smooth', block: 'start' } );
		} );
	} );
}() );
