#!/usr/bin/env node
/**
 * Static design preview for Option C.
 *
 *   npm run build:preview-c
 *
 * Reads the theme's own copy (inc/content.json) and image manifest, renders
 * the same markup as the PHP templates, and copies the theme's assets into
 * /preview-c. Nothing here is shared with the Option A or B builders.
 *
 * Pages: homepage, islands index, one page per island, membership (inner
 * page), privacy, news archive, one page per post, 404.
 */

import { readFileSync, writeFileSync, mkdirSync, rmSync, cpSync, existsSync, readdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const THEME = join(ROOT, 'wordpress', 'caribbean-family-history-c');
const OUT = join(ROOT, 'preview-c');
const MARKER = '.cfhg-preview-c-build';

const C = JSON.parse(readFileSync(join(THEME, 'inc', 'content.json'), 'utf8'));
const MANIFEST = JSON.parse(readFileSync(join(THEME, 'assets', 'images', 'manifest.json'), 'utf8'));

/* -------------------------------------------------------------------------
 * Drift guard: the preview copies the templates' markup by hand, so check
 * that the classes it relies on still exist in the PHP.
 * ---------------------------------------------------------------------- */

const HOOKS = {
	'template-parts/site-header.php': ['cfhg-c-header__top', 'cfhg-c-header__tools', 'cfhg-c-nav__list', 'data-cfhg-c-panel', 'data-cfhg-c-menu-toggle', 'cfhg-c-panel__row'],
	'template-parts/site-footer.php': ['cfhg-c-footer__grid', 'cfhg-c-footer__news', 'id="newsletter"', 'id="contact"', 'cfhg-c-footer__base', 'cfhg-c-footer__legal'],
	'inc/template-tags.php': ['cfhg-c-textsize', 'data-cfhg-c-size-status', 'cfhg-c-btn--', 'cfhg-c-btn__arrow', 'cfhg-c-ring', 'cfhg-c-pagehead__standfirst', 'cfhg-c-newsletter__row'],
	'template-parts/home/hero.php': ['cfhg-c-hero__grid', 'cfhg-c-hero__frame', 'cfhg-c-hero__ring', 'fetchpriority'],
	'template-parts/components/meeting-card.php': ['cfhg-c-card-index__date', 'cfhg-c-card-index__today', 'cfhg-c-card-index__list'],
	'template-parts/home/intro.php': ['cfhg-c-intro__statement', 'cfhg-c-intro__body'],
	'template-parts/home/islands.php': ['cfhg-c-band__head', 'cfhg-c-band__lead'],
	'template-parts/components/island-scroller.php': ['data-cfhg-c-track', 'cfhg-c-scroller__controls', 'Previous islands', 'Next islands'],
	'template-parts/components/island-card.php': ['cfhg-c-island-card__label', 'cfhg-c-island-card__more'],
	'template-parts/home/ways.php': ['cfhg-c-way--meet', 'cfhg-c-way--research', 'cfhg-c-way--share', 'cfhg-c-facts', 'cfhg-c-index'],
	'template-parts/components/programme.php': ['cfhg-c-programme__day', 'cfhg-c-programme__item--tbc'],
	'template-parts/home/support.php': ['cfhg-c-support__cloth', 'cfhg-c-support__note'],
	'template-parts/home/news.php': ['cfhg-c-news__grid', 'cfhg-c-section__head--split'],
	'template-parts/components/post-card.php': ['cfhg-c-post-card--', 'cfhg-c-post-card__excerpt'],
	'single-cfhg_c_island.php': ['cfhg-c-island__grid', 'cfhg-c-island__figure', 'Other islands'],
	'single.php': ['cfhg-c-article__standfirst', 'cfhg-c-article__figure', 'cfhg-c-article__foot'],
	'page.php': ['cfhg-c-page__aside', 'cfhg-c-prose'],
	'404.php': ['cfhg-c-notfound__grid', 'cfhg-c-notfound__figure'],
	'archive-cfhg_c_island.php': ['cfhg-c-island-grid'],
	'template-parts/content/post-list.php': ['cfhg-c-archive'],
};

const drift = [];
for (const [file, needles] of Object.entries(HOOKS)) {
	const src = readFileSync(join(THEME, file), 'utf8');
	for (const needle of needles) {
		if (!src.includes(needle)) drift.push(`${file}: missing "${needle}"`);
	}
}
if (drift.length) {
	console.error('Preview is out of step with the theme templates:\n  ' + drift.join('\n  '));
	process.exit(1);
}

/* -------------------------------------------------------------------------
 * Links: theme paths to preview files. Unknown paths stop the build.
 * ---------------------------------------------------------------------- */

const LINK_MAP = {
	'/': 'index.html',
	'/islands/': 'islands.html',
	'/research/': 'islands.html',
	'/whats-on/': 'index.html#cfhg-c-whatson-heading',
	'/news/': 'news.html',
	'/membership/': 'membership.html',
	'/support-us/': 'membership.html',
	'/about/': 'index.html#cfhg-c-intro-heading',
	'/contact/': '#contact',
	'/privacy-policy/': 'privacy.html',
};
for (const island of C.islands) LINK_MAP[`/islands/${island.slug}/`] = `island-${island.slug}.html`;
for (const post of C.posts) LINK_MAP[`/news/${post.slug}/`] = `news-${post.slug}.html`;

function link(url) {
	if (/\.html(#.*)?$/.test(url) || /^(https?:|mailto:)/.test(url)) return url;
	if (url.startsWith('#')) return url;
	const [path, hash] = url.split('#');
	if (!(path in LINK_MAP)) throw new Error(`No preview page for link "${url}"`);
	const target = LINK_MAP[path];
	return hash ? `${target.split('#')[0]}#${hash}` : target;
}

/* -------------------------------------------------------------------------
 * Helpers mirroring inc/template-tags.php and inc/content.php
 * ---------------------------------------------------------------------- */

const esc = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

function button(label, url, variant = 'outline', { size = 'lg', arrow = true } = {}) {
	return `<a class="cfhg-c-btn cfhg-c-btn--${variant} cfhg-c-btn--${size}" href="${esc(link(url))}"><span>${esc(label)}</span>${arrow ? '<span class="cfhg-c-btn__arrow" aria-hidden="true">&rarr;</span>' : ''}</a>`;
}

const eyebrow = (text, tag = 'p') => (text ? `<${tag} class="cfhg-c-eyebrow">${esc(text)}</${tag}>` : '');
const ring = (cls = '') => `<div class="${('cfhg-c-ring ' + cls).trim()}" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span></div>`;
const arrowLink = (label, url) => `<a class="cfhg-c-link-arrow" href="${esc(link(url))}">${esc(label)}<span aria-hidden="true"> &rarr;</span></a>`;

function themeImage(key, alt, { cls = '', sizes = '100vw', loading = 'lazy', fetchpriority = '' } = {}) {
	const m = MANIFEST[key];
	if (!m) throw new Error(`Image "${key}" is not in the manifest`);
	const set = Object.entries(MANIFEST)
		.filter(([k]) => k === key || (k.startsWith(key + '-') && /^\d+$/.test(k.slice(key.length + 1))))
		.map(([k, v]) => [v.w, `assets/images/${k}.webp ${v.w}w`])
		.sort((a, b) => a[0] - b[0])
		.map(([, s]) => s);
	let html = `<img src="assets/images/${key}.webp" alt="${esc(alt)}" width="${m.w}" height="${m.h}" loading="${loading}" decoding="async"`;
	if (set.length > 1) html += ` srcset="${esc(set.join(', '))}" sizes="${esc(sizes)}"`;
	if (cls) html += ` class="${cls}"`;
	if (fetchpriority) html += ` fetchpriority="${fetchpriority}"`;
	return html + '>';
}

function renderBlocks(blocks) {
	return blocks
		.map(([type, value, id]) => {
			if (type === 'h2') return `<h2 class="wp-block-heading"${id ? ` id="${esc(id)}"` : ''}>${esc(value)}</h2>`;
			if (type === 'ul') return `<ul class="wp-block-list">${value.map((li) => `<li>${esc(li)}</li>`).join('')}</ul>`;
			if (type === 'quote') return `<blockquote class="wp-block-quote"><p>${esc(value)}</p></blockquote>`;
			return `<p>${esc(value)}</p>`;
		})
		.join('\n');
}

/* Meeting dates: last Saturday of the month, not May, August or December. */
const SKIP = [5, 8, 12];
const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function meetingDates(count) {
	const now = new Date();
	const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
	const dates = [];
	for (let i = 0; i < 24 && dates.length < count; i++) {
		const y = today.getFullYear();
		const m = today.getMonth() + i;
		const last = new Date(y, m + 1, 0);
		if (SKIP.includes(last.getMonth() + 1)) continue;
		last.setDate(last.getDate() - ((last.getDay() + 1) % 7));
		if (last >= today) dates.push(last);
	}
	return dates;
}
const iso = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
const longDate = (d) => `${DAYS[d.getDay()]} ${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`;
const isToday = (d) => iso(d) === iso(new Date());
const postDate = (s) => {
	const [y, m, d] = s.split('-').map(Number);
	return `${d} ${MONTHS[m - 1]} ${y}`;
};

/* -------------------------------------------------------------------------
 * Shared parts
 * ---------------------------------------------------------------------- */

function menu(items, id, cls, current) {
	let marked = false;
	return `<ul id="${id}" class="${cls}">${items
		.map((it) => {
			const on = !marked && current && link(it.url) === current;
			if (on) marked = true;
			return `<li class="menu-item${on ? ' current-menu-item' : ''}"><a href="${esc(link(it.url))}"${on ? ' aria-current="page"' : ''}>${esc(it.label)}</a></li>`;
		})
		.join('')}</ul>`;
}

function headerButtons(context) {
	const size = context === 'header' ? 'sm' : 'lg';
	return `<div class="cfhg-c-header-buttons">${button(C.header.donate.label, C.header.donate.url, 'outline', { size, arrow: false })}${button(C.header.member.label, C.header.member.url, 'gold', { size, arrow: false })}</div>`;
}

const textSize = () => `<div class="cfhg-c-textsize" role="group" aria-label="Text size" data-cfhg-c-textsize>
		<button type="button" class="cfhg-c-textsize__btn cfhg-c-textsize__btn--down" data-cfhg-c-size="down"><span aria-hidden="true">A-</span><span class="screen-reader-text">Smaller text</span></button>
		<button type="button" class="cfhg-c-textsize__btn cfhg-c-textsize__btn--reset" data-cfhg-c-size="reset"><span aria-hidden="true">A</span><span class="screen-reader-text">Default text size</span></button>
		<button type="button" class="cfhg-c-textsize__btn cfhg-c-textsize__btn--up" data-cfhg-c-size="up"><span aria-hidden="true">A+</span><span class="screen-reader-text">Larger text</span></button>
		<span class="screen-reader-text" aria-live="polite" data-cfhg-c-size-status data-label="Text size %s"></span>
	</div>`;

const logo = (tone = 'dark') =>
	`<a class="cfhg-c-logo" href="index.html" rel="home"><img src="assets/images/${tone === 'light' ? 'cfhg-c-logo-light.png' : 'cfhg-c-logo.png'}" alt="${esc(C.site.name)}" width="271" height="80"></a>`;

function header(current) {
	return `<header class="cfhg-c-header" data-cfhg-c-header>
	<div class="cfhg-c-header__top">
		<div class="cfhg-c-container cfhg-c-header__inner">
			${logo()}
			<div class="cfhg-c-header__tools">
				<nav class="cfhg-c-utility" aria-label="Utility">${menu(C.header.utility, 'cfhg-c-utility-menu', 'cfhg-c-utility__list')}</nav>
				${headerButtons('header')}
				${textSize()}
			</div>
			<button class="cfhg-c-menu-toggle" type="button" aria-expanded="false" aria-controls="cfhg-c-panel" data-cfhg-c-menu-toggle>
				<span class="cfhg-c-menu-toggle__bars" aria-hidden="true"><span></span><span></span></span>
				<span class="cfhg-c-menu-toggle__label" data-open-label="Menu" data-close-label="Close">Menu</span>
			</button>
		</div>
	</div>
	<nav class="cfhg-c-nav" aria-label="Main">
		<div class="cfhg-c-container">${menu(C.header.primary, 'cfhg-c-primary-menu', 'cfhg-c-nav__list', current)}</div>
	</nav>
	<div class="cfhg-c-panel" id="cfhg-c-panel" hidden data-cfhg-c-panel>
		<div class="cfhg-c-container">
			<nav aria-label="Main">${menu(C.header.primary, 'cfhg-c-panel-menu', 'cfhg-c-panel__list', current)}</nav>
			${headerButtons('panel')}
			<div class="cfhg-c-panel__row">
				<nav aria-label="Utility">${menu(C.header.utility, 'cfhg-c-panel-utility', 'cfhg-c-panel__utility')}</nav>
				${textSize()}
			</div>
		</div>
	</div>
</header>`;
}

function footer() {
	const s = C.site;
	return `<footer class="cfhg-c-footer">
	${ring()}
	<div class="cfhg-c-container">
		<div class="cfhg-c-footer__grid">
			<div class="cfhg-c-footer__about">
				${logo('light')}
				<p>${esc(C.footer.about)}</p>
			</div>
			<div class="cfhg-c-footer__col">
				<h2 class="cfhg-c-footer__heading">Meetings</h2>
				<address>${esc(s.venue_room)}<br>${s.address.map(esc).join('<br>')}</address>
				<p>${esc(s.meeting_rule)}. Also on Zoom.</p>
			</div>
			<div class="cfhg-c-footer__col" id="contact">
				<h2 class="cfhg-c-footer__heading">Contact</h2>
				<p><a href="mailto:${esc(s.email)}">${esc(s.email)}</a></p>
				<nav aria-label="Footer">${menu(C.header.primary, 'cfhg-c-footer-menu', 'cfhg-c-footer__links')}</nav>
			</div>
			<div class="cfhg-c-footer__col cfhg-c-footer__news" id="newsletter">
				<h2 class="cfhg-c-footer__heading">${esc(C.footer.newsletter_heading)}</h2>
				<p>${esc(C.footer.newsletter_body)}</p>
				<form class="cfhg-c-newsletter" action="mailto:${esc(s.email)}?subject=Newsletter%20sign-up" method="post" enctype="text/plain">
					<label for="cfhg-c-footer-news-email">Email address</label>
					<div class="cfhg-c-newsletter__row">
						<input id="cfhg-c-footer-news-email" type="email" name="email" autocomplete="email" required>
						<button type="submit" class="cfhg-c-btn cfhg-c-btn--gold cfhg-c-btn--sm">${esc(C.footer.newsletter_button)}</button>
					</div>
				</form>
			</div>
		</div>
		<div class="cfhg-c-footer__base">
			<p>${esc(C.footer.partners)}</p>
			<ul class="cfhg-c-footer__legal">
				<li>&copy; ${new Date().getFullYear()} ${esc(s.name)}</li>
				${C.footer.legal.map((l) => `<li><a href="${esc(link(l.url))}">${esc(l.label)}</a></li>`).join('')}
			</ul>
			<p class="cfhg-c-preview-label">Design preview: Option C</p>
		</div>
	</div>
</footer>`;
}

function page({ file, title, description, current, body }) {
	const html = `<!doctype html>
<html lang="en-GB">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>${esc(title)}</title>
	<meta name="description" content="${esc(description)}">
	<link rel="preload" href="assets/fonts/newsreader-latin-var.woff2" as="font" type="font/woff2" crossorigin>
	<script>(function(d){d.documentElement.classList.add('cfhg-c-js');try{var s=localStorage.getItem('cfhg-c-text-size');if(s&&/^\\d{2,3}(\\.\\d)?$/.test(s)){d.documentElement.style.fontSize=s+'%';}}catch(e){}})(document);</script>
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="icon" href="assets/images/cfhg-c-logo.png">
	<script src="assets/js/main.js" defer></script>
</head>
<body class="cfhg-c">
<a class="cfhg-c-skip" href="#cfhg-c-main">Skip to content</a>
${header(current)}
<main id="cfhg-c-main" class="cfhg-c-main" tabindex="-1">
${body}
</main>
${footer()}
</body>
</html>
`;
	writeFileSync(join(OUT, file), html);
	return file;
}

/* -------------------------------------------------------------------------
 * Components
 * ---------------------------------------------------------------------- */

function meetingCard() {
	const next = meetingDates(1)[0];
	const h = C.home.hero;
	return `<aside class="cfhg-c-card-index" aria-labelledby="cfhg-c-next-1">
	<h2 class="cfhg-c-card-index__heading" id="cfhg-c-next-1">${esc(h.card_heading)}${isToday(next) ? ' <span class="cfhg-c-card-index__today">Today</span>' : ''}</h2>
	<p class="cfhg-c-card-index__date"><time datetime="${iso(next)}">${longDate(next)}</time></p>
	<dl class="cfhg-c-card-index__list">
		<div><dt>Time</dt><dd>${esc(C.site.meeting_time)}</dd></div>
		<div><dt>Where</dt><dd>${esc(C.site.meeting_where)}</dd></div>
		<div><dt>Cost</dt><dd>First visit free</dd></div>
	</dl>
	${arrowLink(h.card_link.label, h.card_link.url)}
</aside>`;
}

function islandCard(island) {
	return `<article class="cfhg-c-island-card">
	<p class="cfhg-c-island-card__label">${esc(island.label)}</p>
	<div class="cfhg-c-island-card__media">${themeImage(island.image, '', { cls: 'cfhg-c-island-card__img', sizes: '(min-width: 64em) 22rem, 80vw' })}</div>
	<h3 class="cfhg-c-island-card__name"><a href="${esc(link(`/islands/${island.slug}/`))}">${esc(island.name)}</a></h3>
	<p class="cfhg-c-island-card__summary">${esc(island.summary)}</p>
	<p class="cfhg-c-island-card__more" aria-hidden="true">Researching ${esc(island.name)} &rarr;</p>
</article>`;
}

let scrollerCount = 0;
function scroller(islands, headingId, all) {
	const id = `cfhg-c-scroller-${++scrollerCount}`;
	return `<div class="cfhg-c-scroller" data-cfhg-c-scroller>
	<div class="cfhg-c-container cfhg-c-scroller__bar">
		<div class="cfhg-c-scroller__controls">
			<button type="button" class="cfhg-c-scroller__btn" data-cfhg-c-prev aria-controls="${id}"><span aria-hidden="true">&larr;</span><span class="screen-reader-text">Previous islands</span></button>
			<button type="button" class="cfhg-c-scroller__btn" data-cfhg-c-next aria-controls="${id}"><span aria-hidden="true">&rarr;</span><span class="screen-reader-text">Next islands</span></button>
		</div>
		${all ? arrowLink(all.label, all.url) : ''}
	</div>
	<div class="cfhg-c-scroller__track" id="${id}" role="region" tabindex="0" aria-labelledby="${headingId}" data-cfhg-c-track>
		<ul class="cfhg-c-scroller__list">
			${islands.map((i) => `<li class="cfhg-c-scroller__item">${islandCard(i)}</li>`).join('\n')}
		</ul>
	</div>
</div>`;
}

function postMeta(post) {
	return `<p class="cfhg-c-meta"><time datetime="${post.date}">${postDate(post.date)}</time><span class="cfhg-c-meta__sep" aria-hidden="true"></span><span>${esc(post.category)}</span></p>`;
}

function postCard(post, variant, tag = 'h3') {
	const img =
		variant === 'lead'
			? themeImage(post.image, '', { sizes: '(min-width: 64em) 40rem, 100vw' })
			: themeImage(post.image, '', { sizes: '8rem' });
	return `<article class="cfhg-c-post-card cfhg-c-post-card--${variant}">
	<div class="cfhg-c-post-card__media">${img}</div>
	<div class="cfhg-c-post-card__body">
		${postMeta(post)}
		<${tag} class="cfhg-c-post-card__title"><a href="${esc(link(`/news/${post.slug}/`))}">${esc(post.title)}</a></${tag}>
		${variant === 'lead' ? `<p class="cfhg-c-post-card__excerpt">${esc(post.excerpt)}</p>` : ''}
	</div>
</article>`;
}

function pageHeader(title, eyebrowText = '', standfirst = '') {
	return `<header class="cfhg-c-pagehead">
	<div class="cfhg-c-container">
		${eyebrow(eyebrowText)}
		<h1 class="cfhg-c-pagehead__title">${esc(title)}</h1>
		${standfirst ? `<p class="cfhg-c-pagehead__standfirst">${esc(standfirst)}</p>` : ''}
	</div>
	${ring('cfhg-c-ring--thin')}
</header>`;
}

function programme() {
	const wo = C.home.whats_on;
	const meetings = meetingDates(3).map(
		(d) => `<li class="cfhg-c-programme__item">
		<p class="cfhg-c-programme__date"><time datetime="${iso(d)}"><span class="cfhg-c-programme__day">${d.getDate()}</span><span class="cfhg-c-programme__month">${MONTHS[d.getMonth()].slice(0, 3)} ${d.getFullYear()}</span></time></p>
		<div class="cfhg-c-programme__body">
			<p class="cfhg-c-programme__label">${esc(wo.meeting_label)}</p>
			<h3 class="cfhg-c-programme__title">${esc(wo.meeting_title)}</h3>
			<p class="cfhg-c-programme__where">${DAYS[d.getDay()]}, ${esc(C.site.meeting_time)}. ${esc(C.site.meeting_where)}</p>
		</div>
	</li>`
	);
	const extras = wo.extras.map(
		(x) => `<li class="cfhg-c-programme__item cfhg-c-programme__item--tbc">
		<p class="cfhg-c-programme__date"><span class="cfhg-c-programme__month">${esc(x.when)}</span></p>
		<div class="cfhg-c-programme__body">
			<p class="cfhg-c-programme__label">${esc(x.label)}</p>
			<h3 class="cfhg-c-programme__title">${esc(x.title)}</h3>
			<p class="cfhg-c-programme__where">${esc(x.where)}</p>
		</div>
	</li>`
	);
	return `<ol class="cfhg-c-programme">${[...meetings, ...extras].join('\n')}</ol>`;
}

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

function homePage() {
	const h = C.home;
	const hero = `<section class="cfhg-c-hero" aria-labelledby="cfhg-c-hero-title">
	<div class="cfhg-c-container cfhg-c-hero__grid">
		<div class="cfhg-c-hero__text">
			${eyebrow(h.hero.eyebrow)}
			<h1 class="cfhg-c-hero__title" id="cfhg-c-hero-title">${esc(h.hero.title)}</h1>
			<p class="cfhg-c-hero__lead">${esc(h.hero.lead)}</p>
			<div class="cfhg-c-actions">${button(h.hero.primary.label, h.hero.primary.url, 'gold')}${button(h.hero.secondary.label, h.hero.secondary.url, 'outline')}</div>
		</div>
		<div class="cfhg-c-hero__media">
			<figure class="cfhg-c-hero__frame">${themeImage(h.hero.image, h.hero.image_alt, { sizes: '(min-width: 64em) 30rem, (min-width: 40em) 60vw, 90vw', loading: 'eager', fetchpriority: 'high' })}</figure>
			${meetingCard()}
		</div>
	</div>
	${ring('cfhg-c-hero__ring')}
</section>`;

	const intro = `<section class="cfhg-c-section cfhg-c-intro" aria-labelledby="cfhg-c-intro-heading">
	<div class="cfhg-c-container cfhg-c-intro__inner" data-cfhg-c-reveal>
		<h2 class="cfhg-c-eyebrow" id="cfhg-c-intro-heading">${esc(h.intro.eyebrow)}</h2>
		<p class="cfhg-c-intro__statement">${esc(h.intro.statement)}</p>
		<p class="cfhg-c-intro__body">${esc(h.intro.body)}</p>
		${button(h.intro.link.label, h.intro.link.url, 'text')}
	</div>
</section>`;

	const islands = `<section class="cfhg-c-section cfhg-c-band" aria-labelledby="cfhg-c-islands-heading">
	<div class="cfhg-c-container">
		<div class="cfhg-c-band__head" data-cfhg-c-reveal>
			<div>${eyebrow(h.islands.eyebrow)}<h2 class="cfhg-c-h2" id="cfhg-c-islands-heading">${esc(h.islands.heading)}</h2></div>
			<p class="cfhg-c-band__lead">${esc(h.islands.lead)}</p>
		</div>
	</div>
	${scroller(C.islands, 'cfhg-c-islands-heading', h.islands.all)}
</section>`;

	const w = h.ways;
	const ways = `<section class="cfhg-c-section cfhg-c-ways" aria-labelledby="cfhg-c-ways-heading">
	<div class="cfhg-c-container">
		<header class="cfhg-c-section__head" data-cfhg-c-reveal>${eyebrow(w.eyebrow)}<h2 class="cfhg-c-h2" id="cfhg-c-ways-heading">${esc(w.heading)}</h2></header>
		<article class="cfhg-c-way cfhg-c-way--meet" data-cfhg-c-reveal>
			<figure class="cfhg-c-way__media">${themeImage(w.meet.image, w.meet.image_alt, { sizes: '(min-width: 64em) 40rem, 100vw' })}</figure>
			<div class="cfhg-c-way__body">
				<p class="cfhg-c-way__num" aria-hidden="true">01</p>
				${eyebrow(w.meet.label)}
				<h3 class="cfhg-c-h3">${esc(w.meet.heading)}</h3>
				<p>${esc(w.meet.body)}</p>
				<dl class="cfhg-c-facts">${w.meet.facts.map((f) => `<div><dt>${esc(f.term)}</dt><dd>${esc(f.detail)}</dd></div>`).join('')}</dl>
				${button(w.meet.link.label, w.meet.link.url, 'solid')}
			</div>
		</article>
		<article class="cfhg-c-way cfhg-c-way--research" data-cfhg-c-reveal>
			<div class="cfhg-c-way__body">
				<p class="cfhg-c-way__num" aria-hidden="true">02</p>
				${eyebrow(w.research.label)}
				<h3 class="cfhg-c-h3">${esc(w.research.heading)}</h3>
				<p>${esc(w.research.body)}</p>
				<ul class="cfhg-c-index">${w.research.links.map((l) => `<li><a href="${esc(link(l.url))}">${esc(l.label)}</a></li>`).join('')}</ul>
			</div>
			<figure class="cfhg-c-way__media">${themeImage(w.research.image, w.research.image_alt, { sizes: '(min-width: 64em) 30rem, 100vw' })}</figure>
		</article>
		<article class="cfhg-c-way cfhg-c-way--share" data-cfhg-c-reveal>
			<figure class="cfhg-c-way__media">${themeImage(w.share.image, w.share.image_alt, { sizes: '(min-width: 64em) 16rem, 60vw' })}</figure>
			<div class="cfhg-c-way__body">
				<p class="cfhg-c-way__num" aria-hidden="true">03</p>
				${eyebrow(w.share.label)}
				<h3 class="cfhg-c-h3">${esc(w.share.heading)}</h3>
				<p>${esc(w.share.body)}</p>
				${button(w.share.link.label, w.share.link.url, 'light')}
			</div>
		</article>
	</div>
</section>`;

	const wo = h.whats_on;
	const whatsOn = `<section class="cfhg-c-section cfhg-c-whatson" aria-labelledby="cfhg-c-whatson-heading">
	<div class="cfhg-c-container cfhg-c-whatson__grid">
		<header class="cfhg-c-whatson__head" data-cfhg-c-reveal>
			${eyebrow(wo.eyebrow)}
			<h2 class="cfhg-c-h2" id="cfhg-c-whatson-heading">${esc(wo.heading)}</h2>
			<p>${esc(wo.lead)}</p>
			${button(wo.all.label, wo.all.url, 'outline')}
		</header>
		${programme()}
	</div>
</section>`;

	const sp = h.support;
	const support = `<section class="cfhg-c-support" aria-labelledby="cfhg-c-support-heading">
	<div class="cfhg-c-support__cloth" aria-hidden="true" style="background-image:url('assets/images/generated/texture-madras.webp')"></div>
	<div class="cfhg-c-container cfhg-c-support__inner" data-cfhg-c-reveal>
		<div>${eyebrow(sp.eyebrow)}<h2 class="cfhg-c-h2" id="cfhg-c-support-heading">${esc(sp.heading)}</h2></div>
		<div class="cfhg-c-support__body">
			<p>${esc(sp.body)}</p>
			<div class="cfhg-c-actions">${button(C.header.member.label, C.header.member.url, 'gold')}${button(C.header.donate.label, C.header.donate.url, 'light')}</div>
			<p class="cfhg-c-support__note">${esc(sp.note)} <a href="${esc(link(sp.note_link.url))}">${esc(sp.note_link.label)}</a></p>
		</div>
	</div>
</section>`;

	const posts = [...C.posts].sort((a, b) => b.date.localeCompare(a.date)).slice(0, 4);
	const news = `<section class="cfhg-c-section cfhg-c-news" aria-labelledby="cfhg-c-news-heading">
	<div class="cfhg-c-container">
		<header class="cfhg-c-section__head cfhg-c-section__head--split" data-cfhg-c-reveal>
			<div>${eyebrow(h.news.eyebrow)}<h2 class="cfhg-c-h2" id="cfhg-c-news-heading">${esc(h.news.heading)}</h2></div>
			${arrowLink(h.news.all.label, h.news.all.url)}
		</header>
		<div class="cfhg-c-news__grid">${posts.map((p, i) => postCard(p, i === 0 ? 'lead' : 'row')).join('\n')}</div>
	</div>
</section>`;

	return page({
		file: 'index.html',
		title: `${C.site.name}: Caribbean genealogy in Solihull, Birmingham and online`,
		description: h.hero.lead,
		current: 'index.html',
		body: [hero, intro, islands, ways, whatsOn, support, news].join('\n'),
	});
}

function islandPage(island) {
	const others = C.islands.filter((i) => i.slug !== island.slug);
	const body = `<article class="cfhg-c-island">
	<header class="cfhg-c-island__head">
		<div class="cfhg-c-container cfhg-c-island__grid">
			<div class="cfhg-c-island__intro">
				<a class="cfhg-c-back" href="islands.html"><span aria-hidden="true">&larr;</span> All islands</a>
				<p class="cfhg-c-island-card__label">${esc(island.label)}</p>
				<h1 class="cfhg-c-island__title">${esc(island.name)}</h1>
				<p class="cfhg-c-island__summary">${esc(island.summary)}</p>
			</div>
			<figure class="cfhg-c-island__figure">${themeImage(island.image, island.image_alt, { sizes: '(min-width: 64em) 40rem, 100vw', loading: 'eager', fetchpriority: 'high' })}</figure>
		</div>
		${ring('cfhg-c-ring--thin')}
	</header>
	<div class="cfhg-c-section cfhg-c-section--tight">
		<div class="cfhg-c-container cfhg-c-page">
			<div class="cfhg-c-prose">${renderBlocks(island.body)}</div>
			<aside class="cfhg-c-page__aside">${meetingCard()}</aside>
		</div>
	</div>
</article>
<section class="cfhg-c-section cfhg-c-band" aria-labelledby="cfhg-c-more-islands">
	<div class="cfhg-c-container"><h2 class="cfhg-c-h2" id="cfhg-c-more-islands">Other islands</h2></div>
	${scroller(others, 'cfhg-c-more-islands', C.home.islands.all)}
</section>`;
	return page({
		file: `island-${island.slug}.html`,
		title: `${island.name}: researching your family | ${C.site.name}`,
		description: island.summary,
		current: 'islands.html',
		body,
	});
}

function islandsIndex() {
	const body = `${pageHeader('Islands', C.home.islands.eyebrow, C.home.islands.lead)}
<div class="cfhg-c-section cfhg-c-section--tight">
	<div class="cfhg-c-container">
		<ul class="cfhg-c-island-grid">${C.islands.map((i) => `<li>${islandCard(i)}</li>`).join('\n')}</ul>
	</div>
</div>`;
	return page({ file: 'islands.html', title: `Islands | ${C.site.name}`, description: C.home.islands.lead, current: 'islands.html', body });
}

function innerPage({ file, title, standfirst, blocks, current, description }) {
	const body = `${pageHeader(title, '', standfirst)}
<div class="cfhg-c-section cfhg-c-section--tight">
	<div class="cfhg-c-container cfhg-c-page">
		<article class="cfhg-c-prose page">${renderBlocks(blocks)}</article>
		<aside class="cfhg-c-page__aside">${meetingCard()}</aside>
	</div>
</div>`;
	return page({ file, title: `${title} | ${C.site.name}`, description: description || standfirst, current, body });
}

function newsArchive() {
	const posts = [...C.posts].sort((a, b) => b.date.localeCompare(a.date));
	const body = `${pageHeader('Stories and news', 'From the group')}
<div class="cfhg-c-section cfhg-c-section--tight">
	<div class="cfhg-c-container">
		<div class="cfhg-c-archive">${posts.map((p, i) => postCard(p, i === 0 ? 'lead' : 'row', 'h2')).join('\n')}</div>
	</div>
</div>`;
	return page({ file: 'news.html', title: `Stories and news | ${C.site.name}`, description: 'Research tips, members’ stories and news from the Caribbean Family History Group.', current: 'news.html', body });
}

function postPage(post, index, posts) {
	const newer = posts[index - 1];
	const older = posts[index + 1];
	const body = `<article class="cfhg-c-article post">
	<header class="cfhg-c-article__head">
		<div class="cfhg-c-container cfhg-c-article__narrow">
			<a class="cfhg-c-back" href="news.html"><span aria-hidden="true">&larr;</span> Stories and news</a>
			${postMeta(post)}
			<h1 class="cfhg-c-article__title">${esc(post.title)}</h1>
			<p class="cfhg-c-article__standfirst">${esc(post.excerpt)}</p>
		</div>
	</header>
	<figure class="cfhg-c-container cfhg-c-article__figure">${themeImage(post.image, post.image_alt, { sizes: '(min-width: 64em) 60rem, 100vw', loading: 'eager' })}</figure>
	<div class="cfhg-c-container cfhg-c-article__narrow cfhg-c-prose">${renderBlocks(post.body)}</div>
	<footer class="cfhg-c-container cfhg-c-article__narrow cfhg-c-article__foot">
		<nav class="navigation post-navigation" aria-label="Posts">
			<div class="nav-links">
				${older ? `<div class="nav-previous"><a href="${link(`/news/${older.slug}/`)}" rel="prev"><span class="cfhg-c-eyebrow">Previous</span> ${esc(older.title)}</a></div>` : ''}
				${newer ? `<div class="nav-next"><a href="${link(`/news/${newer.slug}/`)}" rel="next"><span class="cfhg-c-eyebrow">Next</span> ${esc(newer.title)}</a></div>` : ''}
			</div>
		</nav>
	</footer>
</article>`;
	return page({ file: `news-${post.slug}.html`, title: `${post.title} | ${C.site.name}`, description: post.excerpt, current: 'news.html', body });
}

function notFound() {
	const nf = C.not_found;
	const body = `<section class="cfhg-c-section cfhg-c-notfound" aria-labelledby="cfhg-c-notfound-title">
	<div class="cfhg-c-container cfhg-c-notfound__grid">
		<div>
			${eyebrow(nf.eyebrow)}
			<h1 class="cfhg-c-notfound__title" id="cfhg-c-notfound-title">${esc(nf.title)}</h1>
			<p class="cfhg-c-lead">${esc(nf.body)}</p>
			<form role="search" class="search-form" action="news.html">
				<label><span class="screen-reader-text">Search for:</span><input type="search" class="search-field" placeholder="Search &hellip;" name="s"></label>
				<input type="submit" class="search-submit" value="Search">
			</form>
			<ul class="cfhg-c-index">${nf.links.map((l) => `<li><a href="${esc(link(l.url))}">${esc(l.label)}</a></li>`).join('')}</ul>
		</div>
		<figure class="cfhg-c-notfound__figure">${themeImage(nf.image, nf.image_alt, { sizes: '(min-width: 64em) 28rem, 90vw' })}</figure>
	</div>
</section>`;
	return page({ file: '404.html', title: `Page not found | ${C.site.name}`, description: nf.body, current: '', body });
}

/* The group's own privacy policy (October 2025), shown as a second inner
   page. It is preview content only; in WordPress it is an ordinary page. */
const PRIVACY = [
	['p', 'The Caribbean Family History Group is committed to protecting your privacy and making sure your personal data is handled safely and responsibly. This policy explains what we collect, how we use it, and your rights when you use our website. Last updated 10 October 2025.'],
	['h2', 'What we collect'],
	['ul', ['Your name and email address, for example through contact forms or event enquiries.', 'The content of messages you send us.', 'Website usage data collected through cookies, such as pages viewed.', 'Device and browser information, such as IP address and general location.']],
	['p', 'We do not collect sensitive personal data through the website.'],
	['h2', 'How we use it'],
	['ul', ['To reply to enquiries about genealogy, events or getting involved.', 'To send updates if you have subscribed.', 'To improve the website and keep it secure.', 'To keep basic records of enquiries and community engagement.']],
	['p', 'We do not sell or share your personal data with third parties for marketing.'],
	['h2', 'How long we keep it'],
	['p', 'We keep enquiry emails for up to 12 months unless further contact continues. Analytics data is kept for the standard period set by our analytics provider.'],
	['h2', 'Your rights'],
	['p', 'Under UK GDPR you can ask to see the personal data we hold about you, ask us to correct or delete it, withdraw consent, object to processing, and ask for your data in a portable form. Email cfhg.sol.birm@gmail.com to do any of these.'],
	['h2', 'Cookies'],
	['p', 'Essential cookies keep the site working and cannot be switched off. Analytics cookies help us understand how the site is used and only run if you accept them. Functionality cookies remember choices such as your cookie preference. Your text size choice is stored in your own browser and is not sent to us.'],
];

/* -------------------------------------------------------------------------
 * Build
 * ---------------------------------------------------------------------- */

if (existsSync(OUT)) {
	if (!existsSync(join(OUT, MARKER))) {
		console.error(`${OUT} exists but was not made by this script. Not touching it.`);
		process.exit(1);
	}
	rmSync(OUT, { recursive: true, force: true });
}
mkdirSync(OUT, { recursive: true });
writeFileSync(join(OUT, MARKER), 'Built by scripts/build-preview-c.mjs. Safe to delete.\n');

for (const dir of ['css', 'js', 'fonts', 'images']) {
	cpSync(join(THEME, 'assets', dir), join(OUT, 'assets', dir), { recursive: true });
}

const files = [];
files.push(homePage());
files.push(islandsIndex());
for (const island of C.islands) files.push(islandPage(island));
files.push(
	innerPage({
		file: 'membership.html',
		title: C.pages.membership.title,
		standfirst: C.pages.membership.standfirst,
		blocks: C.pages.membership.body,
		current: 'membership.html',
	})
);
files.push(
	innerPage({
		file: 'privacy.html',
		title: 'Privacy and cookies',
		standfirst: 'How we look after the information you share with us.',
		blocks: PRIVACY,
		current: '',
	})
);
files.push(newsArchive());
const sorted = [...C.posts].sort((a, b) => b.date.localeCompare(a.date));
sorted.forEach((post, i) => files.push(postPage(post, i, sorted)));
files.push(notFound());

writeFileSync(join(OUT, 'robots.txt'), 'User-agent: *\nDisallow: /\n');

/* Copy checks: no em or en dashes, none of the banned stock phrases. */
const BANNED = ['welcome to', 'embark', 'unlock', 'discover the power', 'tapestry', 'rich heritage', 'celebrat', 'empower', 'journey'];
const problems = [];
for (const f of readdirSync(OUT).filter((f) => f.endsWith('.html'))) {
	const text = readFileSync(join(OUT, f), 'utf8');
	if (/[\u2014\u2013]/.test(text)) problems.push(`${f}: contains an em or en dash`);
	const visible = text.replace(/<[^>]+>/g, ' ').toLowerCase();
	for (const phrase of BANNED) if (visible.includes(phrase)) problems.push(`${f}: contains "${phrase}"`);
}
if (problems.length) {
	console.error('Copy problems:\n  ' + problems.join('\n  '));
	process.exit(1);
}

console.log(`Built ${files.length} pages into preview-c/:\n  ${files.join('\n  ')}`);
