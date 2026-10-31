#!/usr/bin/env node
/**
 * Build a static design preview of the CFHG Option B theme for client review.
 *
 *   npm run build:preview:b
 *
 * Same rules as the Option A build (scripts/build-preview.mjs):
 * - CSS, JS, fonts, images and shapes are copied from the theme every build.
 * - All copy comes from the theme's inc/defaults.php; image sizes, alt text
 *   and focal points from inc/images.php; shapes from assets/images/shapes.
 * - The markup mirrors the theme templates, and a drift guard fails the build
 *   if a template no longer contains the hooks reproduced here.
 *
 * Output: /preview-b, relative paths only, noindex, robots.txt disallows all.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { POST_COPY, innerProse, singleProse } from './preview-content.mjs';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const THEME = process.env.CFHG_THEME_DIR ? path.resolve(process.env.CFHG_THEME_DIR) : path.join(ROOT, 'wordpress', 'cfhg-option-b');
const OUT = process.env.CFHG_PREVIEW_DIR ? path.resolve(process.env.CFHG_PREVIEW_DIR) : path.join(ROOT, 'preview-b');
const MARKER = '.cfhg-preview-build';

const read = (rel) => fs.readFileSync(path.join(THEME, rel), 'utf8');
const fail = (msg) => {
	console.error(`\nbuild:preview:b failed: ${msg}\n`);
	process.exit(1);
};

/* 1. Drift guard ---------------------------------------------------------- */

const HOOKS = {
	'header.php': ['class="cfhg-skip"', 'id="content" class="cfhg-content"'],
	'template-parts/site/header.php': ['cfhg-header__brand', 'cfhg-header__join', 'cfhg-menu-toggle__open', 'cfhg-menu-toggle__close', 'cfhg-menu-toggle__sign', 'cfhg-menu__name', 'cfhg-menu__nav', 'cfhg-menu__foot'],
	'template-parts/site/footer.php': ['cfhg-footer__band', 'cfhg-footer__image', 'cfhg-footer__big', 'cfhg-footer__card', 'cfhg-footer__brand', 'cfhg-footer__col', 'cfhg-footer__heading', 'cfhg-footer__date', 'cfhg-footer__base'],
	'front-page.php': ['cfhg-main cfhg-main--home', 'template-parts/home/'],
	'template-parts/home/hero.php': ['cfhg-hero__inner', 'cfhg-hero__copy', 'cfhg-hero__kicker', 'cfhg-hero__title', 'cfhg_component_hero_proof', 'cfhg-btn--big', 'cfhg-hero__art', 'cfhg-hero__dot--white', 'cfhg-hero__dot--coral', 'cfhg-hero__disc', 'cfhg-hero__dot--blue', 'cfhg-hero__dot--gold'],
	'template-parts/home/intro.php': ['cfhg-intro__stage', 'cfhg-intro__snap--left', 'cfhg-intro__snap--right', 'data-reveal="tilt"', 'cfhg-intro__card', 'cfhg-card__title', 'cfhg_paragraphs'],
	'template-parts/home/meeting.php': ['id="next-meeting"', 'cfhg-meeting__grid', 'cfhg-meeting__card', "cfhg_component_next_meeting( 'title' )", 'cfhg-highlight', 'cfhg-meeting__media', 'cfhg-chip'],
	'template-parts/home/numbers.php': ['cfhg-numbers__title', 'cfhg_component_numbers'],
	'template-parts/home/start.php': ['id="start-here"', 'cfhg-split-head', 'cfhg_component_steps', 'cfhg-start__actions'],
	'template-parts/home/islands.php': ['cfhg-islands__grid', 'cfhg-islands__copy', 'cfhg-islands__text', 'cfhg-islands__window', 'cfhg-islands__frond'],
	'template-parts/home/stories.php': ['cfhg-stories__head', 'cfhg_component_stories'],
	'template-parts/home/help.php': ['cfhg-help__card', 'cfhg-help__flower', 'cfhg-help__copy', 'cfhg-help__media'],
	'template-parts/home/join.php': ['id="join"', 'cfhg-join__band', 'cfhg-join__image', 'cfhg-join__big', 'cfhg-join__card'],
	'template-parts/home/partners.php': ['cfhg-partners__head', 'cfhg-partners__card', 'cfhg_component_partners'],
	'template-parts/content/page.php': ['cfhg-page-hero', 'cfhg-page-hero__title', 'cfhg-page-hero__lead', 'cfhg-entry__media', 'cfhg-prose'],
	'template-parts/content/single.php': ['cfhg-story__tags', 'cfhg-page-hero__meta', 'cfhg-post-nav', "'plain'"],
	'template-parts/content/listing.php': ['cfhg-page-hero__lead', 'cfhg-listing', 'cfhg-stories__list', 'cfhg_component_story_slide'],
	'404.php': ['cfhg-notfound', 'cfhg-notfound__flower', 'cfhg-notfound__frond', 'cfhg-notfound__card'],
	'searchform.php': ['cfhg-search__input', 'cfhg-search__button'],
	'inc/components.php': ['cfhg-date__day', 'cfhg-date__rest', 'cfhg-facts__row', 'cfhg-prices__row', 'cfhg-notes', 'cfhg-note__num', 'cfhg-steps__title', 'cfhg-places__name', 'cfhg-snap__photo', 'cfhg-snap__sticker', 'cfhg-slide__top', 'cfhg-slide__count', 'cfhg-slide__cta', 'cfhg-carousel__track', 'data-cfhg-prev', 'cfhg-partner__name', 'cfhg-next__where', 'cfhg-hero__proof', 'cfhg-avatars__face', 'cfhg-avatars__badge', 'avatar-flat-cap', 'avatar-album', 'avatar-smiling'],
	'inc/template-tags.php': ['cfhg-btn__label', 'cfhg-btn__icon', "'cfhg-btn cfhg-btn--' . $variant"],
	'inc/class-cfhg-menu-walker.php': ['cfhg-menu__link', 'cfhg-menu__label'],
	'inc/images.php': ['function cfhg_shape(', 'cfhg-shape cfhg-shape--'],
};
for (const [file, hooks] of Object.entries(HOOKS)) {
	if (!fs.existsSync(path.join(THEME, file))) fail(`theme file missing: ${file}`);
	const src = read(file);
	const missing = hooks.filter((h) => !src.includes(h));
	if (missing.length) fail(`${file} no longer contains ${missing.map((m) => `"${m}"`).join(', ')}.\nThe template has changed; update scripts/build-preview-b.mjs to match it.`);
}

