# Option A: UI polish pass

Option A stays bold and graphic: gold and navy panels, the slab display face, and large, confident type. This pass made it feel more finished and added a few moments that suit that character.

**Important:** these edits live in this static folder only. Running `npm run build:site` or `npm run build:preview` regenerates `/preview` from the WordPress theme and **will overwrite them**. The original is in `../preview-backup-a` for comparison. Porting the changes into `wordpress/cfhg-option-a` is a separate job; everything below is written to make that straightforward.

## Files

| File | What it is | Size |
|---|---|---|
| `assets/css/ui-pass.css` | All new and overriding styles, loaded after `main.css` | 8.3 KB minified, 2.6 KB gzipped |
| `assets/js/ui-pass.js` | All new behaviour, loaded after `main.js` with `defer` | 4.0 KB minified, 1.5 KB gzipped |
| `assets/images/favicon-32.png`, `apple-touch-icon.png` | Icons made from the palm badge | 2.5 KB, 8.3 KB |
| `assets/images/family-album-caribbean-photographs-{480,760,1100}.webp` | Lighter hero image (the 1100 JPEG was 230 KB; the WebP is 135 KB, and phones now get the 760) | |
| `assets/images/cfhg-logo-horizontal.webp` | Header and footer logo (39 KB, was an 82 KB PNG) | |

Every page also gained, in the head: `theme-color`, the two icons, the `ui-pass.css` link and a one-line script that adds `cfhg-ui` to `<html>` (and removes it again after 3 seconds if `ui-pass.js` never runs, so nothing can stay hidden).

## AI tells removed

| Where | Removed | Replaced with |
|---|---|---|
| Hero | Eyebrow "Tracing Caribbean families in Solihull and online" | Cut. The lead already says Solihull Core Library and Zoom. |
| Hero | Vertical "READ ON" scroll cue with an arrow line | Cut. The hero foot is now one column. |
| Who we are | Eyebrow "WHO WE ARE" | Cut. The statement heading does the work. |
| Next meeting | Eyebrow "NEXT MEETING" and heading "Come along. Your first visit is free." | Heading is now "Next meeting". "Come along. Your first visit is free." kept as the line under it. |
| Research by island | Eyebrow "RESEARCH BY ISLAND" over "Every island kept its records differently" | Heading is now "Research by island". The old heading opens the paragraph: "Every island kept its records differently, so where you start depends on…" |
| Five steps | Eyebrow "NEW TO FAMILY HISTORY?" | Cut. The paragraph under the heading speaks to beginners. |
| Stories | Eyebrow "STORIES AND RESEARCH" over "From the group" | Heading is now "Stories and research". |
| Research help | Eyebrow "RESEARCH HELP" | Cut ("Stuck? Ask us." says it). |
| Membership | Eyebrow "MEMBERSHIP" | Cut ("Join the group for £20 a year" says it). |
| Partners | Eyebrow "WHO WE WORK WITH" over "The partners we rely on", centred | Heading is now "Who we work with", left-aligned like every other section. |
| Story cards (home and Stories) | Topic tag pills ("RESEARCH", "MEMBER STORY", "STORIES") | The topic joins the date line: "14 October 2026 · Research". |
| Story page | "STORIES" and "RESEARCH" pills above the title | Topic link in the date line under the title. |
| 404 | Eyebrow "PAGE NOT FOUND" | Cut. The lead now opens "We can’t find that page." |
| Footer (all pages) | Eyebrow "NEXT MEETING" | A sentence-case column heading, "Next meeting". |
| Meeting facts, next meeting line | Tracked uppercase "WHERE", "ONLINE", "NEXT MEETING" | Sentence case at small size. |

No em dashes, banned phrases or numbered section markers were found or added. The 1 to 5 on the five steps are kept: they are real steps in order.

## Polish

