import { authorized, reject } from '../lib/session.js';
import { getAll } from '../store.mjs';
export default async function handler(req,res) {
  if (!authorized(req)) return reject(res);
  try { res.setHeader('Content-Type','application/json'); res.setHeader('Content-Disposition','attachment; filename="cfhg-feedback.json"'); res.end(JSON.stringify(await getAll(),null,2)); }
  catch(error) { console.error(error); res.statusCode=503; res.end(JSON.stringify({error:'Could not export feedback'})); }
}