/* 2. Content from the theme ------------------------------------------------ */

const phpStr = (s) => s.replace(/\\'/g, "'").replace(/\\n/g, '\n');
const defaultsSrc = read('inc/defaults.php');
const D = {};
for (const m of defaultsSrc.matchAll(/'(\w+)'\s*=>\s*__\(\s*(['"])([\s\S]*?)(?<!\\)\2\s*,\s*'cfhg'\s*\)/g)) D[m[1]] = phpStr(m[3]);
for (const m of defaultsSrc.matchAll(/'(\w+)'\s*=>\s*'([^']*)',/g)) if (!(m[1] in D)) D[m[1]] = m[2];
for (const key of ['numbers_list', 'islands_list', 'start_steps', 'partners_list']) {
	const block = defaultsSrc.match(new RegExp(`'${key}'\\s*=>\\s*implode\\(([\\s\\S]*?)\\n\\t\\t\\),\\n`));
	if (!block) fail(`could not read ${key} from inc/defaults.php`);
	D[key] = [...block[1].matchAll(/__\(\s*'([\s\S]*?)(?<!\\)'\s*,\s*'cfhg'\s*\)/g)].map((m) => phpStr(m[1])).join('\n');
}
for (const k of ['hero_kicker', 'hero_heading', 'hero_text', 'hero_proof_text', 'intro_text', 'meeting_heading', 'meeting_image_label', 'numbers_title', 'stories_read_label', 'join_big', 'footer_big', 'contact_email']) {
	if (!(k in D)) fail(`could not read "${k}" from inc/defaults.php`);
}

const imagesSrc = read('inc/images.php');
const MANIFEST = {};
for (const m of imagesSrc.matchAll(/'([a-z0-9-]+)'\s*=>\s*array\(\s*'sizes'\s*=>\s*array\(([^)]*)\),\s*'alt'\s*=>\s*__\(\s*'([\s\S]*?)(?<!\\)',\s*'cfhg'\s*\),\s*'focus'\s*=>\s*'([^']+)'/g)) {
	const sizes = {};
	for (const s of m[2].matchAll(/(\d+)\s*=>\s*(\d+)/g)) sizes[+s[1]] = +s[2];
	MANIFEST[m[1]] = { sizes, alt: phpStr(m[3]), focus: m[4] };
}
const IMAGE_DEFAULTS = Object.fromEntries([...imagesSrc.matchAll(/'(\w+_image|intro_photo_\d)'\s*=>\s*'([a-z0-9-]+)'/g)].map((m) => [m[1], m[2]]));
if (Object.keys(MANIFEST).length < 9) fail('could not read the image manifest from inc/images.php');
for (const k of ['hero_image', 'intro_photo_1', 'intro_photo_2', 'meeting_image', 'islands_image', 'help_image', 'join_image', 'footer_image']) if (!IMAGE_DEFAULTS[k]) fail(`no default image for ${k}`);

const SHAPES = Object.fromEntries(['hibiscus', 'palm-frond', 'wave'].map((n) => [n, read(`assets/images/shapes/${n}.svg`)]));
const shape = (name, cls = '') => `<span class="cfhg-shape cfhg-shape--${name}${cls ? ' ' + cls : ''}" aria-hidden="true">${SHAPES[name]}</span>`;

const lines = (key, n) => D[key].split('\n').filter((l) => l.trim()).map((l) => {
	const parts = l.split('|').map((p) => p.trim());
	while (parts.length < n) parts.push('');
	return parts.slice(0, n);
});

/* 3. Links ------------------------------------------------------------------ */

const PAGES = { home: 'index.html', page: 'who-we-are.html', archive: 'stories.html', single: 'tracing-ancestors-back-to-the-period-of-slavery.html', notfound: '404.html' };
const LINK_MAP = {
	'/': PAGES.home,
	'/who-we-are/': PAGES.page,
	'/contact/': `${PAGES.page}#contact`,
	'/privacy-and-cookies/': `${PAGES.page}#privacy`,
	'/photography-and-video/': `${PAGES.page}#photography`,
	'/stories/': PAGES.archive,
	'/whats-on/': `${PAGES.home}#next-meeting`,
	'/join/': `${PAGES.home}#join`,
	'/research/': `${PAGES.home}#start-here`,
	'/research/start-here/': `${PAGES.home}#start-here`,
	'/research/get-help/': `${PAGES.page}#contact`,
};
function link(u) {
	u = (u || '').trim();
	if (!u) return '';
	if (u.startsWith('#') || /^(https?:|mailto:|tel:)/i.test(u)) return u;
	if (/^[a-z0-9-]+\.html(#[\w-]+)?$/.test(u)) return u; /* already a preview page */
	if (u in LINK_MAP) return LINK_MAP[u];
	if (u.startsWith('/research/')) return `${PAGES.home}#cfhg-islands-title`;
	fail(`no preview page for the theme link "${u}". Add it to LINK_MAP.`);
}

/* Option B's images for the shared sample posts, in the same order. */
const POST_IMAGES = ['copying-details-from-a-register', 'notebook-and-old-photographs', '', 'marking-a-caribbean-map', ''];
const POSTS = POST_COPY.map((p, i) => ({ ...p, image: POST_IMAGES[i] ?? '', href: p.href === 'single' ? PAGES.single : PAGES.archive }));

/* 4. Markup helpers mirroring inc/template-tags.php and inc/components.php -- */

const esc = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
const A = 'assets';
const ARROW = '<svg class="cfhg-arrow" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false"><path d="M3 10h13M11 4.5 16.5 10 11 15.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
const MAIL = `mailto:${D.contact_email}`;
const text = (s) => esc(s).replace(/\n/g, '<br>');
// Mirrors wp_kses( $value, cfhg_inline_kses() ) for the tags the defaults use.
const inline = (s) => esc(s).replace(/&lt;(\/?)(em|strong|br)&gt;/g, '<$1$2>');
const paragraphs = (s) => s.trim().split(/\n\s*\n/).filter((p) => p.trim()).map((p) => `<p>${text(p.trim())}</p>`).join('');

function img(slug, { sizes = '100vw', loading = 'lazy', alt = null, style = '' } = {}) {
	const it = MANIFEST[slug];
	if (!it) fail(`image "${slug}" is not in inc/images.php`);
	const widths = Object.keys(it.sizes).map(Number).sort((a, b) => a - b);
	const big = widths[widths.length - 1];
	const srcset = widths.map((w) => `${A}/images/${slug}-${w}.jpg ${w}w`).join(', ');
	const a = alt === null ? it.alt : alt;
	return `<img src="${A}/images/${slug}-${big}.jpg" srcset="${srcset}" sizes="${esc(sizes)}" width="${big}" height="${it.sizes[big]}" alt="${esc(a)}" class="" style="object-position:${it.focus}${style ? ';' + style : ''}" loading="${loading}" decoding="async"${loading === 'eager' ? ' fetchpriority="high"' : ''}>`;
}
const settingImg = (key, opts) => img(IMAGE_DEFAULTS[key], opts);

function btn(label, u, variant = 'mark', cls = '') {
	const href = link(u);
	if (!label || !href) return '';
	const classes = `cfhg-btn cfhg-btn--${variant}${cls ? ' ' + cls : ''}`;
	if (variant === 'text') return `<a class="${classes}" href="${esc(href)}">${esc(label)}</a>`;
	return `<a class="${classes}" href="${esc(href)}"><span class="cfhg-btn__label">${esc(label)}</span><span class="cfhg-btn__icon" aria-hidden="true">${ARROW}</span></a>`;
}

function nextMeeting(today = new Date()) {
	const t = new Date(today.getFullYear(), today.getMonth(), today.getDate());
	const skip = new Set(D.meeting_skip_months.split(',').map(Number).filter(Boolean));
	for (let i = 0; i < 14; i++) {
		const first = new Date(t.getFullYear(), t.getMonth() + i, 1);
		if (skip.has(first.getMonth() + 1)) continue;
		const last = new Date(first.getFullYear(), first.getMonth() + 1, 0);
		last.setDate(last.getDate() - ((last.getDay() + 1) % 7));
		if (last >= t) return last;
	}
	return null;
}
const NM = nextMeeting();
const fmt = (d, o) => d.toLocaleDateString('en-GB', o);
const nmLong = fmt(NM, { weekday: 'long', day: 'numeric', month: 'long' });
const iso = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
const postDate = (s) => fmt(new Date(`${s}T12:00:00`), { day: 'numeric', month: 'long', year: 'numeric' });

const heroProof = () => `<div class="cfhg-hero__proof"><span class="cfhg-avatars" aria-hidden="true">${['avatar-flat-cap', 'avatar-album', 'avatar-smiling'].map((slug) => `<span class="cfhg-avatars__face">${img(slug, { sizes: '3rem', alt: '' })}</span>`).join('')}<span class="cfhg-avatars__badge"><img src="${A}/images/cfhg-logo-badge.png" width="511" height="512" alt="" loading="lazy" decoding="async"></span></span><p>${esc(D.hero_proof_text)}</p></div>`;
const nmInline = () => `<p class="cfhg-next"><span class="cfhg-next__label">${esc(D.meeting_kicker)}</span> <strong>${esc(nmLong)}</strong><span class="cfhg-next__where">${esc(D.meeting_short_where)}</span></p>`;
const nmTitle = () => `<p class="cfhg-date"><time datetime="${iso(NM)}"><span class="cfhg-date__day">${fmt(NM, { weekday: 'long' })}</span> <span class="cfhg-date__rest">${fmt(NM, { day: 'numeric', month: 'long' })}</span></time></p>`;
const facts = () => `<dl class="cfhg-facts">${[['Where', D.meeting_venue], ['Online', D.meeting_online], ['When', D.meeting_pattern], ['Time', D.meeting_time]].filter(([, v]) => v).map(([k, v]) => `<div class="cfhg-facts__row"><dt>${k}</dt><dd>${text(v)}</dd></div>`).join('')}</dl>${D.meeting_note ? `<p class="cfhg-facts__note">${text(D.meeting_note)}</p>` : ''}`;
const prices = () => `<dl class="cfhg-prices">${[['price_first_label', 'price_first', 'is-free'], ['price_visit_label', 'price_visit', ''], ['price_year_label', 'price_year', '']].map(([a, b, c]) => `<div class="cfhg-prices__row ${c}"><dt>${esc(D[a])}</dt><dd>${esc(D[b])}</dd></div>`).join('')}</dl>`;
const snapshot = (key, tone, shp) => `<figure class="cfhg-snap cfhg-snap--${tone} cfhg-snap--${shp}"><span class="cfhg-snap__photo">${settingImg(key, { sizes: '(min-width: 1024px) 22vw, 60vw' })}</span><span class="cfhg-snap__sticker"><img src="${A}/images/cfhg-logo-badge.png" width="511" height="512" alt="" loading="lazy" decoding="async"></span></figure>`;

function slide(c, i, total) {
	const tones = ['sand', 'sea', 'gold', 'green', 'coral'];
	const tone = tones[i % tones.length];
	const tags = c.tags.map((t) => `<li class="cfhg-tag">${esc(t)}</li>`).join('');
	return `<article class="cfhg-slide cfhg-slide--${tone}${c.image ? '' : ' cfhg-slide--text'}" aria-roledescription="slide" aria-label="${i + 1} of ${total}">${c.image ? `<div class="cfhg-slide__media">${img(c.image, { sizes: '(min-width: 1024px) 34vw, 90vw' })}</div>` : ''}<div class="cfhg-slide__body"><div class="cfhg-slide__top">${tags ? `<ul class="cfhg-slide__tags" aria-label="Topics">${tags}</ul>` : '<span></span>'}<span class="cfhg-slide__count">${i + 1}/${total}</span></div>${c.meta ? `<p class="cfhg-slide__meta">${esc(c.meta)}</p>` : ''}<h3 class="cfhg-slide__title">${esc(c.title)}</h3>${c.text ? `<p class="cfhg-slide__text">${esc(c.text)}</p>` : ''}${btn(D.stories_read_label, c.href, 'mark', 'cfhg-slide__cta')}</div></article>`;
}

/* 5. Page frame: header.php, footer.php and their parts ---------------------- */

const MENU = [['Research', '/research/'], ['What’s On', '/whats-on/'], ['Stories', '/stories/'], ['Who We Are', '/who-we-are/'], ['Join the group', '/join/'], ['Contact', '/contact/']];
const PREVIEW_NAV = [['Home', PAGES.home], ['Inner page', PAGES.page], ['Stories', PAGES.archive], ['Story', PAGES.single], ['404', PAGES.notfound]];

function header(current) {
	const items = MENU.map(([label, u]) => {
		const href = link(u);
		const isCurrent = href === current;
		return `<li class="cfhg-menu__item${isCurrent ? ' current-menu-item' : ''}"><a class="cfhg-menu__link" href="${esc(href)}"${isCurrent ? ' aria-current="page"' : ''}><span class="cfhg-menu__label">${esc(label)}</span></a></li>`;
	}).join('');
	return `<header class="cfhg-header" data-cfhg-header>
	<div class="cfhg-header__bar">
		<a class="cfhg-header__brand" href="${PAGES.home}" rel="home"><img src="${A}/images/cfhg-logo-horizontal.png" width="798" height="240" alt="Caribbean Family History Group" class="cfhg-logo"></a>
		<div class="cfhg-header__actions">
			<a class="cfhg-header__join" href="${esc(link(D.header_join_url))}">${esc(D.header_join_label)}</a>
			<button type="button" class="cfhg-menu-toggle" aria-expanded="false" aria-controls="cfhg-menu" data-cfhg-menu-toggle><span class="cfhg-menu-toggle__open">Menu</span><span class="cfhg-menu-toggle__close">Close</span><span class="cfhg-menu-toggle__sign" aria-hidden="true"></span></button>
		</div>
	</div>
	<div class="cfhg-menu" id="cfhg-menu" data-cfhg-menu>
		<p class="cfhg-menu__name">Caribbean Family History Group</p>
		<nav class="cfhg-menu__nav" aria-label="Main"><ul class="cfhg-menu__list">${items}</ul></nav>
		<div class="cfhg-menu__foot">${nmInline()}<p class="cfhg-menu__email"><a href="${MAIL}">${esc(D.contact_email)}</a></p></div>
	</div>
</header>`;
}

function footer(current) {
	const links = MENU.map(([label, u]) => `<li><a href="${esc(link(u))}"><span>${esc(label)}</span>${ARROW}</a></li>`).join('');
	const preview = PREVIEW_NAV.map(([label, href]) => (href === current ? `<span aria-current="page">${label}</span>` : `<a href="${href}">${label}</a>`)).join(' ');
	return `<footer class="cfhg-footer">
	<div class="cfhg-footer__band">
		<figure class="cfhg-footer__image">${settingImg('footer_image', { sizes: '100vw', alt: '' })}</figure>
		<p class="cfhg-footer__big" aria-hidden="true">${esc(D.footer_big)}</p>
	</div>
	<div class="cfhg-footer__card">
		<div class="cfhg-footer__brand">
			<a href="${PAGES.home}" rel="home"><img src="${A}/images/cfhg-logo-horizontal.png" width="798" height="240" alt="Caribbean Family History Group" class="cfhg-logo cfhg-footer__logo"></a>
			<p class="cfhg-footer__tagline">${text(D.footer_tagline)}</p>
		</div>
		<nav class="cfhg-footer__col" aria-label="Footer"><p class="cfhg-footer__heading">Explore</p><ul class="cfhg-footer__links">${links}</ul></nav>
		<div class="cfhg-footer__col">
			<p class="cfhg-footer__heading">${esc(D.meeting_kicker)}</p>
			<p class="cfhg-footer__date">${esc(nmLong)}</p>
			<p class="cfhg-footer__venue">${esc(D.meeting_short_where)}</p>
			${btn(D.join_cta_label, D.join_cta_url, 'mark')}
		</div>
		<div class="cfhg-footer__col">
			<p class="cfhg-footer__heading">Contact</p>
			<p><a class="cfhg-footer__email" href="${MAIL}">${esc(D.contact_email)}</a></p>
			<ul class="cfhg-footer__legal"><li><a href="${esc(link(D.privacy_url))}">Privacy and cookies</a></li></ul>
		</div>
		<div class="cfhg-footer__base">
			<p class="cfhg-footer__photo">${esc(D.footer_photo_note)} <a href="${esc(link(D.footer_photo_url))}">Photography and video policy</a></p>
			<p class="cfhg-footer__copy">&copy; ${new Date().getFullYear()} Caribbean Family History Group</p>
			<p class="cfhg-preview-label"><strong>Design preview, option B.</strong> Not the live site. <span class="cfhg-preview-label__pages">${preview}</span></p>
		</div>
	</div>
</footer>`;
}

/* Styles for the preview label only. The theme CSS is untouched. */
const PREVIEW_CSS = `.cfhg-preview-label{flex-basis:100%;margin:0;padding-top:.75rem;border-top:1px dashed var(--cfhg-line);font-size:.75rem;line-height:1.5;color:var(--cfhg-ink-soft)}.cfhg-preview-label strong{font-weight:500;color:var(--cfhg-ink)}.cfhg-preview-label__pages{display:inline-flex;flex-wrap:wrap;gap:.25rem .75rem;margin-left:.5rem}.cfhg-preview-label [aria-current]{text-decoration:underline;text-decoration-style:dotted}`;

function page({ file, title, description, bodyClass, main }) {
	return `<!doctype html>
<html lang="en-GB">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="robots" content="noindex, nofollow">
	<title>${esc(title)}</title>
	<meta name="description" content="${esc(description)}">
	<script>document.documentElement.classList.add('cfhg-js');</script>
	<link rel="preload" href="${A}/fonts/jost-var.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="${A}/fonts/outfit-var.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="icon" href="${A}/images/cfhg-logo-badge.png" sizes="512x512" type="image/png">
	<link rel="stylesheet" href="${A}/css/tokens.css">
	<link rel="stylesheet" href="${A}/css/main.css">
	<style>${PREVIEW_CSS}</style>
</head>
<body class="${bodyClass}">
<a class="cfhg-skip" href="#content">Skip to content</a>
${header(file)}
<div id="content" class="cfhg-content" tabindex="-1">
${main}
</div>
${footer(file)}
<script src="${A}/js/main.js" defer></script>
</body>
</html>
`;
}

/* 6. Pages ------------------------------------------------------------------ */

function homepage() {
	const hero = `<section class="cfhg-hero" id="top" aria-labelledby="cfhg-hero-title">
	<div class="cfhg-hero__inner">
		<div class="cfhg-hero__copy">
			<p class="cfhg-hero__kicker">${esc(D.hero_kicker)}</p>
			<h1 class="cfhg-hero__title" id="cfhg-hero-title">${inline(D.hero_heading)}</h1>
			<p class="cfhg-lead">${esc(D.hero_text)}</p>
			${heroProof()}
			<div class="cfhg-actions">${btn(D.hero_primary_label, D.hero_primary_url, 'mark', 'cfhg-btn--big')}${btn(D.hero_secondary_label, D.hero_secondary_url, 'text')}</div>
			${nmInline()}
		</div>
		<div class="cfhg-hero__art">
			<span class="cfhg-hero__dot cfhg-hero__dot--white" aria-hidden="true"></span>
			<span class="cfhg-hero__dot cfhg-hero__dot--coral" aria-hidden="true"></span>
			<figure class="cfhg-hero__disc">${settingImg('hero_image', { sizes: '(min-width: 900px) 52vw, 92vw', loading: 'eager' })}</figure>
			<span class="cfhg-hero__dot cfhg-hero__dot--blue" aria-hidden="true"></span>
			<span class="cfhg-hero__dot cfhg-hero__dot--gold" aria-hidden="true"></span>
		</div>
	</div>
</section>`;

	const intro = `<section class="cfhg-section cfhg-intro" id="cfhg-intro" aria-labelledby="cfhg-intro-title"><div class="cfhg-intro__stage">
	<div class="cfhg-intro__snap cfhg-intro__snap--left" data-reveal="tilt">${snapshot('intro_photo_1', 'coral', 'portrait')}</div>
	<div class="cfhg-card cfhg-intro__card" data-reveal><h2 class="cfhg-card__title" id="cfhg-intro-title">${esc(D.intro_title)}</h2>${paragraphs(D.intro_text)}${btn(D.intro_link_label, D.intro_link_url, 'mark')}</div>
	<div class="cfhg-intro__snap cfhg-intro__snap--right" data-reveal="tilt">${snapshot('intro_photo_2', 'green', 'landscape')}</div>
</div></section>`;

	const meeting = `<section class="cfhg-section cfhg-meeting" id="next-meeting" aria-labelledby="cfhg-meeting-title"><div class="cfhg-container cfhg-meeting__grid">
	<div class="cfhg-card cfhg-meeting__card" data-reveal><p class="cfhg-label">${esc(D.meeting_kicker)}</p>${nmTitle()}<h2 class="cfhg-h3 cfhg-meeting__heading" id="cfhg-meeting-title"><span class="cfhg-highlight">${esc(D.meeting_heading)}</span></h2>${facts()}${prices()}<div class="cfhg-actions">${btn(D.meeting_cta_label, D.meeting_cta_url, 'green')}${btn(D.meeting_secondary_label, MAIL, 'text')}</div></div>
	<figure class="cfhg-meeting__media" data-reveal>${settingImg('meeting_image', { sizes: '(min-width: 1024px) 52vw, 92vw' })}<figcaption class="cfhg-chip">${esc(D.meeting_image_label)}</figcaption></figure>
</div></section>`;

	const notes = `<ul class="cfhg-notes">${lines('numbers_list', 2).map(([n, t]) => `<li class="cfhg-note"><span class="cfhg-note__num">${esc(n)}</span> <span class="cfhg-note__text">${esc(t)}</span></li>`).join('')}</ul>`;
	const numbers = `<section class="cfhg-section cfhg-numbers" aria-labelledby="cfhg-numbers-title"><div class="cfhg-container"><h2 class="cfhg-h2 cfhg-numbers__title" id="cfhg-numbers-title" data-reveal>${esc(D.numbers_title)}</h2>${notes}</div></section>`;

	const steps = `<ol class="cfhg-steps">${lines('start_steps', 2).map(([t, x]) => `<li class="cfhg-steps__item"><h3 class="cfhg-steps__title">${esc(t)}</h3><p class="cfhg-steps__text">${esc(x)}</p></li>`).join('')}</ol>`;
	const start = `<section class="cfhg-section cfhg-start" id="start-here" aria-labelledby="cfhg-start-title"><div class="cfhg-container">
	<div class="cfhg-split-head" data-reveal><div><p class="cfhg-label">${esc(D.start_kicker)}</p><h2 class="cfhg-h2" id="cfhg-start-title">${esc(D.start_heading)}</h2></div><p class="cfhg-lead cfhg-split-head__text">${esc(D.start_text)}</p></div>
	${steps}<div class="cfhg-actions cfhg-start__actions">${btn(D.start_link_label, D.start_link_url, 'mark')}</div>
</div></section>`;

	const places = `<ul class="cfhg-places">${lines('islands_list', 3).map(([n, u, note]) => {
		const inner = `<strong class="cfhg-places__name">${esc(n)}</strong>${note ? ` <span class="cfhg-places__note">${esc(note)}</span>` : ''}`;
		return u ? `<li><a class="cfhg-places__link" href="${esc(link(u))}">${inner}</a></li>` : `<li><span class="cfhg-places__link">${inner}</span></li>`;
	}).join('')}</ul>`;
	const islands = `<section class="cfhg-section cfhg-islands" aria-labelledby="cfhg-islands-title"><div class="cfhg-container cfhg-islands__grid">
	<div class="cfhg-islands__copy"><p class="cfhg-label">${esc(D.islands_kicker)}</p><h2 class="cfhg-h2" id="cfhg-islands-title">${esc(D.islands_heading)}</h2><p class="cfhg-islands__text">${esc(D.islands_text)}</p>${places}</div>
	<div class="cfhg-islands__window" data-reveal><figure class="cfhg-oval">${settingImg('islands_image', { sizes: '(min-width: 1024px) 34vw, 80vw' })}</figure>${shape('palm-frond', 'cfhg-islands__frond')}</div>
</div></section>`;

	const latest = POSTS.slice(0, 3).map((p) => ({ tags: p.cats.filter((c) => c !== 'Stories').slice(0, 2), meta: postDate(p.date), title: p.title, text: p.excerpt, image: p.image, href: p.href }));
	const stories = `<section class="cfhg-section cfhg-stories" aria-labelledby="cfhg-stories-title"><div class="cfhg-container"><div class="cfhg-stories__head" data-reveal><div><p class="cfhg-label">${esc(D.stories_kicker)}</p><h2 class="cfhg-h2" id="cfhg-stories-title">${esc(D.stories_heading)}</h2></div>${btn(D.stories_link_label, D.stories_link_url, 'text')}</div></div>
	<div class="cfhg-carousel" data-cfhg-carousel><div class="cfhg-carousel__track" tabindex="0" role="region" aria-roledescription="carousel" aria-label="${esc(D.stories_heading)}">${latest.map((c, i) => slide(c, i, latest.length)).join('')}</div><div class="cfhg-carousel__nav"><button type="button" class="cfhg-carousel__btn" data-cfhg-prev aria-label="Previous story"><span class="cfhg-flip">${ARROW}</span></button><button type="button" class="cfhg-carousel__btn" data-cfhg-next aria-label="Next story">${ARROW}</button></div></div>
</section>`;

	const help = `<section class="cfhg-section cfhg-help" aria-labelledby="cfhg-help-title"><div class="cfhg-container"><div class="cfhg-help__card" data-reveal>${shape('hibiscus', 'cfhg-help__flower')}
	<div class="cfhg-help__copy"><p class="cfhg-label">${esc(D.help_kicker)}</p><h2 class="cfhg-h2" id="cfhg-help-title">${esc(D.help_heading)}</h2><p>${esc(D.help_text)}</p>${btn(D.help_cta_label, D.help_cta_url, 'green')}</div>
	<figure class="cfhg-help__media">${settingImg('help_image', { sizes: '(min-width: 1024px) 44vw, 88vw' })}</figure>
</div></div></section>`;

	const join = `<section class="cfhg-section cfhg-join" id="join" aria-labelledby="cfhg-join-title">
	<div class="cfhg-join__band"><figure class="cfhg-join__image">${settingImg('join_image', { sizes: '100vw', alt: '' })}</figure><p class="cfhg-join__big" aria-hidden="true">${esc(D.join_big)}</p></div>
	<div class="cfhg-container"><div class="cfhg-card cfhg-join__card" data-reveal><p class="cfhg-label">${esc(D.join_kicker)}</p><h2 class="cfhg-h2" id="cfhg-join-title">${esc(D.join_heading)}</h2><p class="cfhg-lead">${esc(D.join_text)}</p><div class="cfhg-actions">${btn(D.join_cta_label, D.join_cta_url, 'mark', 'cfhg-btn--big')}${btn(D.join_secondary_label, MAIL, 'text')}</div></div></div>
</section>`;

	const partnerList = `<ul class="cfhg-partners__list">${lines('partners_list', 3).map(([n, t, u]) => `<li class="cfhg-partner"><h3 class="cfhg-partner__name">${u ? `<a href="${esc(link(u))}" rel="noopener">${esc(n)}</a>` : esc(n)}</h3><p class="cfhg-partner__text">${esc(t)}</p></li>`).join('')}</ul>`;
	const partners = `<section class="cfhg-section cfhg-partners" aria-labelledby="cfhg-partners-title"><div class="cfhg-container"><div class="cfhg-partners__head" data-reveal><p class="cfhg-label">${esc(D.partners_kicker)}</p><h2 class="cfhg-h2" id="cfhg-partners-title">${esc(D.partners_heading)}</h2></div><div class="cfhg-card cfhg-partners__card" data-reveal>${partnerList}</div></div></section>`;

	return page({
		file: PAGES.home,
		title: 'Caribbean Family History Group',
		description: D.hero_text,
		bodyClass: 'home page cfhg cfhg-overlay-header cfhg-home',
		main: `<main id="main" class="cfhg-main cfhg-main--home">${hero}${intro}${meeting}${numbers}${start}${islands}${stories}${help}${join}${partners}</main>`,
	});
}

function innerPage() {
	return page({
		file: PAGES.page,
		title: 'Who we are | Caribbean Family History Group',
		description: 'Who the Caribbean Family History Group are, our history, our partners and how the group runs.',
		bodyClass: 'page cfhg cfhg-overlay-header',
		main: `<main id="main" class="cfhg-main">
	<article class="page type-page cfhg-entry">
		<header class="cfhg-page-hero"><div class="cfhg-container">
			<h1 class="cfhg-h1 cfhg-page-hero__title">Who we are</h1>
			<p class="cfhg-lead cfhg-page-hero__lead">Volunteers who enjoy genealogy, helping each other and anyone who asks to trace families with roots in the Caribbean.</p>
		</div></header>
		<figure class="cfhg-container cfhg-entry__media cfhg-rounded">${img('group-at-a-library-table', { sizes: '(min-width: 1200px) 1200px, 92vw', style: 'aspect-ratio:16/9;object-fit:cover' })}</figure>
		<div class="cfhg-container"><div class="cfhg-prose">${innerProse({ mail: MAIL, email: esc(D.contact_email) })}</div></div>
	</article>
</main>`,
	});
}

function archive() {
	const cards = POSTS.map((p, i) => slide({ tags: p.cats.slice(0, 2), meta: postDate(p.date), title: p.title, text: p.excerpt, image: p.image, href: p.href }, i, POSTS.length)).join('');
	return page({
		file: PAGES.archive,
		title: 'Stories | Caribbean Family History Group',
		description: 'Member stories, research articles and news from the Caribbean Family History Group.',
		bodyClass: 'archive category category-stories cfhg cfhg-overlay-header',
		main: `<main id="main" class="cfhg-main">
	<header class="cfhg-page-hero"><div class="cfhg-container">
		<h1 class="cfhg-h1 cfhg-page-hero__title">Stories</h1>
		<div class="cfhg-lead cfhg-page-hero__lead"><p>Member stories, research articles and news from the group.</p></div>
	</div></header>
	<div class="cfhg-container cfhg-listing"><div class="cfhg-stories__list">${cards}</div></div>
</main>`,
	});
}

function single() {
	const p = POSTS[0];
	const tags = p.cats.slice(0, 3).map((c, i) => `<li class="cfhg-tag${i === 0 ? ' cfhg-tag--first' : ''}"><a href="${PAGES.archive}">${esc(c)}</a></li>`).join('');
	return page({
		file: PAGES.single,
		title: `${p.title} | Caribbean Family History Group`,
		description: p.excerpt,
		bodyClass: 'post-template-default single single-post cfhg cfhg-overlay-header',
		main: `<main id="main" class="cfhg-main">
	<article class="post type-post cfhg-entry">
		<header class="cfhg-page-hero"><div class="cfhg-container">
			<ul class="cfhg-story__tags" aria-label="Topics">${tags}</ul>
			<h1 class="cfhg-h1 cfhg-page-hero__title">${esc(p.title)}</h1>
			<p class="cfhg-page-hero__meta"><time datetime="${p.date}">${postDate(p.date)}</time></p>
		</div></header>
		<figure class="cfhg-container cfhg-entry__media cfhg-rounded">${img('notebook-and-old-photographs', { sizes: '(min-width: 1200px) 1200px, 92vw', style: 'aspect-ratio:16/9;object-fit:cover' })}</figure>
		<div class="cfhg-container"><div class="cfhg-prose">${singleProse({ home: PAGES.home })}</div></div>
		<nav class="cfhg-container cfhg-post-nav" aria-label="More stories">${btn(POSTS[1].title, '/stories/', 'plain', 'cfhg-post-nav__next')}</nav>
	</article>
</main>`,
	});
}

function notFound() {
	return page({
		file: PAGES.notfound,
		title: 'Page not found | Caribbean Family History Group',
		description: 'This page could not be found.',
		bodyClass: 'error404 cfhg cfhg-overlay-header',
		main: `<main id="main" class="cfhg-main">
	<section class="cfhg-notfound" aria-labelledby="cfhg-404-title">
		${shape('hibiscus', 'cfhg-notfound__flower')}${shape('palm-frond', 'cfhg-notfound__frond')}
		<div class="cfhg-container"><div class="cfhg-card cfhg-notfound__card">
			<p class="cfhg-label">Page not found</p>
			<h1 class="cfhg-h1" id="cfhg-404-title">This branch of the tree doesn’t lead anywhere</h1>
			<p class="cfhg-lead">The page may have moved, or the link may be out of date. Try searching, or head back to the homepage.</p>
			<form role="search" method="get" class="cfhg-search" action="${PAGES.archive}">
				<label class="screen-reader-text" for="cfhg-search-1">Search the site</label>
				<input type="search" id="cfhg-search-1" class="cfhg-search__input" name="s" value="" placeholder="Search for a name, island or topic">
				<button type="submit" class="cfhg-search__button"><span class="screen-reader-text">Search</span><span class="cfhg-btn__disc" aria-hidden="true">${ARROW}</span></button>
			</form>
			<div class="cfhg-actions">${btn('Back to the homepage', '/', 'mark')}</div>
		</div></div>
	</section>
</main>`,
	});
}

/* 7. Write the folder --------------------------------------------------------- */

if (fs.existsSync(OUT)) {
	const entries = fs.readdirSync(OUT);
	if (entries.length && !entries.includes(MARKER)) fail(`${OUT} exists and was not made by this script. Move it out of the way first.`);
	fs.rmSync(OUT, { recursive: true, force: true });
}
fs.mkdirSync(OUT, { recursive: true });
/* Mark the folder as ours straight away, so a failed build can still be cleaned up next time. */
fs.writeFileSync(path.join(OUT, MARKER), 'Build in progress.\n');
for (const dir of ['css', 'js', 'fonts', 'images']) {
	fs.cpSync(path.join(THEME, 'assets', dir), path.join(OUT, 'assets', dir), { recursive: true, filter: (src) => !src.endsWith('.DS_Store') });
}
const pages = { [PAGES.home]: homepage(), [PAGES.page]: innerPage(), [PAGES.archive]: archive(), [PAGES.single]: single(), [PAGES.notfound]: notFound() };
for (const [file, html] of Object.entries(pages)) {
	if (/[—–]/.test(html)) fail(`${file} contains an em or en dash`);
	fs.writeFileSync(path.join(OUT, file), html);
}
fs.writeFileSync(path.join(OUT, 'robots.txt'), 'User-agent: *\nDisallow: /\n');
fs.writeFileSync(path.join(OUT, MARKER), `Built ${new Date().toISOString()} from wordpress/cfhg-option-b. Do not edit; run npm run build:preview:b.\n`);
const size = (dir) => fs.readdirSync(dir, { withFileTypes: true }).reduce((t, e) => t + (e.isDirectory() ? size(path.join(dir, e.name)) : fs.statSync(path.join(dir, e.name)).size), 0);
console.log(`Preview built in ${path.relative(ROOT, OUT)}/ (${(size(OUT) / 1e6).toFixed(1)} MB)`);
console.log(`  pages: ${Object.keys(pages).join(', ')}`);
console.log(`  next meeting shown: ${nmLong} (fixed at build time; rebuild to refresh)`);