- **Header:** the logo is out of its white pill and larger (`--ui-logo-h`, 48px on phones to 80px on wide screens). Once the page scrolls, the bar takes the paper background with a hairline and the logo eases down to 72%, so it stays legible over navy and photographic sections.
- **Hero:** no longer stretched to full screen height. The photograph's top now lines up with the top of the question at desktop widths, and the paragraph and buttons follow straight on.
- **Type:** balanced wrapping on headings, `text-wrap: pretty` on paragraphs, hanging punctuation where supported, prose capped at 68 characters.
- **Focus:** one 3px ring in the panel's own ink (navy on light, gold on navy); story cards ring the whole card at the same weight.
- **Interaction:** every button presses in to 97% on `:active`. Text links draw a second underline in rather than thickening. Footer arrows nudge on hover. Story photographs ease forward on hover or focus. All timings run 220 to 320ms on one curve, `--ui-ease: cubic-bezier(0.22, 1, 0.36, 1)`.
- **Selection** is gold on navy. **Scrollbar** colours match the page.
- **Performance:** the hero image is WebP with a phone-sized option, and the inner-page images above the fold load straight away.
- **Print:** inner pages print as a plain article. The header, menu, footer navigation, marquee and buttons are hidden, and external links print their address.
- **Page transitions:** the browser's own cross-fade between preview pages (Chromium only; other browsers simply navigate; off for reduced motion).

## Wow moments

1. **The hero question rises word by word, and the album photograph is uncovered from the base of its arch.** The logo settles first, then each word slides up out of its own line, then the paragraph follows. It reads like a poster being set, which suits A's graphic voice.
2. **The "We are volunteers…" statement inks in as you read it.** Words go from pale to full navy in reading order as the block moves up the screen. It is fully inked by the time it sits mid-screen, so no-one is left reading pale text.
3. **A slow marquee of island names in the slab face,** solid and outlined in turn, joined by gold discs. It pauses on hover, a tap pauses it on touch screens, and it only runs while on screen. With reduced motion it becomes a static, wrapping list.
4. **Depth in the photo strip.** Each photograph drifts inside its frame at its own speed as you scroll. This uses CSS scroll-driven animation where supported, with a light JavaScript fallback elsewhere.
5. **Considered reveals, not a blanket fade.** The theme's fade-up on every section is retired. Now the meeting and membership panels grow out of a slightly inset clip, the island and help photographs wipe in from the left, and the step numbers rise into place. Everything else simply sits there.

## Classes and data attributes (for Elementor: Advanced > CSS Classes / Attributes)

| Hook | Put it on | Effect |
|---|---|---|
| `data-cfhg-split` | A heading | Splits into words that rise in on load |
| `cfhg-load-rise` | Any block in the hero | Fades and rises in after the heading |
| `cfhg-load-wipe` | A figure | Uncovered from the bottom on load; the image settles from 114% |
| `data-cfhg-reveal="wipe"` | A figure | Wipes in from the left when scrolled to |
| `data-cfhg-reveal="grow"` | A panel | Grows from a slightly inset rounded clip |
| `data-cfhg-ink` | A heading or paragraph | Words ink in as it is read |
| `data-cfhg-parallax="0.9"` | A frame containing an `img` | The image drifts inside it; the number is the speed, negative reverses |
| `data-cfhg-marquee` | `.cfhg-marquee` wrapping `.cfhg-marquee__track` of `<span>`s | Seamless looping marquee |
| `ui-col-heading` | A footer heading | Sentence-case column heading |
| `ui-meeting-lead` | The line under the meeting heading | Lead size, soft gold on navy |
| `.cfhg-header.is-scrolled` | Added by the script | Paper bar and smaller logo |

States added by the script: `is-split`, `is-in`, `is-inking`, `is-running`, `is-paused`, `is-scrolled`, `is-css-scroll`.

## Checks

- 375, 768, 1024 and 1440: no horizontal overflow and no broken images on any of the five pages.
- Keyboard: 44 tab stops on the homepage, all with a visible ring (story cards ring the whole card).
- Reduced motion: no hiding, no movement, a static island list.
- JavaScript off: everything is visible.
- Scroll-timeline fallback: tested by switching the feature off in Chromium; the JavaScript drift takes over.
- 4x CPU slowdown, scrolling the whole homepage: median frame 17ms, 95th percentile 18ms, no frames over 50ms.
- All 1,284 local links and image references across the three previews resolve as relative paths.

## Lighthouse (homepage, mobile, served with gzip like Vercel)

| | Performance | Accessibility | Best practices | SEO |
|---|---|---|---|---|
| Before | 87 | 100 | 100 | 66 |
| After | 95 | 100 | 100 | 66 |

SEO is held at 66 by the deliberate `noindex`, which keeps the previews out of search. Every other SEO check passes.
