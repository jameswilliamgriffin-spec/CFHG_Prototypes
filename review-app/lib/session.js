import { createHmac, timingSafeEqual } from 'node:crypto';

export function sessionValue() {
  const secret = process.env.REVIEW_ACCESS_CODE;
  return secret ? createHmac('sha256', secret).update('cfhg-review-session-v1').digest('hex') : null;
}
export function authorized(req) {
  const wanted = sessionValue();
  const found = (req.headers.cookie || '').match(/(?:^|;\s*)cfhg_review_session=([a-f0-9]+)/)?.[1];
  return !!(wanted && found && found.length === wanted.length && timingSafeEqual(Buffer.from(found), Buffer.from(wanted)));
}
export function reject(res) { res.statusCode = 401; res.setHeader('Content-Type', 'application/json'); res.end(JSON.stringify({error:'Access required'})); }
