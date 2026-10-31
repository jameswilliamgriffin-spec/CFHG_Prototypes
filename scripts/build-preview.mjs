#!/usr/bin/env node
/**
 * Build a static design preview of the CFHG Option A theme for client review.
 *
 *   npm run build:preview
 *
 * One source of truth:
 * - CSS, JS, fonts and images are copied from the theme on every build.
 * - All copy comes from the theme's inc/defaults.php, and image sizes, alt text
 *   and focal points from inc/images.php.
 * - The markup mirrors the theme templates. There is no PHP here to execute
 *   them, so a drift guard checks that every template still contains the hooks
 *   this script reproduces, and the build fails if one has changed.
 *
 * Output: /preview, with relative paths only, so it works from any folder on
 * any host. Every page is noindex and robots.txt disallows everything.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { POST_COPY, innerProse, singleProse } from './preview-content.mjs';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const THEME = process.env.CFHG_THEME_DIR ? path.resolve(process.env.CFHG_THEME_DIR) : path.join(ROOT, 'wordpress', 'cfhg-option-a');
const OUT = process.env.CFHG_PREVIEW_DIR ? path.resolve(process.env.CFHG_PREVIEW_DIR) : path.join(ROOT, 'preview');
const MARKER = '.cfhg-preview-build';

const read = (rel) => fs.readFileSync(path.join(THEME, rel), 'utf8');
const fail = (msg) => {
	console.error(`\nbuild:preview failed: ${msg}\n`);
	process.exit(1);
};

/* ------------------------------------------------------------------ */
/* 1. Drift guard: the templates must still contain what we reproduce. */
/* ------------------------------------------------------------------ */

const HOOKS = {
	'header.php': ['class="cfhg-skip"', 'id="content" class="cfhg-content"'],
	'template-parts/site/header.php': ['cfhg-header__brand', 'cfhg-header__join', 'data-cfhg-menu-toggle', 'cfhg-menu__nav', 'cfhg-menu__foot'],
	'template-parts/site/footer.php': ['cfhg-footer__inner', 'cfhg-footer__badge', 'cfhg-footer__tagline', 'cfhg-footer__meeting', 'cfhg-footer__base', 'cfhg-footer__photo', 'cfhg-footer__copy'],
	'front-page.php': ['cfhg-main cfhg-main--home', 'template-parts/home/'],
	'template-parts/home/hero.php': ['cfhg-hero__inner', 'cfhg-hero__copy', 'cfhg-hero__title', 'cfhg-hero__cue', 'cfhg-hero__body', 'cfhg-hero__media cfhg-arch'],
	'template-parts/home/intro.php': ['cfhg-inline-tile', 'cfhg-statement', 'cfhg-intro__aside'],
	'template-parts/home/strip.php': ['cfhg-strip-wrap', 'cfhg-strip__item'],
	'template-parts/home/meeting.php': ['id="next-meeting"', 'cfhg-meeting__panel cfhg-on-dark', 'cfhg-meeting__when', 'cfhg-meeting__details', 'cfhg-meeting__media'],
	'template-parts/home/islands.php': ['cfhg-split-head', 'cfhg-islands__grid', 'cfhg-islands__list', 'cfhg-islands__media', 'id="cfhg-islands-title"'],
	'template-parts/home/start.php': ['id="start-here"', 'cfhg-start__grid', 'cfhg-start__intro', 'cfhg-start__media', 'cfhg-start__steps'],
	'template-parts/home/stories.php': ['cfhg-stories__head', 'cfhg_component_stories'],
	'template-parts/home/help.php': ['cfhg-help__grid', 'cfhg-help__media', 'cfhg-help__card'],
	'template-parts/home/join.php': ['id="join"', 'cfhg-join__panel', 'cfhg-join__copy', 'cfhg-join__media'],
	'template-parts/home/partners.php': ['cfhg-partners__head', 'cfhg_component_partners'],
	'template-parts/content/page.php': ['cfhg-page-hero', 'cfhg-page-hero__title', 'cfhg-page-hero__lead', 'cfhg-entry__media', 'cfhg-prose'],
	'template-parts/content/single.php': ['cfhg-story__tags', 'cfhg-page-hero__meta', 'cfhg-post-nav'],
	'template-parts/content/listing.php': ['cfhg-page-hero__lead', 'cfhg-listing', 'cfhg-stories__list', 'cfhg_component_story_card'],
	'404.php': ['cfhg-hero cfhg-hero--compact cfhg-notfound', 'cfhg-notfound__body'],
	'searchform.php': ['cfhg-search__input', 'cfhg-search__button'],
	'inc/components.php': ['cfhg-date__num', 'cfhg-facts__row', 'cfhg-facts__note', 'cfhg-ledger__row', 'cfhg-places__name', 'cfhg-steps__title', 'cfhg-story__body', 'cfhg-story__disc', 'cfhg-partner__name', 'cfhg-next__where'],
	'inc/template-tags.php': ['cfhg-btn__disc', 'cfhg-btn__label', "'cfhg-btn cfhg-btn--' . $variant"],
	'inc/class-cfhg-menu-walker.php': ['cfhg-menu__link', 'cfhg-menu__label', 'cfhg-menu__disc'],
};

