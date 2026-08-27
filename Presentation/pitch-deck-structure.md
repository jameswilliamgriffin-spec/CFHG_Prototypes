# CFHG Website Pitch — Deck Structure & Production Plan

Format target: 16:9, 1920×1080 per slide. 18 slides, built to answer their 5 questions directly plus your above-and-beyond material. Anything in `[BRACKETS]` is a placeholder only you can fill in — everything else is drafted content you can drop into Figma as-is or lightly edit.

Note: the 4 logo image files live in `Logo ideas/` at the project root (not in `Presentation/`) — `logo 1.png`, `logo 1.1.png`, `logo2.png`, `logo 2.1.png`.

---

## SLIDE-BY-SLIDE STRUCTURE

### 1. Title — LOCKED
- **Content:** "Website Redesign Proposal", CFHG name (plain text, not a logo mark — no logo concept should appear before the logo-choice slide), your name/agency name, date.
- **Graphic:** None — clean typographic title slide.
- **Tone:** Confident, warm, not corporate.

### 2. Purpose — why now — LOCKED
- **Headline:** "A site that's done its job — built for what's next"
- **Body:** "The current site has done exactly what it needed to: it's kept members connected, published news and events, and given CFHG a home online for years. But the organisation — and the audience it's trying to reach — has moved on. A new generation of Caribbean family historians expects something fresh, interactive, and easy to use on a phone, not just a desktop. This isn't about replacing what worked — it's about meeting your community where it is now."
- **Three pillars underneath:** Fresh & modern · Interactive & engaging · Built for the next generation
- **Placement rationale:** Sits before the "where the site is today" screenshot so the arc reads why now → here's the starting point → here's the vision, rather than leading with a screenshot that could feel like criticism of the volunteers who built it.
- **Graphic:** Simple typographic slide, no bespoke art needed.

### 3. Where the site is today
- **Content:** Full-bleed screenshot of the live site. One or two short callouts (not paragraphs): "Built years ago, hard to update," "No mobile-first design," "News and events go stale between updates."
- **Purpose:** Sets the stakes without insulting the volunteers who built it — frame as "you've outgrown it," not "this is bad."
- **Graphic:** 🔲 Need tonight — clean screenshot of https://caribbeanfamilyhistorygroup.org/ homepage.

### 4. Heirloom — the idea
*(Answers client Q4 — look & feel. Shown as one of two example directions, not a decided outcome)*
- **Layout:** Split page — overview text on one side, image on the other.
- **Overview text:** "Heirloom treats your website like a family archive brought to life — sepia photographs, ship manifests, postmarks and hand-written records, framed with warmth and care. It's designed to feel like opening a treasured family album: personal, credible, and rooted in the real documents that make up a family history journey."
- **Style tags:** Archival photography · Editorial serif type · Warm, reflective tone
- **Graphic:** ✅ Have it — `hero.png`, on the opposite side of the page to the text.

### 5. Heirloom — homepage concept
- **Content:** Full-bleed reveal of the homepage UI mockup. Minimal or no text — let it land.
- **Graphic:** ✅ Have it — `ref.png`. Crop 2–3 detail shots (hero, "Begin with what you know" cards, "Explore by island" row) alongside the full reveal if there's room.

### 6. Horizon — the idea
*(Second example direction — deliberate contrast to Heirloom)*
- **Layout:** Same split-page structure as Slide 4, so the two directions read as a matched pair.
- **Overview text:** "Horizon reflects where CFHG's community is heading — vibrant, current, and as easy to explore from a phone as a research desk. Built around real photography of members, meetings and events, told in the bold colours of the Caribbean flag, it's designed to feel like joining a living, active community rather than browsing an archive."
- **Style tags:** Bold Caribbean palette · Contemporary photography · Social, forward-facing tone
- **Graphic:** 🔲 Need tonight — new mood/flat-lay image, the Horizon equivalent of `hero.png`. Prompt in `prompts.md` ("Horizon — mood/concept image").

### 7. Horizon — homepage concept
- **Content:** Full-bleed reveal of the homepage UI mockup, same treatment as Slide 5.
- **Graphic:** 🔲 Need tonight — generate from the "Option B homepage mockup" prompt in `prompts.md`, then clean up nav/headline text by hand in Figma since image models render text unreliably.

### 8. A refreshed identity — concept 1, the palm tree
- **Content:** Current logo shown briefly for contrast, then the palm-tree mark in two lockup formats — a horizontal version for the site header, a circular badge version for social/favicon use.
- **Graphic:** ✅ Have both — `Logo ideas/logo 1.png` (horizontal) and `Logo ideas/logo 1.1.png` (circular badge). Current rainbow-palm logo cropped from the "where the site is today" screenshot, for contrast.

### 9. A refreshed identity — concept 2, the family tree
- **Content:** A distinct second mark — a family tree with a sunrise/horizon motif (navy, gold, teal), shown draft-to-refined. Ties naturally to the "Horizon" naming used elsewhere in the deck.
- **Graphic:** ✅ Have both — `Logo ideas/logo2.png` (draft/outline) and `Logo ideas/logo 2.1.png` (refined/solid, the stronger of the two).

