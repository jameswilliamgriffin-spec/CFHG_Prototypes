import { authorized, reject } from '../lib/session.js';
import { getPage, saveNote } from '../store.mjs';
import pages from '../pages.json' with { type: 'json' };
const validPages = new Set(pages.map(p=>p.path));
export default async function handler(req,res) {
  if (!authorized(req)) return reject(res);
  res.setHeader('Content-Type','application/json'); res.setHeader('Cache-Control','no-store');
  try {
    const page = req.method === 'GET' ? req.query?.page : req.body?.page;
    if (!validPages.has(page)) { res.statusCode=400; return res.end(JSON.stringify({error:'Unknown page'})); }
    if (req.method === 'GET') return res.end(JSON.stringify({notes:await getPage(page)}));
    if (req.method !== 'POST') { res.statusCode=405; return res.end(); }
    const {blockId, reviewerId, name, text} = req.body || {};
    if (!/^[a-zA-Z0-9:_./-]{1,240}$/.test(blockId||'') || !/^[a-f0-9-]{36}$/.test(reviewerId||'') || typeof name !== 'string' || !name.trim() || name.length>80 || typeof text !== 'string' || text.length>5000) {
      res.statusCode=400; return res.end(JSON.stringify({error:'Invalid note'}));
    }
    const note = {blockId, reviewerId, name:name.trim(), text, updatedAt:new Date().toISOString()};
    await saveNote(page, reviewerId+':'+blockId, note);
    res.end(JSON.stringify({ok:true,note}));
  } catch (error) { console.error(error); res.statusCode=503; res.end(JSON.stringify({error:'Could not save or load feedback'})); }
}
