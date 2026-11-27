import { timingSafeEqual } from 'node:crypto';
import { sessionValue } from '../lib/session.js';
export default function handler(req,res) {
  if (req.method !== 'POST') { res.statusCode=405; return res.end(); }
  const expected = process.env.REVIEW_ACCESS_CODE;
  const supplied = String(req.body?.code || '');
  if (!expected || supplied.length !== expected.length || !timingSafeEqual(Buffer.from(supplied), Buffer.from(expected))) {
    res.statusCode=401; res.setHeader('Content-Type','application/json'); return res.end(JSON.stringify({error:'Incorrect access code'}));
  }
  const secure = /localhost|127\.0\.0\.1/.test(req.headers.host || '') ? '' : ' Secure;';
  res.setHeader('Set-Cookie', `cfhg_review_session=${sessionValue()}; HttpOnly;${secure} SameSite=Lax; Path=/; Max-Age=2592000`);
  res.setHeader('Content-Type','application/json'); res.end(JSON.stringify({ok:true}));
}
