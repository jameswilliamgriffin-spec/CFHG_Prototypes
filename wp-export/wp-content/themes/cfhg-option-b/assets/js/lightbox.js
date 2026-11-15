/**
 * CFHG Option B: video lightbox. Plain JavaScript, no dependencies.
 *
 * Any link with the class cfhg-video-open and a video file as its address
 * opens that film in a dialog on top of the page. Without this script (or in
 * a browser with no <dialog>) the link simply opens the file, so nothing is
 * lost. Because it works from a class, it works on Elementor's free button
 * and shortcode widgets too.
 *
 * Esc, the close button and a click outside the film all close it. The film
 * is stopped and unloaded on close, and focus goes back to the link.
 */
( function () {
	'use strict';

	if ( ! window.HTMLDialogElement ) {
		return;
	}

	var dialog = null;
	var film = null;

	function build() {
		dialog = document.createElement( 'dialog' );
		dialog.className = 'cfhg-lightbox';
		dialog.setAttribute( 'aria-label', 'Video' );

		var close = document.createElement( 'button' );
		close.type = 'button';
		close.className = 'cfhg-lightbox__close';
		close.setAttribute( 'aria-label', 'Close the video' );
		close.innerHTML = '<svg viewBox="0 0 20 20" width="20" height="20" aria-hidden="true"><path d="M4 4l12 12M16 4L4 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
		close.addEventListener( 'click', function () {
			dialog.close();
		} );

		film = document.createElement( 'video' );
		film.controls = true;
		film.playsInline = true;
		film.preload = 'none';

		dialog.appendChild( close );
		dialog.appendChild( film );
		document.body.appendChild( dialog );

		/* A click on the dark backdrop (the dialog itself) closes it. */
		dialog.addEventListener( 'click', function ( e ) {
			if ( e.target === dialog ) {
				dialog.close();
			}
		} );
		dialog.addEventListener( 'close', function () {
			film.pause();
			film.removeAttribute( 'src' );
			film.load();
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest ? e.target.closest( 'a.cfhg-video-open, .cfhg-video-open a' ) : null;
		if ( ! link || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button ) {
			return;
		}
		var url = link.getAttribute( 'href' ) || '';
		if ( ! /\.(mp4|webm|mov)(\?|$)/i.test( url ) ) {
			return;
		}
		e.preventDefault();
		if ( ! dialog ) {
			build();
		}
		var poster = link.querySelector( 'img' );
		if ( poster ) {
			film.poster = poster.currentSrc || poster.src;
		} else {
			film.removeAttribute( 'poster' );
		}
		film.src = url;
		dialog.showModal();
		var started = film.play();
		if ( started && started.catch ) {
			started.catch( function () {} );
		}
	} );
}() );
