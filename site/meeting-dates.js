/**
 * Keeps "next meeting" dates current in the static previews.
 *
 * The previews are built once, so any date worked out at build time goes
 * stale. This recalculates the group's meeting pattern in the visitor's
 * browser (last Saturday of the month, except May, August and December) and
 * rewrites the dates each option shows. The WordPress themes do the same
 * calculation on the server, so they do not need this file.
 *
 * Added to every preview page by scripts/build-site.mjs.
 */
(function () {
	'use strict';

	var SKIP = [5, 8, 12];
	var DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

	var now = new Date();
	var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

	function meetings(count) {
		var out = [];
		for (var i = 0; i < 24 && out.length < count; i++) {
			var last = new Date(today.getFullYear(), today.getMonth() + i + 1, 0);
			if (SKIP.indexOf(last.getMonth() + 1) > -1) {
				continue;
			}
			last.setDate(last.getDate() - ((last.getDay() + 1) % 7));
			if (last >= today) {
				out.push(last);
			}
		}
		return out;
	}

	function iso(d) {
		return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
	}

	function set(root, selector, text) {
		var el = root.querySelector(selector);
		if (el) {
			el.textContent = text;
		}
	}

	function setTime(root, d) {
		var time = root.matches('time') ? root : root.querySelector('time');
		if (time) {
			time.setAttribute('datetime', iso(d));
		}
	}

	var dates = meetings(3);
	var next = dates[0];
	var isToday = iso(next) === iso(today);

	// Options A and B: the meeting panel.
	document.querySelectorAll('.cfhg-date').forEach(function (el) {
		set(el, '.cfhg-date__day', DAYS[next.getDay()]);
		set(el, '.cfhg-date__num', String(next.getDate()));
		set(el, '.cfhg-date__month', MONTHS[next.getMonth()] + ' ' + next.getFullYear());
		set(el, '.cfhg-date__rest', next.getDate() + ' ' + MONTHS[next.getMonth()]);
		setTime(el, next);
	});

	// Options A and B: the line in the menu and hero, and the footer date.
	document.querySelectorAll('.cfhg-next strong, .cfhg-footer__date').forEach(function (el) {
		el.textContent = DAYS[next.getDay()] + ' ' + next.getDate() + ' ' + MONTHS[next.getMonth()];
	});

	// Option C: the index card.
	document.querySelectorAll('.cfhg-c-card-index').forEach(function (card) {
		var time = card.querySelector('.cfhg-c-card-index__date time');
		if (time) {
			time.textContent = DAYS[next.getDay()] + ' ' + next.getDate() + ' ' + MONTHS[next.getMonth()] + ' ' + next.getFullYear();
			time.setAttribute('datetime', iso(next));
		}
		var badge = card.querySelector('.cfhg-c-card-index__today');
		if (badge && !isToday) {
			badge.remove();
		} else if (!badge && isToday) {
			var heading = card.querySelector('.cfhg-c-card-index__heading');
			if (heading) {
				var span = document.createElement('span');
				span.className = 'cfhg-c-card-index__today';
				span.textContent = 'Today';
				heading.appendChild(span);
			}
		}
	});

	// Option C: the programme of coming meetings.
	document.querySelectorAll('.cfhg-c-programme').forEach(function (list) {
		var items = list.querySelectorAll('.cfhg-c-programme__item:not(.cfhg-c-programme__item--tbc)');
		items.forEach(function (item, i) {
			var d = dates[i];
			if (!d) {
				return;
			}
			set(item, '.cfhg-c-programme__day', String(d.getDate()));
			set(item, '.cfhg-c-programme__month', MONTHS[d.getMonth()].slice(0, 3) + ' ' + d.getFullYear());
			setTime(item, d);
		});
	});
})();
