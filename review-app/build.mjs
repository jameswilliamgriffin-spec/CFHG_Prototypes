import { cpSync, mkdirSync, readdirSync, readFileSync, writeFileSync, rmSync } from 'node:fs';
import { join, relative, dirname } from 'node:path';

const root = dirname(new URL(import.meta.url).pathname);
const source = join(root, '..', 'wp-export');
const dist = join(root, 'dist');
rmSync(dist, { recursive: true, force: true });
mkdirSync(dist, { recursive: true });
cpSync(source, join(dist, 'site'), { recursive: true });
for (const asset of ['wp-content', 'wp-includes', 'calendar']) cpSync(join(source, asset), join(dist, asset), { recursive: true });
for (const file of ['index.html', 'app.js', 'style.css']) cpSync(join(root, 'src', file), join(dist, file));
const pages = [];
function walk(dir) {
  for (const entry of readdirSync(dir, { withFileTypes: true })) {
    const path = join(dir, entry.name);
    if (entry.isDirectory()) walk(path);
    else if (entry.name === 'index.html') {
      const rel = relative(source, path).replaceAll('\\', '/');
      const slug = rel === 'index.html' ? '/' : '/' + rel.slice(0, -'index.html'.length);
      const html = readFileSync(path, 'utf8');
      const raw = html.match(/<h1\b[^>]*>([\s\S]*?)<\/h1>/i)?.[1] || html.match(/<title>([\s\S]*?)<\/title>/i)?.[1] || slug;
      const title = raw.replace(/<[^>]*>/g, ' ').replace(/&amp;/g, '&').replace(/&#8217;|&#039;/g, '’').replace(/\s+/g, ' ').trim();
      pages.push({ path: slug, title });
    }
  }
}
walk(source);
pages.sort((a,b) => a.path === '/' ? -1 : b.path === '/' ? 1 : a.path.localeCompare(b.path));
const titleCounts = new Map();
for (const page of pages) titleCounts.set(page.title, (titleCounts.get(page.title) || 0) + 1);
for (const page of pages) if (titleCounts.get(page.title) > 1) {
  const date = page.path.match(/(\d{4}-\d{2}-\d{2})/);
  page.title += date ? ` · ${date[1]}` : ` · ${page.path}`;
}
writeFileSync(join(dist, 'pages.json'), JSON.stringify(pages));
writeFileSync(join(root, 'pages.json'), JSON.stringify(pages, null, 2) + '\n');
console.log(`Built review app with ${pages.length} pages.`);
