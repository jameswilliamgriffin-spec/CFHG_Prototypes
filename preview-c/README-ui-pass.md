# Option C: UI polish pass

Option C stays dignified and institutional, like a small museum. It keeps the white header, the sea-blue bar, the screenprint illustrations, the index card and the logo-colour rule. This pass makes it quieter and more precise, and the motion is closer to print and paper than to play.

**Important:** these edits live in this static folder only. Running `npm run build:site` or `npm run build:preview-c` regenerates `/preview-c` from the WordPress theme and **will overwrite them**. The original is in `../preview-backup-c`. Porting into `wordpress/caribbean-family-history-c` is a separate job.

## Files

| File | What it is | Size |
|---|---|---|
| `assets/css/ui-pass.css` | New and overriding styles, after `main.css` | 8.4 KB minified, 2.6 KB gzipped |
| `assets/js/ui-pass.js` | New behaviour, after `main.js`, `defer` | 4.8 KB minified, 1.8 KB gzipped |
| `assets/images/favicon-32.png`, `apple-touch-icon.png` | Icons from the palm badge (cropped from the logo) | |
| `assets/images/cfhg-c-logo.webp`, `cfhg-c-logo-light.webp` | Header and footer logos (31 KB and 27 KB; were 59 KB and 48 KB PNGs) | |
| `assets/images/generated/hero-hillside-bay-720.webp` | A phone-sized hero, so phones stop downloading the 900px version | |

Every page gained `theme-color`, proper icons (the 542px logo was the favicon before), the `ui-pass.css` link and the `cfhg-ui` head script, which removes itself after 3 seconds if `ui-pass.js` never runs.

## AI tells removed

| Where | Removed | Replaced with |
|---|---|---|
| Hero | Red-dash eyebrow "CARIBBEAN FAMILY HISTORY GROUP" | Cut. The logo sits directly above it. |
| Who we are | Eyebrow-styled `h2` "WHO WE ARE" | Kept for screen readers and the "About us" menu link, but hidden. The statement does the work on screen. |
| Research by island | Eyebrow over "Every island keeps its records differently." | Heading is now "Research by island". The paragraph beside it already says records vary from island to island. |
| Island cards (home, island pages) | Black label bars "SUB-GROUP", "RESEARCH GUIDE", "MEMBERS’ STORIES" (35 in all), plus the label above each island title | Cut. Each summary already says it, for example "our largest sub-group to help you read them". |
| Ways in | Eyebrow "THREE WAYS IN", the "01 / 02 / 03" markers, and the eyebrows "MEET US", "RESEARCH", "SHARE" | Cut. The heading and each card’s own heading say it. |
| What’s on | Eyebrow over "Coming up" | Heading is now "What’s on", matching the menu. |
| Programme | Green "MEETING", "AGM", "SOCIAL" labels | Cut. Each title already says what the event is. |
| Support, Stories | Eyebrows "SUPPORT THE GROUP", "FROM THE GROUP" | Cut. |
| Islands and Stories pages | Eyebrows above the page titles | Cut. |
| Story pages | "PREVIOUS", "NEXT" eyebrows in the post navigation | Plain sentence-case labels. |
| 404 | Eyebrow "PAGE NOT FOUND" | Cut. The lead opens "We can’t find that page." |
| Index card | Tracked uppercase heading and labels, gold "TODAY" lozenge | Serif "Next meeting", sentence-case labels, and "Today" as bold red words. |
| Post cards | Tracked uppercase date and category with a rule | "2 October 2026 · Group history", sentence case. |
| Footer, preview line | Gold tracked uppercase headings | Serif sentence-case headings. |
| Text links | "ABOUT THE GROUP →", "RESEARCHING JAMAICA →" and similar in tracked capitals | Sentence case with a rule that thickens. Real buttons keep their capitals. |

No em dashes or banned phrases were found or added. The centred "Who we are" statement is the one centred block on the page and is kept on purpose.

