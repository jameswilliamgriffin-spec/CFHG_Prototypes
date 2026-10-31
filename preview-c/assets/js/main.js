/**
 * Caribbean Family History Group, Option C.
 * Text size, menu panel, island scroller, gentle reveals. No dependencies.
 */
(function () {
	'use strict';

	var root = document.documentElement;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Text size: A- / A / A+ ------------------------------------------- */

	var SIZES = [87.5, 100, 112.5, 125, 137.5, 150];
	var STORE = 'cfhg-c-text-size';

	function readSize() {
		var saved = null;
		try {
			saved = window.localStorage.getItem(STORE);
		} catch (e) {}
		var value = parseFloat(saved);
		return SIZES.indexOf(value) > -1 ? value : 100;
	}

	function applySize(value, announce) {
		root.style.fontSize = value === 100 ? '' : value + '%';
		try {
			if (value === 100) {
				window.localStorage.removeItem(STORE);
			} else {
				window.localStorage.setItem(STORE, String(value));
			}
		} catch (e) {}

		var index = SIZES.indexOf(value);
		document.querySelectorAll('[data-cfhg-c-textsize]').forEach(function (group) {
			group.querySelector('[data-cfhg-c-size="down"]').setAttribute('aria-disabled', index === 0 ? 'true' : 'false');
			group.querySelector('[data-cfhg-c-size="up"]').setAttribute('aria-disabled', index === SIZES.length - 1 ? 'true' : 'false');
			group.querySelector('[data-cfhg-c-size="reset"]').setAttribute('aria-pressed', value === 100 ? 'true' : 'false');
			var status = group.querySelector('[data-cfhg-c-size-status]');
			if (status && announce) {
				status.textContent = (status.getAttribute('data-label') || 'Text size %s').replace('%s', value + '%');
			}
		});
	}

	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-cfhg-c-size]');
		if (!button || button.getAttribute('aria-disabled') === 'true') {
			return;
		}
		var index = SIZES.indexOf(readSize());
		var action = button.getAttribute('data-cfhg-c-size');
		var next = 100;
		if (action === 'down') {
			next = SIZES[Math.max(0, index - 1)];
		} else if (action === 'up') {
			next = SIZES[Math.min(SIZES.length - 1, index + 1)];
		}
		applySize(next, true);
		updateScrollers();
	});

	applySize(readSize(), false);

	/* Menu panel ------------------------------------------------------- */

	var toggle = document.querySelector('[data-cfhg-c-menu-toggle]');
	var panel = document.querySelector('[data-cfhg-c-panel]');

	function setMenu(open, returnFocus) {
		if (!toggle || !panel) {
			return;
		}
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.hidden = !open;
		var label = toggle.querySelector('.cfhg-c-menu-toggle__label');
		if (label) {
			label.textContent = label.getAttribute(open ? 'data-close-label' : 'data-open-label');
		}
		if (open) {
			var first = panel.querySelector('a, button');
			if (first) {
				first.focus();
			}
		} else if (returnFocus) {
			toggle.focus();
		}
	}

	if (toggle && panel) {
		toggle.addEventListener('click', function () {
			setMenu(toggle.getAttribute('aria-expanded') !== 'true', false);
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
				setMenu(false, true);
			}
		});

		window.addEventListener('resize', function () {
			if (window.matchMedia('(min-width: 64em)').matches && !panel.hidden) {
				setMenu(false, false);
			}
		});
	}

	/* Dropdowns: let Escape close a focused submenu on desktop. -------- */

	document.querySelectorAll('.cfhg-c-nav__list .menu-item-has-children > a').forEach(function (link) {
		link.setAttribute('aria-haspopup', 'true');
	});

	/* Island scroller -------------------------------------------------- */

	var scrollers = [];

	function step(track) {
		var item = track.querySelector('.cfhg-c-scroller__item');
		if (!item) {
			return track.clientWidth * 0.8;
		}
		var gap = parseFloat(getComputedStyle(item.parentElement).columnGap) || 0;
		var perView = Math.max(1, Math.floor((track.clientWidth + gap) / (item.offsetWidth + gap)) - 1);
		return perView * (item.offsetWidth + gap);
	}

	function updateScroller(s) {
		var max = s.track.scrollWidth - s.track.clientWidth - 2;
		s.prev.setAttribute('aria-disabled', s.track.scrollLeft <= 2 ? 'true' : 'false');
		s.next.setAttribute('aria-disabled', s.track.scrollLeft >= max ? 'true' : 'false');
	}

	function updateScrollers() {
		scrollers.forEach(updateScroller);
	}

	document.querySelectorAll('[data-cfhg-c-scroller]').forEach(function (el) {
		var s = {
			track: el.querySelector('[data-cfhg-c-track]'),
			prev: el.querySelector('[data-cfhg-c-prev]'),
			next: el.querySelector('[data-cfhg-c-next]')
		};
		if (!s.track || !s.prev || !s.next) {
			return;
		}
		scrollers.push(s);

		function go(direction) {
			s.track.scrollBy({
				left: direction * step(s.track),
				behavior: reduceMotion ? 'auto' : 'smooth'
			});
		}

		s.prev.addEventListener('click', function () {
			if (s.prev.getAttribute('aria-disabled') !== 'true') {
				go(-1);
			}
		});
		s.next.addEventListener('click', function () {
			if (s.next.getAttribute('aria-disabled') !== 'true') {
				go(1);
			}
		});

		s.track.addEventListener('keydown', function (event) {
			if (event.target !== s.track) {
				return;
			}
			if (event.key === 'ArrowRight') {
				event.preventDefault();
				go(1);
			} else if (event.key === 'ArrowLeft') {
				event.preventDefault();
				go(-1);
			}
		});

		var ticking = false;
		s.track.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(function () {
					updateScroller(s);
					ticking = false;
				});
			}
		}, { passive: true });

		updateScroller(s);
	});

	window.addEventListener('resize', updateScrollers);

	/* Reveal on scroll ------------------------------------------------- */

	var reveals = document.querySelectorAll('[data-cfhg-c-reveal]');

	if (reveals.length && !reduceMotion && 'IntersectionObserver' in window) {
		root.classList.add('cfhg-c-reveal-ready');
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

		reveals.forEach(function (el) {
			observer.observe(el);
		});
	}
})();
