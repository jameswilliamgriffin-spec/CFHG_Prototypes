# AI Image Prompts

## Option B homepage mockup — "Community & Colour" (contrast to ref.png)

Purpose: a second design-language example for the pitch deck, shown alongside `Presentation/ref.png` (Option A — vintage/archival). This one should look, feel, and read as the opposite: bold, contemporary, and community-forward rather than sepia and scrapbook-like. Above-the-fold only (nav + hero), not a full page.

### Primary prompt

```
Create a high-fidelity website homepage mockup (UI/UX design concept, not a photograph), showing only the above-the-fold section — navigation bar plus hero — for "Caribbean Family History Group," a nonprofit genealogy and family history organisation.

Style: modern, clean, bold, and contemporary. Card-based layout with generous white space, soft rounded corners, and a confident geometric sans-serif typeface throughout. This should feel like a warm, energetic community platform — NOT vintage, NOT sepia-toned, NOT a scrapbook or collage of old documents. Avoid aged paper textures, distressed edges, postmarks, or archival ephemera entirely.

Color palette: bold Caribbean-flag-inspired colors used as solid blocks and accents — sun gold (#F5B841), sea teal (#1B998B), palm green (#2E8B57), hibiscus red (#E63946), and deep navy (#0B1F3A) for text and anchoring elements. Bright and saturated, not muted or pastel.

Navigation bar: logo mark on the left (a simple palm tree + sun + wave icon inside a circular ring split into the palette colors, with wordmark "CARIBBEAN FAMILY HISTORY GROUP" in bold sans-serif beside it), horizontal nav links (Discover, Start Your Research, Explore by Country, DNA, Stories & History, Community, News & Events), a search icon, and a solid gold "Join CFHG" button on the right.

Hero section: large bold headline text (short, 4-6 words, e.g. "Your Story. Our Community."), a short one-sentence subheading below it, and two call-to-action buttons ("Start your research" solid, "Explore the community" outlined). To the right or as a background element, a vibrant, warm, contemporary photo collage of real Caribbean community life — a multigenerational family looking at photos together, people at a community dinner/dance event, a guest speaker presenting to an audience — shot in natural light, full color, joyful and current, not black-and-white or archival.

Overall mood: inviting, energetic, proud, forward-facing — a living community, not a museum archive.

Format: web design mockup, browser-width aspect ratio (roughly 16:10), flat 2D UI design presentation (like a Figma or Webflow concept render), no browser chrome/URL bar, no watermark.
```

### Notes for whoever runs this prompt
- Image models are unreliable at rendering small/long text accurately — expect to redo nav labels, the headline, and button text by hand in Figma afterward. Keep the prompt's requested text short for the best chance of a legible first pass.
- If the first result still drifts toward vintage/sepia, add explicitly: "no film grain, no vintage color grading, fully saturated modern photography."
- If it comes back too generic/corporate (loses the Caribbean identity), add: "should feel distinctly Caribbean and Black British, not generic stock-photo diversity."

---

## CMS backend comparison — Sanity vs WordPress (Slides 10–11)

Purpose: a matched pair of images contrasting the two CMS editing experiences — deliberately calm/simple vs. deliberately cluttered/overwhelming. Same framing and aspect ratio for both so they read as a fair, direct comparison side by side.

### Prompt 1 — Sanity backend (calm, simple)

```
Create a UI mockup (design concept, not a real screenshot) of a clean, minimal content-management admin interface, browser window at widescreen aspect ratio (16:10).

Layout: a narrow left sidebar containing exactly 8 clearly labeled menu items, each with a simple line icon: News, Events, AGM, Research Dates, Sub-Group Dates, Gallery, Exhibitions, Homepage Highlights. "News" is selected/highlighted. The main content area shows a simple list of 4 news article rows, each with a small thumbnail image, a title, and a date — plenty of white space between rows, no clutter.

Style: modern, calm, uncluttered SaaS-style admin panel. Generous white space, soft rounded corners, a restrained neutral palette (white/light grey background, one accent color for the highlighted menu item and buttons — deep navy or teal). Clean sans-serif typography. A single "+ New" button top right.

Mood: calm, simple, approachable — designed so a complete beginner would feel comfortable within seconds. No dense toolbars, no overlapping panels, no more than the 8 sidebar items should be visible.

Format: flat 2D UI design presentation, no browser chrome/URL bar, no watermark.
```

### Prompt 2 — WordPress backend (busy, overwhelming)