### 10. How your volunteers will actually update it — LOCKED
*(Answers client Q2 — maintenance/cross-training)*
- **Content:** A scoped-down Sanity Studio — when volunteers log in they see a short, clearly labeled menu showing *only* the sections relevant to them, with icons next to each: News, Events, AGM, Research Dates, Sub-Group Dates, Gallery, Exhibitions, Homepage Highlights. Not a generic 30-field admin panel, but not a bespoke wizard/tile UI either — a clean, real, buildable-at-this-rate Studio.
- **Training:** 3–4 volunteers trained to start. A live Zoom/in-person walkthrough at launch, **plus a screen-recorded video** they keep — matters more for a volunteer group than a typical client, since whoever takes over "Events" in two years won't have been in the live session.
- **Graphic:** 🔲 Need tonight — `sanity-cms-menu.png`, same asset as Slide 12's Sanity mockup (Prompt 1 under "CMS backend comparison" in `prompts.md`). Generate once, use on both slides.

### 11. Archive strategy — LOCKED
*(Answers client Q3)*
- **Content:**
  - Homepage shows the latest 3 news items / upcoming events only.
  - "View all →" links through to a full archive page listing everything, past and present.
  - The archive page is **filterable** — by category (News / Events / Exhibitions) and likely by year or island, given the sitemap's structure.
  - Everything currently on WordPress is migrated over at launch, so the archive has real history in it from day one, not a blank slate.
  - Nothing is ever deleted.
- **Graphic:** 🔲 Need tonight — simple mockup of the archive page with filter controls (chips or dropdowns) above a list/grid of past items.

### 12. Sanity — the recommendation — LOCKED
- Fully bespoke — built around CFHG's actual sitemap, not a generic template.
- Editors see only the sections they need (easy for people to update relevant sections).
- Fast, modern, pairs natively with Next.js — very fast site performance.
- No plugins to maintain or patch — smaller security surface.
- Free tier comfortably covers this project (20 seats, 10k documents).
- Faster to build out a large number of custom page types, since it's structured content + code rather than fighting a theme/page-builder.
- **Graphic:** ✅ Sanity logo in folder. New: calm 8-item admin mockup — Prompt 1 in `prompts.md`.

