# Option B: UI polish pass

Option B stays warm and personal: the cream ground, the logo-ring colours, the snapshot photographs and the highlighter yellow. This pass makes it feel more finished, and it moves in a softer, rounder way than Option A.

**Important:** these edits live in this static folder only. Running `npm run build:site` or `npm run build:preview:b` regenerates `/preview-b` from the WordPress theme and **will overwrite them**. The original is in `../preview-backup-b`. Porting into `wordpress/cfhg-option-b` is a separate job.

## Files

| File | What it is | Size |
|---|---|---|
| `assets/css/ui-pass.css` | New and overriding styles, after `main.css` | 7.5 KB minified, 2.3 KB gzipped |
| `assets/js/ui-pass.js` | New behaviour, after `main.js`, `defer` | 2.7 KB minified, 1.1 KB gzipped |
| `assets/images/favicon-32.png`, `apple-touch-icon.png` | Icons from the palm badge | 2.5 KB, 8.3 KB |
| `assets/images/three-generations-with-an-album-on-the-sofa-{640,960,1400}.webp` | Lighter hero photograph (was a 178 KB JPEG at 1600) | |
| `assets/images/cfhg-logo-horizontal.webp` | Header and footer logo (39 KB, was an 82 KB PNG) | |

Every page gained `theme-color`, the icons, the `ui-pass.css` link and the `cfhg-ui` head script, which removes itself after 3 seconds if `ui-pass.js` never runs.

## AI tells removed

| Where | Removed | Replaced with |
|---|---|---|
| Hero | White pill eyebrow "VOLUNTEER FAMILY HISTORY GROUP" with a coral dot | Cut. The lead opens "We’re a volunteer group". |
| Next meeting | Green-bar eyebrow "NEXT MEETING" | It became the section heading. "Your first visit is free" stays as the highlighted line under the date. |
| Next meeting photo | White pill chip "Solihull Core Library" | A plain caption on a soft scrim: "Solihull Core Library, where we meet". |
| Start here | Eyebrow "NEW TO FAMILY HISTORY?" | Cut. The lead beside the heading speaks to beginners. |
| Research by island | Eyebrow over "Where was your family from?" | Heading is now "Research by island". |
| Stories | Eyebrow "STORIES AND RESEARCH" over "From the group" | Heading is now "Stories and research". |
| Research help, Membership | Eyebrows | Cut. The headings say it. |
| Partners | Eyebrow over "The partners we rely on" | Heading is now "Who we work with". |
| Story slides (home and Stories) | Topic tag chips and the "1/3", "1/5" counters | Topic joins the date line ("14 October 2026 · Research"); a slim progress bar sits beside the carousel arrows instead of the counters. |
| Story page | Tag chips above the title; tracked uppercase date line | Topic in the date line, sentence case. |
| 404 | Eyebrow "PAGE NOT FOUND" | Cut. The lead opens "We can’t find that page." |
| Footer (all pages) | Green-bar eyebrows "EXPLORE", "NEXT MEETING", "CONTACT" | Sentence-case column headings in the display face. |
| Meeting facts, next meeting line | Tracked uppercase labels | Sentence case. |

Kept on purpose:

- **The group in numbers.** The figures are real and specific to the group.
- **The numbered five steps.** They are a real sequence.
- **The hero's decorative circles and portrait row.** You asked for these in the header rework.

No em dashes or banned phrases were found or added.

## Polish

- **Type:** balanced headings, pretty paragraphs, hanging punctuation, prose capped at 68 characters.
- **Focus:** one 3px ink ring, highlighter yellow on the dark photographic bands.
- **Interaction:**
  - Buttons press in to 96%, and the arrow in each icon square nudges forward.
  - Text links get a highlighter stroke that slides in under the words.
  - Footer arrows nudge.
  - Story photographs ease forward on hover or focus.
  - The carousel has a progress bar.
  - One shared curve, `--ui-ease`, at 220 to 320ms, with a spring curve for the hero circles only.
- **Header:** the scrolled bar gains a soft edge as well as the hairline.
- **Selection** is highlighter yellow. **Scrollbar** colours match the page.
- **Performance:** WebP hero and logo; the inner-page images above the fold load straight away.
- **Print:** inner pages print as a plain article.
- **Page transitions:** the browser's own cross-fade between preview pages (Chromium only; off for reduced motion).

## Wow moments

1. **The hero circle opens like an iris.** The family photograph widens out from a small circle as it settles. The white, coral, blue and gold circles then pop in one after another on a gentle spring. The highlighter sweeps under "Trace", and the small portraits slide in one by one. It is warm and playful rather than graphic, which is how B differs from A.
2. **The group's numbers count up.** As the scattered notes land, "40+", "9", "3" and "4" count up from nothing. They stop at the real figure, which is already in the HTML, so nothing changes without JavaScript. "2011" and "£0" are left alone, because counting up to a year or to zero would be silly.
3. **Highlighter strokes draw in when you reach them.** "Your first visit is free" gets its yellow stroke drawn left to right as the meeting card comes into view.
4. **"COME ALONG" opens out as it scrolls past.** The giant words widen and drift sideways across the membership band, and the photograph behind moves more slowly. This uses CSS scroll-driven animation where supported, with a JavaScript fallback elsewhere.
5. **Considered reveals.** The theme's fade-up on every section is retired. The scattered notes and tilted snapshots keep their landing, because that is B's character. Everything else simply sits there.

## Classes and data attributes

| Hook | Put it on | Effect |
|---|---|---|
| `cfhg-load-iris` | A round figure | Opens from a small circle on load |
| `cfhg-load-pop` + `style="--i:n"` | Decorative shapes | Pop in on a spring, staggered by `--i` |
| `cfhg-load-fade` + `style="--d:n"` | Hero blocks | Fade and rise, staggered by `--d` |
| `data-cfhg-mark` | An inline span with the highlighter background | The stroke draws in when seen |
| `data-cfhg-count` | A number | Counts up once, keeping any prefix or suffix |
| `data-cfhg-track` | Giant display words | Letter-spacing and position follow scroll |
| `data-cfhg-parallax` | A frame containing an `img` | The image drifts more slowly than the page |
| `data-cfhg-carousel` (existing) | The carousel | Gains a progress bar (`.ui-progress`) |
| `ui-col-heading` | Footer headings | Sentence-case column heading |
| `ui-meeting-title` | The meeting card heading | Spacing above the big date |
| `ui-caption` | A `figcaption` over a photograph | Plain caption on a scrim |

States added by the script: `is-in`, `is-css-scroll`.

## Checks

- 375, 768, 1024 and 1440: no horizontal overflow and no broken images on any of the five pages. The only elements past the edge are the decorative hibiscus, palm and snapshot, which are meant to bleed and are clipped.
- Keyboard: 45 tab stops on the homepage, every one with a visible ring.
- Reduced motion: no hiding, no movement, and the counters show their final figures.
- JavaScript off: everything is visible.
- Scroll-timeline fallback: tested with the feature switched off; the JavaScript fallback takes over.
- 4x CPU slowdown: median frame 17ms, 95th percentile 18ms, no frames over 50ms.

## Not done

- **Text-size control.** The brief mentions a text-size control for B, but B has never had one; only C does. Adding one is a feature change rather than polish, so I left it for you to decide.

## Lighthouse (homepage, mobile, served with gzip like Vercel)

| | Performance | Accessibility | Best practices | SEO |
|---|---|---|---|---|
| Before | 88 | 100 | 100 | 66 |
| After | 91 | 100 | 100 | 66 |

SEO is held at 66 by the deliberate `noindex`.
