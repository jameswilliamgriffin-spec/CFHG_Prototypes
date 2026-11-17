#!/usr/bin/env node
/**
 * Exports the local WordPress site (Local, http://cfhg.local) as plain
 * static files, for the client review copy on Vercel.
 *
 *   node scripts/export-wordpress.mjs            crawl and write wp-export/
 *   node scripts/export-wordpress.mjs --base http://cfhg.local --out wp-export
 *
 * It starts at the homepage and follows every link on the site, saving each
 * page as folder/index.html, then fetches the CSS, scripts, images, fonts and
 * video those pages use. Along the way:
 *
 *   - addresses are made root-relative, so the copy works on any domain;
 *   - links to pages that don't exist yet (the Research section) are sent to
 *     /coming-soon/, the page of that name in WordPress;
 *   - links that WordPress redirects are rewritten to where they end up;
 *   - "Add to my calendar" links become static .ics files under /calendar/;
 *   - the unused WordPress extras (REST, oEmbed, feeds) are left out.
 *
 * What a static copy can't do: the search box, anything that needs WordPress
 * to run. The surname filter, parish map, "Where should I look?", the
 * lightbox and the timeline all run in the browser and work.
 *
 * The local site must be running in Local. Nothing here changes WordPress.
 */

import { mkdirSync, writeFileSync, rmSync, existsSync, readFileSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const arg = (name, fallback) => {
	const i = process.argv.indexOf(`--${name}`);
	return i > -1 ? process.argv[i + 1] : fallback;
};
const BASE = arg('base', 'http://cfhg.local').replace(/\/$/, '');
const OUT = join(ROOT, arg('out', 'wp-export'));
const HOST = new URL(BASE).host;
const COMING = '/coming-soon/';

const SKIP_PAGE = /^\/(wp-admin|wp-login|wp-json|wp-content|wp-includes|feed|comments|xmlrpc|author|tag|category|island|elementor-home-test|sample-page)(\/|\.|$)|\/feed\/?$|\.(php|xml)$/;

const status = new Map(); // path -> 200 | 404 | { redirect: path }
const pages = new Map(); // path -> html
const ics = new Map(); // id -> text
const assets = new Set();
const queue = ['/'];

async function get(url, init = {}) {
	for (let attempt = 0; attempt < 3; attempt++) {
		try {
			return await fetch(url, { redirect: 'manual', ...init });
		} catch (e) {
			if (attempt === 2) throw e;
			await new Promise((r) => setTimeout(r, 400));
		}
	}
}

const toPath = (href) => {
	if (/^(mailto:|tel:|javascript:|data:|webcal:|#)/i.test(href)) return null;
	let u;
	try {
		u = new URL(href.replace(/&#0?38;|&amp;/g, '&'), BASE + '/');
	} catch {
		return null;
	}
	if (u.host !== HOST) return null;
	return u;
};

/* 1. Crawl the pages. The coming-soon page is only fetched if something links nowhere. */
for (let pass = 0; pass < 2; pass++) {
if (pass === 1) {
	if (![...status.values()].includes(404) || status.has(COMING)) break;
	queue.push(COMING);
}
while (queue.length) {
	const path = queue.shift();
	if (status.has(path)) continue;
	const res = await get(BASE + path);
	if (res.status >= 300 && res.status < 400) {
		const target = toPath(res.headers.get('location') || '');
		const to = target ? target.pathname : null;
		status.set(path, to && to !== path ? { redirect: to } : 404);
		if (to && !status.has(to)) queue.push(to);
		continue;
	}
	status.set(path, res.status);
	if (res.status !== 200 || !(res.headers.get('content-type') || '').includes('text/html')) continue;
	const html = await res.text();
	pages.set(path, html);
	for (const m of html.matchAll(/(?:href|action)="([^"]+)"/g)) {
		const u = toPath(m[1]);
		if (!u) continue;
		if (u.searchParams.has('cfhg_ics')) {
			ics.set(u.searchParams.get('cfhg_ics'), null);
			continue;
		}
		if (u.search) continue; // ?s=, ?replytocom= and the like
		if (SKIP_PAGE.test(u.pathname) || /\.[a-z0-9]{2,4}$/i.test(u.pathname)) continue;
		if (!status.has(u.pathname)) queue.push(u.pathname);
	}
}
}
const nf = await get(`${BASE}/this-page-does-not-exist-404/`);
if (nf.status === 404) pages.set('/__404__', await nf.text());
console.log(`Crawled ${pages.size} pages, ${[...status.values()].filter((s) => s === 404).length} links to pages that don't exist yet.`);

/* 2. Calendar files. */
if (!ics.has('feed')) ics.set('feed', null);
for (const id of ics.keys()) {
	const res = await get(`${BASE}/?cfhg_ics=${id}`);
	if (res.status === 200) ics.set(id, await res.text());
}

/* Where does a path end up? Follows redirects; null if it is a 404. */
const resolve = (path) => {
	for (let i = 0; i < 5; i++) {
		const s = status.get(path);
		if (s && typeof s === 'object') path = s.redirect;
		else return s === 200 ? path : null;
	}
	return null;
};

/* 3. Rewrite every page. */
const hostRe = new RegExp(`https?:(?:\\\\?/){2}${HOST.replace(/\./g, '\\.')}`, 'g');
const rewritten = new Map();
for (const [path, original] of pages) {
	let html = original;
	html = html.replace(/<link[^>]+(?:api\.w\.org|oembed|xmlrpc|EditURI|rel=["']alternate["'][^>]+(?:rss|atom))[^>]*>\s*/gi, '');
	html = html.replace(/<link[^>]+rel=["']shortlink["'][^>]*>\s*/gi, '');
	html = html.replace(/<link[^>]+(?:application\/json|\/feed\/)[^>]*>\s*/gi, '');
	/* Category, tag and author pages aren't part of the site: keep the words, drop the link. */
	html = html.replace(/<a\b[^>]*href="(?:https?:\/\/[^"\/]+)?\/(?:category|tag|author)\/[^"]*"[^>]*>(.*?)<\/a>/gis, '<span>$1</span>');
	/* Calendar links first, while the host is still in the address. */
	html = html.replace(/(?:webcal|https?):\/\/[^"'\s<>]*?\?cfhg_ics=(\d+|feed)(?:&#0?38;[^"'\s<>]*|&amp;[^"'\s<>]*)?/g, (_, id) => `/calendar/${id}.ics`);
	html = html.replace(/(href|action)="([^"]*)"/g, (whole, attr, value) => {
		if (/^\/calendar\//.test(value)) return whole;
		const u = toPath(value);
		if (!u || u.search) return whole;
		if (/\.[a-z0-9]{2,4}$/i.test(u.pathname) || SKIP_PAGE.test(u.pathname)) return whole;
		const target = resolve(u.pathname);
		return `${attr}="${(target || COMING)}${u.hash}"`;
	});
	html = html.replace(hostRe, '');
	rewritten.set(path, html);
	for (const m of html.matchAll(/(?:^|["'(=,\s])(\/(?:wp-content|wp-includes)\/[^"'\s<>)?\\#]+)/g)) assets.add(m[1]);
	/* The same paths inside JSON, where the slashes are escaped (the looping films). */
	for (const m of html.matchAll(/"(\\\/wp-content\\\/[^"?#]+\.(?:mp4|webm|webp|jpg|png|svg|woff2?))/g)) assets.add(m[1].replace(/\\\//g, '/'));
}

/* 4. Assets, and what the CSS files in turn use. */
const saved = new Set();
const assetQueue = [...assets];
const writeFile = (rel, data) => {
	const file = join(OUT, rel);
	mkdirSync(dirname(file), { recursive: true });
	writeFileSync(file, data);
};
rmSync(OUT, { recursive: true, force: true });
mkdirSync(OUT, { recursive: true });

let missing = 0;
while (assetQueue.length) {
	const path = decodeURI(assetQueue.shift());
	if (saved.has(path)) continue;
	saved.add(path);
	const res = await get(BASE + encodeURI(path));
	if (res.status !== 200) {
		missing++;
		console.warn(`  missing ${res.status} ${path}`);
		continue;
	}
	let body = Buffer.from(await res.arrayBuffer());
	if (/\.(css|js)$/i.test(path)) {
		let text = body.toString('utf8').replace(hostRe, '');
		if (path.endsWith('.css')) {
			for (const m of text.matchAll(/url\(\s*["']?([^"')]+?)["']?\s*\)|@import\s+["']([^"']+)["']/g)) {
				const ref = (m[1] || m[2] || '').trim();
				if (!ref || /^(data:|#|https?:\/\/(?!cfhg))/i.test(ref)) continue;
				const abs = ref.startsWith('/') ? ref : new URL(ref, 'http://x' + path).pathname;
				assetQueue.push(abs.split('?')[0].split('#')[0]);
			}
		}
		body = Buffer.from(text);
	}
	writeFile(path.replace(/^\//, ''), body);
}

/* 5. Pages, calendar files and the not-found page. */
for (const [path, html] of rewritten) writeFile(path === '/__404__' ? '404.html' : join(path.replace(/^\//, ''), 'index.html'), html);
for (const [id, text] of ics) if (text) writeFile(`calendar/${id}.ics`, text);

writeFile('robots.txt', 'User-agent: *\nDisallow: /\n');

console.log(`Wrote ${rewritten.size - 1} pages, ${saved.size - missing} files and ${[...ics.values()].filter(Boolean).length} calendar files to ${OUT.replace(ROOT + '/', '')}${missing ? ` (${missing} files could not be fetched)` : ''}.`);
