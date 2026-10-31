#!/usr/bin/env node
/**
 * Builds the whole client site for Vercel:
 *
 *   npm run build:site
 *
 * 1. Rebuilds the three static previews (/preview, /preview-b, /preview-c)
 *    from their WordPress themes.
 * 2. Adds site/meeting-dates.js to every preview page, so "next meeting"
 *    dates stay current after deployment.
 * 3. Checks the landing page links to all three previews.
 *
 * The output is committed; Vercel serves it as plain static files.
 */

import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync, readdirSync, existsSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');

const PREVIEWS = [
	{ dir: 'preview', script: 'build-preview.mjs' },
	{ dir: 'preview-b', script: 'build-preview-b.mjs' },
	{ dir: 'preview-c', script: 'build-preview-c.mjs' },
];

const TAG = '<script src="../site/meeting-dates.js" defer></script>';

for (const { dir, script } of PREVIEWS) {
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
