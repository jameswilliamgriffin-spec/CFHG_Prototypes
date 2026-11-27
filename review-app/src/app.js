const $ = id => document.getElementById(id);
let pages = [], current = 0, blocks = [], notes = {};
const reviewerId = localStorage.getItem('cfhg-reviewer-id') || crypto.randomUUID();
localStorage.setItem('cfhg-reviewer-id', reviewerId);
$('name').value = localStorage.getItem('cfhg-reviewer-name') || '';
$('name').addEventListener('input', e => localStorage.setItem('cfhg-reviewer-name', e.target.value));

async function api(path, options) {
  const response = await fetch('/api/' + path, { credentials:'same-origin', ...options });
  if (response.status === 401) { $('app').hidden = true; $('login').hidden = false; throw new Error('Access required'); }
  const data = await response.json();
  if (!response.ok) throw new Error(data.error || 'Request failed');
  return data;
}
function pathFor(node, main) {
  const parts = [];
  while (node && node !== main) {
    const siblings = [...node.parentElement.children].filter(el => el.tagName === node.tagName);
    parts.unshift(node.tagName.toLowerCase() + (siblings.length > 1 ? ':' + siblings.indexOf(node) : ''));
    node = node.parentElement;
  }
  return parts.join('/');
}
function scanPreview() {
  const doc = $('preview').contentDocument;
  if (!doc) return [];
  const main = doc.querySelector('main');
  if (!main) return [];
  doc.addEventListener('click', e => { if (e.target.closest('a')) e.preventDefault(); }, true);
  const candidates = [...main.querySelectorAll('section, article, li, p')];
  const result = candidates.filter(el => {
    const text = el.innerText?.replace(/\s+/g,' ').trim() || '';
    if (text.length < 20 || el.closest('nav,form')) return false;
    if (el.tagName === 'P' && el.closest('li')) return false;
    if (el.tagName === 'SECTION' && el.querySelectorAll('section').length > 0 && !el.querySelector(':scope > h1,:scope > h2,:scope > h3')) return false;
    return true;
  }).map(el => {
    const type = el.tagName === 'LI' ? 'Row' : el.tagName === 'P' ? 'Text' : el.tagName === 'ARTICLE' ? 'Article' : 'Section';
    const title = el.querySelector('h1,h2,h3,h4')?.innerText?.trim() || el.getAttribute('aria-label') || el.innerText.replace(/\s+/g,' ').trim().slice(0,65);
    return { id:pathFor(el,main), type, title, preview:el.innerText.replace(/\s+/g,' ').trim().slice(0,190), element:el };
  });
  if (!result.length) result.push({id:'main',type:'Page',title:pages[current].title,preview:main.innerText.slice(0,190),element:main});
  return result;
}
function noteValues() {
  return Object.values(notes).map(value => { try { return JSON.parse(value); } catch { return null; } }).filter(Boolean);
}
function renderBlocks() {
  const pagePath = pages[current].path;
  $('block-count').textContent = blocks.length + ' blocks';
  const list = $('feedback-list'); list.replaceChildren();
  const all = noteValues();
  for (const block of blocks) {
    const card = document.createElement('article'); card.className='block';
    const head = document.createElement('div'); head.className='block-head';
    const title = document.createElement('p'); title.className='block-title'; title.textContent=block.title;
    const type = document.createElement('span'); type.className='block-type'; type.textContent=block.type;
    head.append(title,type);
    const preview = document.createElement('p'); preview.className='block-preview'; preview.textContent=block.preview;
    const editor = document.createElement('textarea'); editor.placeholder='Type a change or request…'; editor.setAttribute('aria-label','Feedback for '+block.title);
    editor.value = all.find(n => n.blockId === block.id && n.reviewerId === reviewerId)?.text || '';
    const status = document.createElement('div'); status.className='save-state';
    let timer, version=0;
    async function save() {
      const name = $('name').value.trim();
      if (!name) { status.textContent='Enter your name to save'; $('name').focus(); return; }
      const thisVersion=++version; status.textContent='Saving…';
      try {
        const result=await api('feedback',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({page:pagePath,blockId:block.id,reviewerId,name,text:editor.value})});
        notes[reviewerId+':'+block.id]=JSON.stringify(result.note);
        if (thisVersion===version) status.textContent='Saved';
      } catch { if (thisVersion===version) status.textContent='Could not save — edit to retry'; }
    }
    editor.addEventListener('input',()=>{clearTimeout(timer);status.textContent='Unsaved';timer=setTimeout(()=>{timer=null;save();},800);});
    editor.addEventListener('blur',()=>{if(timer){clearTimeout(timer);timer=null;save();}});
    card.addEventListener('click',e=>{if(e.target===editor)return;block.element.scrollIntoView({behavior:'smooth',block:'center'});block.element.style.outline='3px solid #2d8584';setTimeout(()=>block.element.style.outline='',1800);});
    card.append(head,preview,editor,status);
    for (const note of all.filter(n=>n.blockId===block.id && n.reviewerId!==reviewerId && n.text)) {
      const other=document.createElement('div');other.className='other-note';const author=document.createElement('strong');author.textContent=note.name;other.append(author,document.createTextNode(note.text));card.append(other);
    }
    list.append(card);
  }
}
function renderPages() {
  const filter=$('page-search').value.toLowerCase();const nav=$('pages');nav.replaceChildren();
  pages.forEach((page,index)=>{if(!(page.title+' '+page.path).toLowerCase().includes(filter))return;const button=document.createElement('button');button.type='button';button.textContent=page.title;button.className=index===current?'active':'';button.onclick=()=>openPage(index);nav.append(button);});
  $('page-count').textContent=`${current+1} of ${pages.length} pages`;
}
async function openPage(index) {
  current=index;const page=pages[index];history.replaceState(null,'','#'+encodeURIComponent(page.path));
  $('page-title').textContent=page.title;$('live-link').href='https://cfhgprototypes.vercel.app'+page.path;
  $('previous').disabled=index===0;$('next').disabled=index===pages.length-1;
  renderPages();$('feedback-list').replaceChildren();$('block-count').textContent='Loading…';
  const frame=$('preview');frame.onload=async()=>{
    blocks=scanPreview();
    try { notes=(await api('feedback?page='+encodeURIComponent(page.path))).notes; } catch { notes={}; }
    renderBlocks();
  };
  frame.src='/site'+page.path;
}
$('previous').onclick=()=>openPage(current-1);$('next').onclick=()=>openPage(current+1);
$('page-search').addEventListener('input',renderPages);
$('login-form').addEventListener('submit',async e=>{e.preventDefault();$('login-error').textContent='';try{await api('login',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({code:$('code').value})});$('login').hidden=true;$('app').hidden=false;await start();}catch(error){$('login-error').textContent=error.message;}});
async function start() {
  pages=await (await fetch('/pages.json')).json();
  const hash=decodeURIComponent(location.hash.slice(1));
  await openPage(Math.max(0,pages.findIndex(p=>p.path===hash)));
}
try { await api('feedback?page=%2F'); $('app').hidden=false; await start(); } catch { $('login').hidden=false; }
