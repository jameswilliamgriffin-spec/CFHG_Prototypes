import { createServer } from 'node:http';
import { readFileSync, existsSync } from 'node:fs';
import { extname, join, normalize } from 'node:path';
import login from './api/login.js';import feedback from './api/feedback.js';import exportData from './api/export.js';
process.env.LOCAL_REVIEW_STORE='1';process.env.REVIEW_ACCESS_CODE ||= 'local-review';
const root=new URL('./dist/',import.meta.url).pathname;
const types={'.html':'text/html','.js':'text/javascript','.css':'text/css','.json':'application/json','.png':'image/png','.jpg':'image/jpeg','.jpeg':'image/jpeg','.webp':'image/webp','.svg':'image/svg+xml','.woff2':'font/woff2','.ics':'text/calendar'};
const handlers={'/api/login':login,'/api/feedback':feedback,'/api/export':exportData};
createServer(async(req,res)=>{
  const url=new URL(req.url,'http://localhost');
  if(handlers[url.pathname]){
    req.query=Object.fromEntries(url.searchParams);
    if(req.method==='POST'){let body='';for await(const part of req)body+=part;if(body.length>10000){res.statusCode=413;return res.end();}try{req.body=JSON.parse(body);}catch{res.statusCode=400;return res.end();}}
    return handlers[url.pathname](req,res);
  }
  const file=join(root,normalize(decodeURIComponent(url.pathname)).replace(/^\/+/,''));
  const target=existsSync(file)&&!file.endsWith('/')&&extname(file)?file:join(file,'index.html');
  if(!target.startsWith(root)||!existsSync(target)){res.statusCode=404;return res.end('Not found');}
  res.setHeader('Content-Type',types[extname(target)]||'application/octet-stream');res.end(readFileSync(target));
}).listen(3108,'127.0.0.1',()=>console.log('Review app at http://127.0.0.1:3108/ (local access code: local-review)'));