## Polish

- **Type:** balanced headings, pretty paragraphs, hanging punctuation.
- **Focus:** one 3px ring, sea blue on light and gold on the blue bar, the support band, the dark way-in panel and the footer.
- **Interaction:** buttons press in, button arrows nudge, and text links thicken their rule. One curve, the theme's own `--cfhg-c-ease`, at 200 to 300ms.
- **Text-size feedback:** pressing A-, A or A+ briefly shows "Text size 113%" under the buttons. The theme's live region still announces the size to screen readers.
- **Selection** is white on sea blue. **Scrollbar** colours match the page.
- **Print:** inner pages print as a clean article at 100% text, whatever size the visitor chose.
- **Page transitions:** the browser's own cross-fade between pages (Chromium only; off for reduced motion).

## Wow moments

1. **The hero is printed.** The heading is revealed left to right through a soft mask, like ink going down. The illustration is then uncovered from the base of its arch upwards. Last, the next-meeting index card is laid down on top of it, settling from a slight angle. This is different from A's word stagger and B's iris, and it suits a museum print.
2. **The logo-colour rule draws itself,** one colour at a time, red through to navy, wherever it appears on any page.
3. **The island scroller has focus, depth and progress.** The card nearest the middle steps forward onto its tinted offset shadow. The illustrations drift inside their frames as the row moves. A progress rule sits beside the arrows. The arrow buttons and keyboard control are unchanged.
4. **The What’s on programme is a timeline.** A red rule fills down between the dates and the events as you read, and each event's marker fills when the rule reaches it.
5. **The sea-blue bar stays.** On desktop, once the header above has scrolled away, the navigation bar holds at the top and a small white logo slides in at its left. The logo only appears where it clears the menu, which matters at the largest text size.
6. **The way-in photographs open like a shutter.** This is the only scroll reveal left on the page. The theme's fade-up on every section is retired.

## Classes and data attributes

| Hook | Put it on | Effect |
|---|---|---|
| `cfhg-load-ink` | A heading | Printed left to right through a mask on load |
| `cfhg-load-print` | A figure | Uncovered from the base upwards; the image settles |
| `cfhg-load-card` | A card | Laid down from a slight angle |
| `cfhg-load-fade` + `style="--d:n"` | Hero blocks | Fade and rise, staggered |
| `data-cfhg-draw` | `.cfhg-c-ring` | Colours draw in one by one |
| `data-cfhg-focus` | A scroller track | Centre card steps forward, image parallax, progress rule |
| `data-cfhg-timeline` | `.cfhg-c-programme` | The rule fills down the list as it is read |
| `data-cfhg-stick` | `.cfhg-c-nav` | The bar stays at the top; small logo slides in |
| `.cfhg-c-way__media` | (automatic) | Shutter reveal |
| `ui-postnav-label` | Previous / next labels | Plain label |
| `screen-reader-text` | The "Who we are" heading | Hidden on screen, still read out |

States added by the script: `is-in`, `is-focus`, `is-past`, `is-stuck`, `has-logo-room`.

## Checks

- All 15 pages at 375, 768, 1024 and 1440, and again at 150% text: no horizontal overflow and no broken images.
- At 150% text the sticky logo stays hidden where it would overlap the menu; this bug was found and fixed during the check.
- Keyboard: 45 tab stops on the homepage, all visible. Island cards ring the whole card, as the theme intends.
- Reduced motion: no hiding and no movement; the timeline shows filled.
- JavaScript off: everything is visible.
- 4x CPU slowdown: median frame 17ms, 95th percentile 18ms, no frames over 50ms.

## Lighthouse (homepage, mobile, served with gzip like Vercel)

| | Performance | Accessibility | Best practices | SEO |
|---|---|---|---|---|
| Before | 79 | 100 | 100 | 66 |
| After | 90 | 100 | 100 | 66 |

SEO is held at 66 by the deliberate `noindex`.
