#!/usr/bin/env node
/**
 * Builds the whole client site for Vercel:
 *
 *   npm run build:site            every preview that is safe to rebuild
 *   npm run build:site -- b       only option B (a, b and c can be combined)
 *
 * 1. Rebuilds the static previews (/preview, /preview-b, /preview-c)
 *    from their WordPress themes.
 * 2. Adds site/meeting-dates.js to every preview page, so "next meeting"
 *    dates stay current after deployment.
 * 3. Checks the landing page links to all three previews.
 *
 * A preview folder that still holds README-ui-pass.md has hand-made polish
 * that is not in its theme yet. Rebuilding would wipe it, so that preview is
 * skipped until the polish is ported (as option B's was, into
 * wordpress/cfhg-option-b; see its docs/UI-PASS.md).
 *
 * The output is committed; Vercel serves it as plain static files.
 */

import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync, readdirSync, existsSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');

const PREVIEWS = [
	{ key: 'a', dir: 'preview', script: 'build-preview.mjs' },
	{ key: 'b', dir: 'preview-b', script: 'build-preview-b.mjs' },
	{ key: 'c', dir: 'preview-c', script: 'build-preview-c.mjs' },
];

const wanted = process.argv.slice(2).map((a) => a.toLowerCase());
const unknown = wanted.filter((k) => !PREVIEWS.some((p) => p.key === k));
if (unknown.length) {
	console.error(`Unknown option ${unknown.join(', ')}. Use a, b and/or c.`);
	process.exit(1);
}

const TAG = '<script src="../site/meeting-dates.js" defer></script>';

for (const { key, dir, script } of PREVIEWS) {
	if (wanted.length && !wanted.includes(key)) continue;
	if (existsSync(join(ROOT, dir, 'README-ui-pass.md'))) {
		console.log(`Skipping ${dir}: its hand-made polish (README-ui-pass.md) is not in the theme yet, and rebuilding would wipe it.`);
		continue;
	}
	console.log(`Building ${dir}`);
	execFileSync(process.execPath, [join(ROOT, 'scripts', script)], { stdio: ['ignore', 'ignore', 'inherit'] });

	let count = 0;
	for (const file of readdirSync(join(ROOT, dir)).filter((f) => f.endsWith('.html'))) {
		const path = join(ROOT, dir, file);
		const html = readFileSync(path, 'utf8');
		if (html.includes(TAG)) continue;
		if (!html.includes('</body>')) throw new Error(`${dir}/${file} has no </body>`);
		writeFileSync(path, html.replace('</body>', `${TAG}\n</body>`));
		count++;
	}
	console.log(`  ${count} pages updated`);
}

const landing = readFileSync(join(ROOT, 'index.html'), 'utf8');
for (const { dir } of PREVIEWS) {
	if (!landing.includes(`href="${dir}/"`)) throw new Error(`index.html does not link to ${dir}/`);
	if (!existsSync(join(ROOT, dir, 'index.html'))) throw new Error(`${dir}/index.html is missing`);
}

console.log('Site ready: index.html, preview/, preview-b/, preview-c/');
