f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c = open(f, 'r', encoding='utf-8').read()

# 1) loadNews এ "আরো দেখুন" বাটন সরাই + infinite scroll যোগ
old_loadnews_end = """  newsLoaded = true;
  window._newsOffset = 10;
  load72hBar('newsPostsWrap');
  if (res.posts.length >= 10) {
    document.getElementById('newsPostsWrap').insertAdjacentHTML('beforeend',
      '<div id="newsLoadMore" style="text-align:center;padding:1rem;">' +
      '<button onclick="loadMoreNews()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">' +
      'খেদমতের খবর আরো দেখুন</button></div>');
  }
}"""

new_loadnews_end = """  newsLoaded = true;
  window._newsOffset = res.posts.length;
  load72hBar('newsPostsWrap');
  // Infinite scroll
  window._filterState = window._filterState || {};
  window._filterState['newsPostsWrap'] = {hours: 0, cat: '', offset: res.posts.length, done: res.posts.length < 10, loading: false};
  const oldSen = document.getElementById('scroll_sentinel_newsPostsWrap');
  if (oldSen) oldSen.remove();
  if (res.posts.length >= 10) {
    const sen = document.createElement('div');
    sen.id = 'scroll_sentinel_newsPostsWrap';
    sen.style.height = '10px';
    document.getElementById('newsPostsWrap').appendChild(sen);
    const obs = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting) return;
      const st = window._filterState['newsPostsWrap'];
      if (!st || st.done || st.loading) return;
      st.loading = true;
      const r2 = await api('get_public_news', {limit: 10, offset: st.offset});
      if (r2.success && r2.posts.length) {
        const isMasked2 = r2.masked || false;
        const catMap2 = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
        const html2 = r2.posts.map(p => {
          const ct = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(cc=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap2[cc]||cc}</span>`).join('');
          return `<div class="post-card"><div class="post-date">${fmtDtNews(p.created)}</div>${ct?`<div style="padding:0.3rem 0.85rem;">${ct}</div>`:''}<div class="post-body">${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${isMasked2?highlightMasked(escHtml(p.content)):escHtml(p.content)}</div><div class="mask-info" onclick="showPage('home');setTimeout(openDetail,300)">🔒 সম্পূর্ণ নাম্বারসহ দেখতে পাসওয়ার্ড সংগ্রহ করুন</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
        }).join('');
        sen.insertAdjacentHTML('beforebegin', html2);
        st.offset += r2.posts.length;
        if (r2.posts.length < 10) { st.done = true; obs.disconnect(); sen.remove(); }
      } else { st.done = true; obs.disconnect(); sen.remove(); }
      st.loading = false;
    }, {rootMargin: '300px'});
    obs.observe(sen);
  }
}"""

# 2) applyFilter এ limit 200 → 10 + infinite scroll
old_apply_limit = "  const params = {limit: 200, offset: 0, hours: hours};"
new_apply_limit = "  const params = {limit: 10, offset: 0, hours: hours};"

old_apply_end = "  if (containerId === 'newsPostsWrap') { newsLoaded = true; window._newsOffset = res.posts.length; }\n}"
new_apply_end = """  if (containerId === 'newsPostsWrap') { newsLoaded = true; window._newsOffset = res.posts.length; }
  // Infinite scroll for filter
  window._filterState = window._filterState || {};
  window._filterState[containerId] = {hours: hours, cat: cat, offset: res.posts.length, done: res.posts.length < 10, loading: false};
  const oldSen2 = document.getElementById('scroll_sentinel_' + containerId);
  if (oldSen2) oldSen2.remove();
  if (res.posts.length >= 10) {
    const sen2 = document.createElement('div');
    sen2.id = 'scroll_sentinel_' + containerId;
    sen2.style.height = '10px';
    wrap.appendChild(sen2);
    const obs2 = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting) return;
      const st2 = window._filterState[containerId];
      if (!st2 || st2.done || st2.loading) return;
      st2.loading = true;
      const p2 = {limit: 10, offset: st2.offset, hours: st2.hours};
      if (st2.cat) p2.category = st2.cat;
      const r3 = await api('get_public_news', p2);
      if (r3.success && r3.posts.length) {
        const isMasked3 = r3.masked || false;
        const catMap3 = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
        const html3 = r3.posts.map(p => {
          const ct3 = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(cc=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap3[cc]||cc}</span>`).join('');
          return `<div class="post-card"><div class="post-date">${fmtDtNews(p.created)}</div>${ct3?`<div style="padding:0.3rem 0.85rem;">${ct3}</div>`:''}<div class="post-body">${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${isMasked3?highlightMasked(escHtml(p.content)):escHtml(p.content)}</div><div class="mask-info" onclick="showPage('home');setTimeout(openDetail,300)">🔒 সম্পূর্ণ নাম্বারসহ দেখতে পাসওয়ার্ড সংগ্রহ করুন</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
        }).join('');
        sen2.insertAdjacentHTML('beforebegin', html3);
        st2.offset += r3.posts.length;
        if (r3.posts.length < 10) { st2.done = true; obs2.disconnect(); sen2.remove(); }
      } else { st2.done = true; obs2.disconnect(); sen2.remove(); }
      st2.loading = false;
    }, {rootMargin: '300px'});
    obs2.observe(sen2);
  }
}"""

changes = [
    (old_loadnews_end, new_loadnews_end, 'loadNews infinite scroll'),
    (old_apply_limit, new_apply_limit, 'applyFilter limit'),
    (old_apply_end, new_apply_end, 'applyFilter infinite scroll'),
]

for old, new, name in changes:
    if old in c:
        c = c.replace(old, new, 1)
        print('ok:', name)
    else:
        print('NOT FOUND:', name)

open(f, 'w', encoding='utf-8').write(c)
print('done')
