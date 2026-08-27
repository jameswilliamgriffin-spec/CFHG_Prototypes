# CFHG Website Redesign — Pitch Presentation

18 slides, 16:9. This is the clean slide-by-slide content, signed off in full. (For production notes, graphics checklist, and image prompts, see `Presentation/pitch-deck-structure.md` and `prompts.md`.)

---

## 1. Title
Website Redesign Proposal
Caribbean Family History Group
[Your name / agency name]
[Date]

*No logo on this slide — logo choice comes later, at Slide 8–9.*

---

## 2. Purpose — why now

**A site that's done its job — built for what's next**

The current site has done exactly what it needed to: it's kept members connected, published news and events, and given CFHG a home online for years. But the organisation — and the audience it's trying to reach — has moved on. A new generation of Caribbean family historians expects something fresh, interactive, and easy to use on a phone, not just a desktop. This isn't about replacing what worked — it's about meeting your community where it is now.

**Fresh & modern · Interactive & engaging · Built for the next generation**

---

## 3. Where the site is today
Full-bleed screenshot of caribbeanfamilyhistorygroup.org.

Callouts: "Built years ago, hard to update" · "No mobile-first design" · "News and events go stale between updates"

---

## 4. Heirloom — the idea
*Split page: text on one side, image on the other.*

Heirloom treats your website like a family archive brought to life — sepia photographs, ship manifests, postmarks and hand-written records, framed with warmth and care. It's designed to feel like opening a treasured family album: personal, credible, and rooted in the real documents that make up a family history journey.

**Archival photography · Editorial serif type · Warm, reflective tone**

Image: `hero.png`

---

## 5. Heirloom — homepage concept
Full-bleed reveal of the homepage mockup.

Image: `ref.png`

---

## 6. Horizon — the idea
*Same split-page structure as Slide 4, so the two directions read as a matched pair.*

Horizon reflects where CFHG's community is heading — vibrant, current, and as easy to explore from a phone as a research desk. Built around real photography of members, meetings and events, told in the bold colours of the Caribbean flag, it's designed to feel like joining a living, active community rather than browsing an archive.

**Bold Caribbean palette · Contemporary photography · Social, forward-facing tone**

Image: `horizon-hero.png` (generate via the v2 prompt in `prompts.md`)

---

## 7. Horizon — homepage concept
Full-bleed reveal of the homepage mockup, same treatment as Slide 5.

Image: generate via the "Option B homepage mockup" prompt in `prompts.md`

---

## 8. A refreshed identity — concept 1, the palm tree
Current logo shown briefly for contrast, then the palm-tree mark in two formats: a horizontal lockup for the site header, a circular badge for social/favicon use.

Images: `Logo ideas/logo 1.png`, `Logo ideas/logo 1.1.png`

---

## 9. A refreshed identity — concept 2, the family tree
A distinct second mark — a family tree with a sunrise/horizon motif (navy, gold, teal), shown draft-to-refined.

Images: `Logo ideas/logo2.png`, `Logo ideas/logo 2.1.png`

---

## 10. How your volunteers will actually update it
A scoped-down Sanity Studio — when volunteers log in they see a short, clearly labeled menu showing only the sections relevant to them: News, Events, AGM, Research Dates, Sub-Group Dates, Gallery, Exhibitions, Homepage Highlights.

**Training:** 3–4 volunteers trained to start. A live Zoom/in-person walkthrough at launch, plus a screen-recorded video they keep.

Image: `sanity-cms-menu.png` (shared with Slide 12)

---

## 11. Archive strategy
- Homepage shows the latest 3 news items / upcoming events only.
- "View all →" links through to a full archive page listing everything, past and present.
- The archive page is filterable — by category, and likely by year or island.
- Everything currently on WordPress is migrated over at launch.
- Nothing is ever deleted.

*[Graphic to be dropped in later]*

---

## 12. Sanity — the recommendation
- Fully bespoke — built around CFHG's actual sitemap, not a generic template.
- Editors see only the sections they need — easy for people to update relevant sections.
- Fast, modern, pairs natively with Next.js — very fast site performance.
- No plugins to maintain or patch — smaller security surface.
- Free tier comfortably covers this project.
- Faster to build out a large number of custom page types than fighting a theme/page-builder.

Image: Sanity logo + `sanity-cms-menu.png`

---

## 13. WordPress — for comparison
**Pros:** extremely well-known; huge plugin ecosystem; runs a large share of the web; cheap, widely available hosting.

**Cons:** slower to build — with a sitemap this size, that adds up to meaningfully longer development time. Requires ongoing plugin/core updates and security patching. Generic admin exposes every field, easier to accidentally break something.

Image: WordPress logo + cluttered admin mockup (prompt in `prompts.md`)

---

## 14. Hosting: Vercel
The same infrastructure Next.js itself is built by — fast, reliable, zero-maintenance hosting.

Vercel's free tier is for personal/non-commercial projects, and because we're being paid to build and maintain this site, it needs a paid Pro plan. We already run our own projects on Vercel Pro, so we'll host CFHG on our existing account — **no separate hosting bill for CFHG.** A named contact will be added as a collaborator for visibility, and hosting/transfer terms are covered in our agreement.

Image: Vercel logotype

---

## 15. Price

| | |
|---|---|
| **Website design & build** | **£400** ~~normally £1,000+~~ — community rate |
| **Includes** | Full design, scoped-down Sanity CMS build, migration of existing content, volunteer training (live + video), launch |
| **Hosting** | Included — hosted on our existing Vercel Pro account, no separate bill to CFHG |
| **Ongoing support** | 5 hours/year included. Beyond that: **£20/hr** ~~normally £60/hr~~ by agreement |
| **Payment** | 50% to begin, 50% on completion |

---

## 16. Timeline
*(Based on a Monday 17 Aug start)*

- **17–23 Aug (Week 1):** Discovery & content collection — confirm sitemap details, collect copy/photos from CFHG, agree direction (Heirloom or Horizon) and logo.
- **24 Aug–13 Sep (Weeks 2–4): Design & build** — homepage and key templates designed and signed off, then built out across the same window. Content migrated from WordPress in parallel.
- **14–20 Sep (Week 5):** Final QA, DNS switch, launch.
- **21–27 Sep (Week 6):** Training — live walkthrough + video guide, on the real, live site.

---

## 17. What we need from you to start
- ✅ Sitemap
- ✅ Domain access
- ✅ Existing site
- 🔲 Final copy/text for static pages (Who We Are, Constitution, Partners, DNA guidance, Research Tips)
- 🔲 Photo/image archive (originals, not just what's on the current site)
- 🔲 A single point of contact for content sign-off and Q&A during build
- 🔲 Confirmation of the 3–4 volunteers to be trained on the CMS

---

## 18. Thank you
Thank you.

[Contact details]