### 13. WordPress — for comparison — LOCKED
- *Pros:* extremely well-known (head start on familiarity for volunteers who've used it elsewhere); huge plugin ecosystem (contact forms, SEO, event calendars as free installs, no custom dev); runs a large share of the web (mature, easy to hand off to another developer later); cheap, widely available hosting at every price point.
- *Cons:* slower to build — theme/page-builder work takes longer per page than a structured, code-driven build; with a sitemap this size (multiple country pages, sub-groups, research sections), that adds up to meaningfully longer development time. Requires ongoing plugin/core updates and security patching. Generic admin exposes every field — easier for a non-technical volunteer to accidentally break something.
- **Graphic:** ✅ WordPress logo in folder. New: cluttered generic admin mockup — Prompt 2 in `prompts.md`. Pair side-by-side with Slide 12's mockup for direct contrast.

### 14. Hosting: Vercel
- **Content:** 
  - What it is: the same infrastructure Next.js itself is built by — fast, reliable, zero-maintenance hosting.
  - **Honest cost line:** "Vercel's free tier is for personal/non-commercial projects, and because we're being paid to build and maintain this site, it needs a paid Pro plan. We already run our own projects on Vercel Pro, so we'll host CFHG on our existing account — **no separate hosting bill for CFHG.** You'll have a named contact added as a collaborator for visibility, and hosting/transfer terms are covered in our agreement."
  - This is a genuine value-add (worth stating plainly as one) rather than a workaround — it shows you did your homework and pass the saving on.
- **Graphic:** ✅ Vercel logotype already in folder. Optional: simple 3-node stack diagram (Next.js + Tailwind → Sanity CMS → Vercel hosting) — nice-to-have, not essential.

### 15. Price — LOCKED
| | |
|---|---|
| **Website design & build** | **£400** ~~normally £1,000+~~ — community rate |
| **Includes** | Full design, scoped-down Sanity CMS build, migration of existing content, volunteer training (live + video), launch |
| **Hosting** | Included — hosted on our existing Vercel Pro account, no separate bill to CFHG |
| **Ongoing support** | 5 hours/year included. Beyond that: **£20/hr** ~~normally £60/hr~~ by agreement |
| **Payment** | 50% to begin, 50% on completion |

- Show the strikethrough comparison subtly (small note, not a big callout) — there to point to if asked, not the headline.
- **Graphic:** Simple table/card layout, no bespoke art.

### 16. Timeline — LOCKED
*(Answers client Q5, based on a Monday 17 Aug start)*
  - **17–23 Aug (Week 1):** Discovery & content collection — confirm sitemap details, collect copy/photos from CFHG, agree direction (Heirloom or Horizon) and logo.
  - **24 Aug–13 Sep (Weeks 2–4): Design & build** — homepage and key templates designed and signed off, then built out across the same window. Content migrated from WordPress in parallel.
  - **14–20 Sep (Week 5):** Final QA, DNS switch, launch.
  - **21–27 Sep (Week 6):** Training — live walkthrough + video guide, on the real, live site.
- **Graphic:** Horizontal milestone timeline, 4 nodes.

### 17. What we need from you to start — LOCKED
*(Second half of Q5 — deliverables beyond sitemap/domain/existing site)*
- **Content:**
  - ✅ Sitemap (have it)
  - ✅ Domain access (have it / confirm registrar login when ready)
  - ✅ Existing site (have it)
  - 🔲 Final copy/text for static pages (Who We Are, Constitution, Partners, DNA guidance, Research Tips)
  - 🔲 Photo/image archive (originals, not just what's on the current site)
  - 🔲 A single point of contact for content sign-off and Q&A during build
  - 🔲 Confirmation of the 3–4 volunteers to be trained on the CMS
- **Graphic:** Simple checklist layout.

### 18. Thank you — LOCKED
- **Content:** Simple closing slide — "Thank you" plus contact details. No recap list.
- **Graphic:** None.

---

## TONIGHT'S GRAPHICS PRODUCTION CHECKLIST (priority order)

| # | Asset | Status | Notes |
|---|---|---|---|
| 1 | Current site "before" screenshot | 🔲 Need | Clean full-page screenshot of caribbeanfamilyhistorygroup.org homepage. |
| 2 | Horizon homepage mockup (Slide 7) | 🔲 Need | Run the "Option B homepage mockup" prompt in `prompts.md` through ChatGPT image generation, then clean up text by hand in Figma. Highest-priority new asset — the whole Heirloom/Horizon contrast depends on it existing. |
| 3 | Horizon mood/idea image (Slide 6) | 🔁 Retry | First attempt came back too generic/"tropical holiday," not brand-specific. Use the **v2 prompt** in `prompts.md` (solid brand-colour props instead of floral fabric, candid not posed, no phone/app). Save as `horizon-hero.png`. |
| 4 | Sanity CMS menu mockup (Slides 10 + 12, shared asset) | 🔲 Need | Prompt 1 under "CMS backend comparison" in `prompts.md`. Save as `sanity-cms-menu.png`, reuse on both slides — same asset, no need to generate twice. |
| 5 | WordPress backend mockup (Slide 13) | 🔲 Need | Prompt 2 under "CMS backend comparison" in `prompts.md`. Pair with #4 side-by-side on Slide 13. |
| 6 | Archive page mockup w/ filters (Slide 11) | 🔲 Need | List/grid with filter chips or dropdowns above it. |
| 7 | Old logo screenshot for contrast (Slide 8) | 🔲 Need | Crop from the "before" screenshot, 2-minute job. |
| 8 | Timeline graphic (Slide 16) | 🔲 Need | 4-node horizontal milestone bar. |
| 9 | Price table (Slide 15) | 🔲 Need | Layout only. |
| 10 | Tech stack diagram (Slide 14) | 🔲 Optional | Nice-to-have, cut if short on time. |
| 11 | Detail crops from ref.png (Slide 5) | 🔲 Need | Just cropping existing asset, fast. |

**Already done, ready to drop in:** Heirloom homepage mockup (`ref.png`, Slide 5), Heirloom mood image (`hero.png`, Slide 4), current-site screenshot (captured, Slide 3), 4 logo files in `Logo ideas/` (Slides 8–9), Sanity logo, WordPress logo, Vercel logotype, all prompts written in `prompts.md`.

---

## OPEN ITEMS ONLY YOU CAN FILL IN
- [ ] Decide & disclose (not needed in the deck itself, just have an answer ready if asked): what happens to hosting/domain if the working relationship ends — e.g. transfer to a CFHG-owned Vercel team on request.
- [ ] Confirm with the committee whether any payment ever flows through the site (donate button, membership checkout) — doesn't change the Vercel recommendation, but affects whether a "Join/Donate" CTA needs a payment processor discussion later.
- [ ] Retry the Horizon mood image with the v2 prompt, then run the remaining image prompts tonight and sanity-check them against the notes in `prompts.md`.

## LOCKED SO FAR
Title (1), Purpose (2), Heirloom idea+homepage (4–5), Horizon idea+homepage content (6–7, graphics pending), identity concepts 1 & 2 (8–9), maintenance/cross-training incl. 3–4 volunteers trained (10), archive strategy (11), Sanity recommendation (12), WordPress comparison (13), price (15), timeline (16), deliverables checklist (17).

## SLIDE-BY-SLIDE SIGN-OFF PROGRESS
**All 18 slides signed off.** Changes made during the pass: Slide 1 dropped its logo graphic (text-only title), Slide 11 ("static vs. editable") was cut entirely, Slide 16's timeline shifted to a Monday 17 Aug start, Slide 17 dropped its direction/logo-choice bullet, Slide 18 simplified to a plain "Thank you" with no recap list.

**Outline is complete end-to-end.** Remaining work is tonight's actual graphics production — see the checklist above (the Horizon mood image needs a v2 retry, and several other assets are still 🔲 Need).
