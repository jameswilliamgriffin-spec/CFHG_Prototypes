import { readFileSync, writeFileSync } from 'node:fs';
const prefix = 'cfhg-review:v1:';
const localPath = new URL('.local-feedback.json', import.meta.url);

async function command(parts) {
  const url = process.env.KV_REST_API_URL || process.env.UPSTASH_REDIS_REST_URL;
  const token = process.env.KV_REST_API_TOKEN || process.env.UPSTASH_REDIS_REST_TOKEN;
  if (!url || !token) throw new Error('Database is not configured');
  const response = await fetch(url, { method: 'POST', headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' }, body: JSON.stringify(parts) });
  if (!response.ok) throw new Error(`Database request failed: ${response.status}`);
  const data = await response.json();
  if (data.error) throw new Error('Database command failed');
  return data.result;
}
function localRead() { try { return JSON.parse(readFileSync(localPath, 'utf8')); } catch { return {}; } }
export async function getPage(page) {
  if (process.env.LOCAL_REVIEW_STORE === '1') return localRead()[page] || {};
  const fields = await command(['HGETALL', prefix + 'page:' + page]);
  if (!fields) return {};
  if (Array.isArray(fields)) return Object.fromEntries(Array.from({length:fields.length/2},(_,i)=>[fields[i*2],fields[i*2+1]]));
  return fields;
}
export async function saveNote(page, field, note) {
  if (process.env.LOCAL_REVIEW_STORE === '1') {
    const all = localRead(); all[page] ||= {}; all[page][field] = JSON.stringify(note); writeFileSync(localPath, JSON.stringify(all)); return;
  }
  await command(['HSET', prefix + 'page:' + page, field, JSON.stringify(note)]);
  await command(['SADD', prefix + 'pages', page]);
}
export async function getAll() {
  if (process.env.LOCAL_REVIEW_STORE === '1') return localRead();
  const pages = await command(['SMEMBERS', prefix + 'pages']) || [];
  const result = {};
  for (const page of pages) result[page] = await getPage(page);
  return result;
}