```
Create a UI mockup (design concept, generic/illustrative — not a reproduction of any specific real product's exact branding or logo) of a dense, old-fashioned website admin dashboard, browser window at the same widescreen aspect ratio (16:10) as a companion comparison image.

Layout: a tall left sidebar packed with many small menu items and sub-menus (roughly 15-20 entries — dashboard, posts, media, pages, comments, appearance, plugins with a red notification badge, users, tools, settings, and several extra plugin-style menu entries with unfamiliar icons). The main content area is a cluttered dashboard home screen with 5-6 small overlapping widget boxes of dense text and tiny charts, a "screen options" bar, and a long dropdown-heavy toolbar at the top.

Style: dated, generic admin-panel aesthetic — grey and white, small default system font, cramped spacing, low visual hierarchy, everything roughly the same visual weight so nothing stands out. Slightly overwhelming and busy on purpose.

Mood: cluttered, intimidating for a first-time non-technical user — many small unfamiliar options with no obvious starting point, in deliberate contrast to a companion image of a clean, simple 8-item admin panel.

Format: flat 2D UI design presentation, no browser chrome/URL bar, no watermark.
```

### Notes
- Keep both at the same aspect ratio/crop so they can sit side-by-side on Slides 12–13 as a direct visual contrast.
- Prompt 2 is deliberately generic/illustrative rather than a pixel-accurate WordPress clone — avoids reproducing real trademarked UI/branding while still making the point.
- Same text-rendering caveat as the Horizon prompt above: expect to clean up any garbled labels by hand in Figma.

---

## Horizon — mood/concept image (Slide 6, the "idea" page)

Purpose: the Horizon equivalent of `hero.png`. Heirloom's "idea" page pairs an overview with `hero.png` (a vintage flat-lay of family photos, ship manifests, and maps). Horizon needs its own flat-lay moodboard image on the same page structure — but as the deliberate opposite: current, colourful, no vintage elements at all. This is a mood/concept image, NOT the full homepage UI (that's the separate prompt above for Slide 7).

### Prompt

```
Create a vibrant, contemporary flat-lay moodboard photograph (not a screenshot, not a UI mockup) representing a modern, colourful Caribbean family history community — the deliberate opposite of a vintage/archival aesthetic.

Arrange the following on a clean, bright, plain surface (white, pale wood, or a solid bold colour background — NOT aged paper, NOT parchment): 2-3 full-colour candid photographs of multigenerational Caribbean families laughing and embracing at a community event; a smartphone screen showing a bright, modern family-tree app interface; a vivid Caribbean-print textile swatch or fabric; a fresh green palm leaf or two; a cup of traditional Caribbean tea or coffee. Shot from directly above (flat-lay photography), bright natural daylight, soft natural shadows.

Colour palette: bold and saturated — sun gold, sea teal, palm green, hibiscus red — as accents against the clean background. No sepia, no aged paper, no distressing, no film grain, no vintage colour grading, no ship manifests or old documents.

Mood: warm, joyful, current, community-forward — this should feel like today, not archival memorabilia. The photographic equivalent of the hero.png moodboard, but representing where the community is going rather than where it came from.

Format: high-resolution flat-lay photography style, landscape orientation, no watermark, no text overlay.
```

### Notes
- This is a photographic mood image, not a UI mockup — different job from the Slide 7 homepage prompt above. Don't merge them.
- If it drifts toward looking like stock photography with no Caribbean specificity, add: "should feel distinctly Caribbean and Black British, not generic stock-photo diversity" (same fix as the Slide 7 prompt).

### v1 result — rejected, use v2 below
First attempt (prompt above) came back reading as generic tropical/holiday stock photography rather than a Caribbean heritage organisation: the floral fabric swatch pushed it toward "beach brochure," the brand palette got lost in naturalistic greens/florals instead of showing up as deliberate colour blocks, the phone's "My Family Tree" app UI was generic filler with garbled text, and the photos felt posed rather than candid.

### v2 prompt — use this one

```
Create a vibrant, contemporary flat-lay moodboard photograph (not a screenshot, not a UI mockup) representing a modern UK-based Caribbean family history community — the deliberate opposite of a vintage/archival aesthetic, and NOT a tropical holiday/vacation mood.

Arrange the following on a clean, bright, plain white or pale wood surface: 2-3 candid (not posed) full-colour photographs of multigenerational Black British Caribbean families at a community event — laughing mid-conversation, looking at printed photos together, not smiling straight at camera; a single clean printed modern family-tree chart with simple connecting lines (not a phone screen, not an app); a community event lanyard/name badge; a solid-colour object (e.g. a plain teal or gold mug, or a plain red folder) to carry brand colour without relying on floral print. A fresh green palm leaf, kept minimal, in one corner only.

Colour palette: bold and saturated, used deliberately as solid blocks on props (mug, folder, badge lanyard) — sun gold (#F5B841), sea teal (#1B998B), palm green (#2E8B57), hibiscus red (#E63946). Avoid busy floral patterns — the colour should come from clean solid objects, not printed fabric.

Mood: warm, candid, current, rooted in a specific UK Caribbean community (not generic multicultural stock photography, not a beach holiday). Shot from directly above, bright natural daylight, soft natural shadows.

Format: high-resolution flat-lay photography style, landscape orientation, no watermark, no text overlay.
```