for (const [file, hooks] of Object.entries(HOOKS)) {
	if (!fs.existsSync(path.join(THEME, file))) fail(`theme file missing: ${file}`);
	const src = read(file);
	const missing = hooks.filter((h) => !src.includes(h));
	if (missing.length) {
		fail(`${file} no longer contains ${missing.map((m) => `"${m}"`).join(', ')}.\nThe template has changed; update scripts/build-preview.mjs to match it.`);
	}
}

/* ------------------------------------------------------------------ */
/* 2. Content from the theme.                                          */
/* ------------------------------------------------------------------ */

const phpStr = (s) => s.replace(/\\'/g, "'").replace(/\\n/g, '\n');
const defaultsSrc = read('inc/defaults.php');

const D = {};
for (const m of defaultsSrc.matchAll(/'(\w+)'\s*=>\s*__\(\s*(['"])([\s\S]*?)(?<!\\)\2\s*,\s*'cfhg'\s*\)/g)) D[m[1]] = phpStr(m[3]);
for (const m of defaultsSrc.matchAll(/'(\w+)'\s*=>\s*'([^']*)',/g)) if (!(m[1] in D)) D[m[1]] = m[2];
for (const key of ['islands_list', 'start_steps', 'partners_list']) {
	const block = defaultsSrc.match(new RegExp(`'${key}'\\s*=>\\s*implode\\(([\\s\\S]*?)\\n\\t\\t\\),\\n`));
	if (!block) fail(`could not read ${key} from inc/defaults.php`);
	D[key] = [...block[1].matchAll(/__\(\s*'([\s\S]*?)(?<!\\)'\s*,\s*'cfhg'\s*\)/g)].map((m) => phpStr(m[1])).join('\n');
}
const REQUIRED = ['hero_kicker', 'hero_heading', 'hero_text', 'intro_heading', 'meeting_heading', 'meeting_short_where', 'meeting_skip_months', 'islands_list', 'start_steps', 'partners_list', 'join_heading', 'contact_email', 'footer_tagline', 'footer_photo_note', 'header_join_label'];
for (const k of REQUIRED) if (!(k in D)) fail(`could not read "${k}" from inc/defaults.php`);

const imagesSrc = read('inc/images.php');
const MANIFEST = {};
for (const m of imagesSrc.matchAll(/'([a-z0-9-]+)'\s*=>\s*array\(\s*'sizes'\s*=>\s*array\(([^)]*)\),\s*'alt'\s*=>\s*__\(\s*'([\s\S]*?)(?<!\\)',\s*'cfhg'\s*\),\s*'focus'\s*=>\s*'([^']+)'/g)) {
	const sizes = {};
	for (const s of m[2].matchAll(/(\d+)\s*=>\s*(\d+)/g)) sizes[+s[1]] = +s[2];
	MANIFEST[m[1]] = { sizes, alt: phpStr(m[3]), focus: m[4] };
}
const IMAGE_DEFAULTS = Object.fromEntries([...imagesSrc.matchAll(/'(\w+_image(?:_\d)?)'\s*=>\s*'([a-z0-9-]+)'/g)].map((m) => [m[1], m[2]]));
if (Object.keys(MANIFEST).length < 10) fail('could not read the image manifest from inc/images.php');

const lines = (key, n) =>
	D[key].split('\n').filter((l) => l.trim()).map((l) => {
		const parts = l.split('|').map((p) => p.trim());
		while (parts.length < n) parts.push('');
		return parts.slice(0, n);
	});

/* ------------------------------------------------------------------ */
/* 3. Links: theme addresses mapped to preview pages.                  */
/* ------------------------------------------------------------------ */

const PAGES = {
	home: 'index.html',
	page: 'who-we-are.html',
	archive: 'stories.html',
	single: 'tracing-ancestors-back-to-the-period-of-slavery.html',
	notfound: '404.html',
};

/* Every internal address the theme uses must be listed, or the build fails. */
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
	if (u in LINK_MAP) return LINK_MAP[u];
	/* Island guides are not part of the preview: point at the island list. */
	if (u.startsWith('/research/')) return `${PAGES.home}#cfhg-islands-title`;
	fail(`no preview page for the theme link "${u}". Add it to LINK_MAP.`);
}

/* ------------------------------------------------------------------ */
/* 4. Sample posts for the stories pages.                              */
/* ------------------------------------------------------------------ */

const POSTS = POST_COPY.map((p) => ({ ...p, href: p.href === 'single' ? PAGES.single : PAGES.archive }));

/* ------------------------------------------------------------------ */
/* 5. Markup helpers, mirroring inc/template-tags.php and components.  */
/* ------------------------------------------------------------------ */

const esc = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
const A = 'assets';
const ARROW = '<svg class="cfhg-arrow" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false"><path d="M3 10h13M11 4.5 16.5 10 11 15.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
const MAIL = `mailto:${D.contact_email}`;

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

function btn(label, u, variant = 'disc', cls = '') {
	const href = link(u);
	if (!label || !href) return '';
	const classes = `cfhg-btn cfhg-btn--${variant}${cls ? ' ' + cls : ''}`;
	if (variant === 'text') return `<a class="${classes}" href="${esc(href)}">${esc(label)}</a>`;
	return `<a class="${classes}" href="${esc(href)}"><span class="cfhg-btn__disc" aria-hidden="true">${ARROW}</span><span class="cfhg-btn__label">${esc(label)}</span></a>`;
}

const text = (s) => esc(s).replace(/\n/g, '<br>');

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

const nmInline = () => `<p class="cfhg-next"><span class="cfhg-next__label">${esc(D.meeting_kicker)}</span> <strong>${esc(nmLong)}</strong><span class="cfhg-next__where">${esc(D.meeting_short_where)}</span></p>`;

function storyCard(c, i) {
	const tones = ['sand', 'sand-deep', 'paper'];
	const tone = c.image ? tones[i % 3] : 'gold';
	const tags = c.tags.map((t, j) => `<li class="cfhg-tag${j === 0 ? ' cfhg-tag--first' : ''}">${esc(t)}</li>`).join('');
	return `<article class="cfhg-story cfhg-story--${tone}${c.image ? '' : ' cfhg-story--text'}">${tags ? `<ul class="cfhg-story__tags" aria-label="Topics">${tags}</ul>` : ''}${c.image ? `<div class="cfhg-story__media">${img(c.image, { sizes: '(min-width: 1024px) 30vw, 90vw' })}</div>` : ''}<div class="cfhg-story__body">${c.meta ? `<p class="cfhg-story__meta">${esc(c.meta)}</p>` : ''}<h3 class="cfhg-story__title"><a href="${esc(c.href)}">${esc(c.title)}</a></h3>${c.text ? `<p class="cfhg-story__text">${esc(c.text)}</p>` : ''}<span class="cfhg-story__disc" aria-hidden="true">${ARROW}</span></div></article>`;
}

/* ------------------------------------------------------------------ */
/* 6. Shared page frame: header.php, footer.php and their parts.       */
/* ------------------------------------------------------------------ */

const MENU = [
	['Research', '/research/'],
	['What’s On', '/whats-on/'],
	['Stories', '/stories/'],
	['Who We Are', '/who-we-are/'],
	['Join the group', '/join/'],
	['Contact', '/contact/'],
];
const PREVIEW_NAV = [
	['Home', PAGES.home],
	['Inner page', PAGES.page],
	['Stories', PAGES.archive],
	['Story', PAGES.single],
	['404', PAGES.notfound],
];

function header(current) {
	const items = MENU.map(([label, u]) => {
		const href = link(u);
		const isCurrent = href === current;
		return `<li class="cfhg-menu__item${isCurrent ? ' current-menu-item' : ''}"><a class="cfhg-menu__link" href="${esc(href)}"${isCurrent ? ' aria-current="page"' : ''}><span class="cfhg-menu__label">${esc(label)}</span><span class="cfhg-menu__disc" aria-hidden="true">${ARROW}</span></a></li>`;
	}).join('');
	return `<header class="cfhg-header" data-cfhg-header>
	<div class="cfhg-header__bar">
		<a class="cfhg-header__brand" href="${PAGES.home}" rel="home"><img src="${A}/images/cfhg-logo-horizontal.png" width="798" height="240" alt="Caribbean Family History Group" class="cfhg-logo"></a>
		<div class="cfhg-header__actions">
			<a class="cfhg-header__join" href="${esc(link(D.header_join_url))}">${esc(D.header_join_label)}</a>
			<button type="button" class="cfhg-menu-toggle" aria-expanded="false" aria-controls="cfhg-menu" data-cfhg-menu-toggle><span class="cfhg-menu-toggle__label">Menu</span><span class="cfhg-menu-toggle__disc" aria-hidden="true"><span></span><span></span></span></button>
		</div>
	</div>
	<div class="cfhg-menu" id="cfhg-menu" data-cfhg-menu>
		<nav class="cfhg-menu__nav" aria-label="Main"><ul class="cfhg-menu__list">${items}</ul></nav>
		<div class="cfhg-menu__foot">${nmInline()}<p class="cfhg-menu__email"><a href="${MAIL}">${esc(D.contact_email)}</a></p></div>
	</div>
</header>`;
}

function footer(current) {
	const links = MENU.map(([label, u]) => `<li><a href="${esc(link(u))}"><span>${esc(label)}</span>${ARROW}</a></li>`).join('');
	const preview = PREVIEW_NAV.map(([label, href]) => (href === current ? `<span aria-current="page">${label}</span>` : `<a href="${href}">${label}</a>`)).join(' ');
	return `<footer class="cfhg-footer">
	<div class="cfhg-footer__inner">
		<div class="cfhg-footer__brand">
			<a class="cfhg-footer__badge" href="${PAGES.home}" rel="home"><img src="${A}/images/cfhg-logo-badge.png" width="511" height="512" alt="Caribbean Family History Group" loading="lazy" decoding="async"></a>
			<p class="cfhg-footer__tagline">${text(D.footer_tagline)}</p>
			<a class="cfhg-footer__email" href="${MAIL}">${esc(D.contact_email)}</a>
		</div>
		<nav class="cfhg-footer__nav" aria-label="Footer"><ul class="cfhg-footer__links">${links}</ul></nav>
		<div class="cfhg-footer__meeting">
			<p class="cfhg-kicker">${esc(D.meeting_kicker)}</p>
			<p class="cfhg-footer__date">${esc(nmLong)}</p>
			<p class="cfhg-footer__venue">${esc(D.meeting_short_where)}</p>
			${btn(D.join_cta_label, D.join_cta_url, 'disc')}
		</div>
	</div>
	<div class="cfhg-footer__base">
		<p class="cfhg-footer__photo">${esc(D.footer_photo_note)} <a href="${esc(link(D.footer_photo_url))}">Photography and video policy</a></p>
		<ul class="cfhg-footer__legal"><li><a href="${esc(link(D.privacy_url))}">Privacy and cookies</a></li></ul>
		<p class="cfhg-footer__copy">&copy; ${new Date().getFullYear()} Caribbean Family History Group</p>
	</div>
	<p class="cfhg-preview-label"><strong>Design preview.</strong> Not the live site. <span class="cfhg-preview-label__pages">${preview}</span></p>
</footer>`;
}

/* Styles for the preview label only. The theme CSS is untouched. */
const PREVIEW_CSS = `.cfhg-preview-label{max-width:var(--cfhg-max);margin:var(--cfhg-sp-lg) auto 0;padding-top:var(--cfhg-sp-sm);border-top:1px dashed var(--cfhg-line);font-size:.75rem;line-height:1.5;color:var(--cfhg-ink-soft)}.cfhg-preview-label strong{font-weight:600;color:var(--cfhg-ink)}.cfhg-preview-label__pages{display:inline-flex;flex-wrap:wrap;gap:.25rem .75rem;margin-left:.5rem}.cfhg-preview-label [aria-current]{text-decoration:underline;text-decoration-style:dotted}`;

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
	<link rel="preload" href="${A}/fonts/montagu-slab-var.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="${A}/fonts/hanken-grotesk-var.woff2" as="font" type="font/woff2" crossorigin>
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

/* ------------------------------------------------------------------ */
/* 7. Pages.                                                           */
/* ------------------------------------------------------------------ */

function homepage() {
	const hero = `<section class="cfhg-hero" id="top" aria-labelledby="cfhg-hero-title"><div class="cfhg-hero__inner"><div class="cfhg-hero__copy">
<p class="cfhg-kicker cfhg-kicker--plain cfhg-hero__kicker">${text(D.hero_kicker)}</p>
<h1 class="cfhg-display cfhg-hero__title" id="cfhg-hero-title">${esc(D.hero_heading)}</h1>
<div class="cfhg-hero__foot"><a class="cfhg-hero__cue" href="#cfhg-intro"><span class="cfhg-hero__cue-label">${esc(D.hero_cue_label)}</span><span class="cfhg-hero__cue-line" aria-hidden="true"></span></a>
<div class="cfhg-hero__body"><p class="cfhg-lead">${esc(D.hero_text)}</p><div class="cfhg-actions">${btn(D.hero_primary_label, D.hero_primary_url, 'solid')}${btn(D.hero_secondary_label, D.hero_secondary_url, 'text')}</div>${nmInline()}</div></div></div>
<figure class="cfhg-hero__media cfhg-arch">${settingImg('hero_image', { sizes: '(min-width: 1024px) 40vw, 92vw', loading: 'eager' })}</figure></div></section>`;

	const tile = `<span class="cfhg-inline-tile">${settingImg('intro_tile_image', { sizes: '120px', alt: '' })}</span>`;
	const intro = `<section class="cfhg-section cfhg-intro" id="cfhg-intro"><div class="cfhg-container"><p class="cfhg-kicker" data-reveal>${esc(D.intro_kicker)}</p>
<h2 class="cfhg-h1 cfhg-statement" data-reveal>${esc(D.intro_heading).replace('{tile}', tile)}</h2>
<div class="cfhg-intro__aside" data-reveal><p>${esc(D.intro_text)}</p>${btn(D.intro_link_label, D.intro_link_url)}</div></div></section>`;

	const strip = `<section class="cfhg-strip-wrap" aria-label="${esc(D.strip_label)}"><div class="cfhg-strip" tabindex="0">${[1, 2, 3, 4, 5].map((i) => `<figure class="cfhg-strip__item cfhg-strip__item--${i}">${settingImg(`strip_image_${i}`, { sizes: '(min-width: 1024px) 30vw, 72vw' })}</figure>`).join('')}</div></section>`;

	const facts = `<dl class="cfhg-facts">${[['Where', D.meeting_venue], ['Online', D.meeting_online], ['When', D.meeting_pattern], ['Time', D.meeting_time]].filter(([, v]) => v).map(([k, v]) => `<div class="cfhg-facts__row"><dt>${k}</dt><dd>${text(v)}</dd></div>`).join('')}</dl>${D.meeting_note ? `<p class="cfhg-facts__note">${text(D.meeting_note)}</p>` : ''}`;
	const prices = `<dl class="cfhg-ledger">${[['price_first_label', 'price_first', 'is-free'], ['price_visit_label', 'price_visit', ''], ['price_year_label', 'price_year', '']].map(([a, b, c]) => `<div class="cfhg-ledger__row ${c}"><dt>${esc(D[a])}</dt><dd>${esc(D[b])}</dd></div>`).join('')}</dl>`;
	const meeting = `<section class="cfhg-section cfhg-meeting" id="next-meeting" aria-labelledby="cfhg-meeting-title"><div class="cfhg-container"><div class="cfhg-meeting__panel cfhg-on-dark" data-reveal>
<div class="cfhg-meeting__when"><p class="cfhg-kicker">${esc(D.meeting_kicker)}</p><p class="cfhg-date"><span class="cfhg-date__day">${fmt(NM, { weekday: 'long' })}</span><time class="cfhg-date__num" datetime="${iso(NM)}">${NM.getDate()}</time><span class="cfhg-date__month">${fmt(NM, { month: 'long', year: 'numeric' })}</span></p></div>
<div class="cfhg-meeting__details"><h2 class="cfhg-h2" id="cfhg-meeting-title">${esc(D.meeting_heading)}</h2>${facts}${prices}<div class="cfhg-actions">${btn(D.meeting_cta_label, D.meeting_cta_url, 'solid')}${btn(D.meeting_secondary_label, MAIL, 'text')}</div></div>
<figure class="cfhg-meeting__media cfhg-rounded">${settingImg('meeting_image', { sizes: '(min-width: 1024px) 26vw, 92vw' })}</figure></div></div></section>`;

	const places = `<ul class="cfhg-places">${lines('islands_list', 3).map(([n, u, note]) => {
		const inner = `<strong class="cfhg-places__name">${esc(n)}</strong>${note ? ` <span class="cfhg-places__note">${esc(note)}</span>` : ''}`;
		return u ? `<li><a class="cfhg-places__link" href="${esc(link(u))}">${inner}</a></li>` : `<li><span class="cfhg-places__link">${inner}</span></li>`;
	}).join('')}</ul>`;
	const islands = `<section class="cfhg-section cfhg-islands" aria-labelledby="cfhg-islands-title"><div class="cfhg-container"><div class="cfhg-split-head" data-reveal><div><p class="cfhg-kicker">${esc(D.islands_kicker)}</p><h2 class="cfhg-h2" id="cfhg-islands-title">${esc(D.islands_heading)}</h2></div><p class="cfhg-split-head__text">${esc(D.islands_text)}</p></div>
<div class="cfhg-islands__grid"><div class="cfhg-islands__list">${places}</div><figure class="cfhg-islands__media cfhg-rounded" data-reveal>${settingImg('islands_image', { sizes: '(min-width: 1024px) 55vw, 92vw' })}</figure></div></div></section>`;

	const steps = `<ol class="cfhg-steps">${lines('start_steps', 2).map(([t, x]) => `<li class="cfhg-steps__item"><h3 class="cfhg-steps__title">${esc(t)}</h3><p class="cfhg-steps__text">${esc(x)}</p></li>`).join('')}</ol>`;
	const start = `<section class="cfhg-section cfhg-start" id="start-here" aria-labelledby="cfhg-start-title"><div class="cfhg-container cfhg-start__grid"><div class="cfhg-start__intro"><p class="cfhg-kicker">${esc(D.start_kicker)}</p><h2 class="cfhg-h2" id="cfhg-start-title">${esc(D.start_heading)}</h2><p class="cfhg-lead">${esc(D.start_text)}</p>
<figure class="cfhg-start__media cfhg-rounded">${settingImg('start_image', { sizes: '(min-width: 1024px) 34vw, 92vw' })}</figure></div><div class="cfhg-start__steps">${steps}${btn(D.start_link_label, D.start_link_url)}</div></div></section>`;

	/* Posts exist in the preview, so the section shows them, as the theme does. */
	const latest = POSTS.slice(0, 3).map((p) => ({ tags: p.cats.filter((c) => c !== 'Stories').slice(0, 2), meta: postDate(p.date), title: p.title, text: p.excerpt, image: p.image, href: p.href }));
	const stories = `<section class="cfhg-section cfhg-stories" aria-labelledby="cfhg-stories-title"><div class="cfhg-container"><div class="cfhg-stories__head" data-reveal><div><p class="cfhg-kicker">${esc(D.stories_kicker)}</p><h2 class="cfhg-h1" id="cfhg-stories-title">${esc(D.stories_heading)}</h2></div>${btn(D.stories_link_label, D.stories_link_url)}</div>
<div class="cfhg-stories__list">${latest.map(storyCard).join('')}</div></div></section>`;

	const help = `<section class="cfhg-section cfhg-help" aria-labelledby="cfhg-help-title"><div class="cfhg-container cfhg-help__grid"><figure class="cfhg-help__media cfhg-rounded" data-reveal>${settingImg('help_image', { sizes: '(min-width: 1024px) 60vw, 92vw' })}</figure>
<div class="cfhg-help__card" data-reveal><p class="cfhg-kicker">${esc(D.help_kicker)}</p><h2 class="cfhg-h2" id="cfhg-help-title">${esc(D.help_heading)}</h2><p>${esc(D.help_text)}</p>${btn(D.help_cta_label, D.help_cta_url, 'solid')}</div></div></section>`;

	const join = `<section class="cfhg-section cfhg-join" id="join" aria-labelledby="cfhg-join-title"><div class="cfhg-container"><div class="cfhg-join__panel" data-reveal><div class="cfhg-join__copy"><p class="cfhg-kicker">${esc(D.join_kicker)}</p><h2 class="cfhg-h1" id="cfhg-join-title">${esc(D.join_heading)}</h2><p class="cfhg-lead">${esc(D.join_text)}</p>
<div class="cfhg-actions">${btn(D.join_cta_label, D.join_cta_url, 'solid')}${btn(D.join_secondary_label, MAIL, 'text')}</div></div><figure class="cfhg-join__media cfhg-rounded">${settingImg('join_image', { sizes: '(min-width: 1024px) 45vw, 92vw' })}</figure></div></div></section>`;

	const partnerList = `<ul class="cfhg-partners__list">${lines('partners_list', 3).map(([n, t, u]) => `<li class="cfhg-partner"><h3 class="cfhg-partner__name">${u ? `<a href="${esc(link(u))}" rel="noopener">${esc(n)}</a>` : esc(n)}</h3><p class="cfhg-partner__text">${esc(t)}</p></li>`).join('')}</ul>`;
	const partners = `<section class="cfhg-section cfhg-partners" aria-labelledby="cfhg-partners-title"><div class="cfhg-container"><div class="cfhg-partners__head" data-reveal><p class="cfhg-kicker">${esc(D.partners_kicker)}</p><h2 class="cfhg-h2" id="cfhg-partners-title">${esc(D.partners_heading)}</h2></div>${partnerList}</div></section>`;

	return page({
		file: PAGES.home,
		title: 'Caribbean Family History Group',
		description: D.hero_text,
		bodyClass: 'home page cfhg cfhg-overlay-header cfhg-home',
		main: `<main id="main" class="cfhg-main cfhg-main--home">${hero}${intro}${strip}${meeting}${islands}${start}${stories}${help}${join}${partners}</main>`,
	});
}

/* template-parts/content/page.php */
function innerPage() {
	const prose = innerProse({ mail: MAIL, email: esc(D.contact_email) });

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
		<figure class="cfhg-container cfhg-entry__media cfhg-rounded">${img('meeting-at-the-library-table', { sizes: '(min-width: 1200px) 1200px, 92vw', style: 'aspect-ratio:16/9;object-fit:cover' })}</figure>
		<div class="cfhg-container"><div class="cfhg-prose">${prose}</div></div>
	</article>
</main>`,
	});
}

/* template-parts/content/listing.php */
function archive() {
	const cards = POSTS.map((p, i) => storyCard({ tags: p.cats.slice(0, 2), meta: postDate(p.date), title: p.title, text: p.excerpt, image: p.image, href: p.href }, i)).join('');
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

/* template-parts/content/single.php */
function single() {
	const p = POSTS[0];
	const tags = p.cats.slice(0, 3).map((c, i) => `<li class="cfhg-tag${i === 0 ? ' cfhg-tag--first' : ''}"><a href="${PAGES.archive}">${esc(c)}</a></li>`).join('');
	const prose = singleProse({ home: PAGES.home });

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
		<figure class="cfhg-container cfhg-entry__media cfhg-rounded">${img(p.image, { sizes: '(min-width: 1200px) 1200px, 92vw', style: 'aspect-ratio:16/9;object-fit:cover' })}</figure>
		<div class="cfhg-container"><div class="cfhg-prose">${prose}</div></div>
		<nav class="cfhg-container cfhg-post-nav" aria-label="More stories">${btn(POSTS[1].title, '/stories/', 'disc', 'cfhg-post-nav__next')}</nav>
	</article>
</main>`,
	});
}

/* 404.php */
function notFound() {
	return page({
		file: PAGES.notfound,
		title: 'Page not found | Caribbean Family History Group',
		description: 'This page could not be found.',
		bodyClass: 'error404 cfhg cfhg-overlay-header',
		main: `<main id="main" class="cfhg-main">
	<section class="cfhg-hero cfhg-hero--compact cfhg-notfound" aria-labelledby="cfhg-404-title"><div class="cfhg-hero__inner"><div class="cfhg-hero__copy">
		<p class="cfhg-kicker cfhg-kicker--plain">Page not found</p>
		<h1 class="cfhg-display cfhg-hero__title" id="cfhg-404-title">This branch of the tree doesn’t lead anywhere</h1>
		<div class="cfhg-notfound__body">
			<p class="cfhg-lead">The page may have moved, or the link may be out of date. Try searching, or head back to the homepage.</p>
			<form role="search" method="get" class="cfhg-search" action="${PAGES.archive}">
				<label class="screen-reader-text" for="cfhg-search-1">Search the site</label>
				<input type="search" id="cfhg-search-1" class="cfhg-search__input" name="s" value="" placeholder="Search for a name, island or topic">
				<button type="submit" class="cfhg-search__button"><span class="screen-reader-text">Search</span><span class="cfhg-btn__disc" aria-hidden="true">${ARROW}</span></button>
			</form>
			<div class="cfhg-actions">${btn('Back to the homepage', '/', 'solid')}</div>
		</div>
	</div></div></section>
</main>`,
	});
}

/* ------------------------------------------------------------------ */
/* 8. Write the folder.                                                */
/* ------------------------------------------------------------------ */

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

const pages = {
	[PAGES.home]: homepage(),
	[PAGES.page]: innerPage(),
	[PAGES.archive]: archive(),
	[PAGES.single]: single(),
	[PAGES.notfound]: notFound(),
};
for (const [file, html] of Object.entries(pages)) {
	if (/[—–]/.test(html)) fail(`${file} contains an em or en dash`);
	fs.writeFileSync(path.join(OUT, file), html);
}

fs.writeFileSync(path.join(OUT, 'robots.txt'), 'User-agent: *\nDisallow: /\n');
fs.writeFileSync(path.join(OUT, MARKER), `Built ${new Date().toISOString()} from wordpress/cfhg-option-a. Do not edit; run npm run build:preview.\n`);

const size = (dir) => fs.readdirSync(dir, { withFileTypes: true }).reduce((t, e) => t + (e.isDirectory() ? size(path.join(dir, e.name)) : fs.statSync(path.join(dir, e.name)).size), 0);
console.log(`Preview built in ${path.relative(ROOT, OUT)}/ (${(size(OUT) / 1e6).toFixed(1)} MB)`);
console.log(`  pages: ${Object.keys(pages).join(', ')}`);
console.log(`  next meeting shown: ${nmLong} (fixed at build time; rebuild to refresh)`);
