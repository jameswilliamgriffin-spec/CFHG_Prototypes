import { authorized, reject } from '../lib/session.js';
import { getProgress, setProgress } from '../store.mjs';
import pages from '../pages.json' with { type: 'json' };
const validPages = new Set(pages.map(page => page.path));
export default async function handler(req, res) {
  if (!authorized(req)) return reject(res);
  res.setHeader('Content-Type', 'application/json'); res.setHeader('Cache-Control', 'no-store');
  const body = req.method === 'GET' ? req.query : req.body;
  const reviewerId = body?.reviewerId;
  if (!/^[a-f0-9-]{36}$/.test(reviewerId || '')) { res.statusCode = 400; return res.end(JSON.stringify({ error: 'Invalid reviewer' })); }
  try {
    if (req.method === 'GET') return res.end(JSON.stringify({ progress: await getProgress(reviewerId) }));
    if (req.method !== 'POST') { res.statusCode = 405; return res.end(); }
    const { page, reviewed, name } = body;
    if (!validPages.has(page) || typeof reviewed !== 'boolean' || typeof name !== 'string' || !name.trim() || name.length > 80) {
      res.statusCode = 400; return res.end(JSON.stringify({ error: 'Invalid review status' }));
    }
    await setProgress(reviewerId, page, reviewed, name.trim());
    res.end(JSON.stringify({ ok: true, reviewed }));
  } catch (error) { console.error(error); res.statusCode = 503; res.end(JSON.stringify({ error: 'Could not save review status' })); }
}
