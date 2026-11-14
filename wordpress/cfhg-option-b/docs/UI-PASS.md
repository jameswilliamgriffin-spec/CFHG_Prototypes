# Option B: UI polish pass

Option B stays warm and personal: the cream ground, the logo-ring colours, the snapshot photographs and the highlighter yellow. The polish pass makes it feel more finished, and it moves in a softer, rounder way than Option A.

It was first made by hand in the static preview, which the client approved. It is now part of the theme (version 1.1.0). The preview is rebuilt from the theme (`npm run build:site -- b`), so the two can't drift apart.

## Where it lives

| File | What it does |
|---|---|
| `assets/css/ui-pass.css` | New and overriding styles, loaded after `main.css`. Section 8 holds the Elementor equivalents. |
| `assets/js/ui-pass.js` | New behaviour, loaded after `main.js`, deferred. |
| `inc/enqueue.php` | Loads both files. Prints the `cfhg-ui` head script and the `theme-color` meta. |
| `inc/setup.php` | Icons cut from the palm badge (used until a Site Icon is set), and the page titles. |
| `inc/components.php` | Story slides, the count-up rule, and the animation hooks on the hero proof and meeting line. |
| `assets/images/` | `favicon-32.png`, `apple-touch-icon.png`, `cfhg-logo-horizontal.webp`, and the hero photograph as WebP at 640, 960 and 1400 wide (it was a 178 KB JPEG). |

The head script adds `cfhg-ui` to `<html>`. It takes the class off again after three seconds if `ui-pass.js` never runs, so a blocked script can't leave anything hidden.

## Labels removed

The small labels above headings (the "eyebrows") were cut. Where a label said something the heading didn't, its words became the heading.

| Where | Was | Now |
|---|---|---|
| Opening panel | White pill "Volunteer family history group" | Cut. The introduction opens "We’re a volunteer group". |
| Next meeting | Label "Next meeting" over the date, heading "Your first visit is free" | "Next meeting" is the heading. "Your first visit is free" is the highlighted line under the date. |
| Meeting photograph | White pill "Solihull Core Library" | Plain caption on a soft scrim: "Solihull Core Library, where we meet". |
| Start here | Label "New to family history?" | Cut. |
| Research by island | Label over "Where was your family from?" | Heading is "Research by island". |
| Stories | Label "Stories and research" over "From the group" | Heading is "Stories and research". |
| Research help, Membership | Labels | Cut. |
| Partners | Label over "The partners we rely on" | Heading is "Who we work with". |
| Story slides | Topic chips and "1/3" counters | The topic joins the date line ("14 October 2026 · Research"). A slim progress bar sits beside the carousel arrows. |
| Story page | Category chips above the title | The topic is a link in the date line. |
| 404 | Label "Page not found" | Cut. The introduction opens "We can’t find that page." |
| Footer | Green-bar labels "Explore", "Next meeting", "Contact" | Sentence-case column headings (`h2.ui-col-heading`). |
| Meeting facts | Tracked uppercase labels | Sentence case. |

The Customizer no longer has the label settings for those sections. "Meetings and prices > Heading" (`meeting_kicker`) is still there: it is the meeting card's heading and the "Next meeting" words in the opening panel, menu and footer.

The homepage title in the browser tab is set at **Opening panel > Homepage title in the browser tab**. Other pages read "Page | Caribbean Family History Group".

Kept on purpose:

- **The group in numbers.** The figures are real and specific to the group.
- **The numbered five steps.** They are a real sequence.
- **The hero's decorative circles and portrait row.**

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
- **Performance:** WebP hero and logo; the featured image at the top of pages and stories loads straight away.
- **Print:** inner pages print as a plain article.
- **Page transitions:** the browser's own cross-fade between pages (Chromium only; off for reduced motion).

## Wow moments

1. **The hero circle opens like an iris.** The family photograph widens out from a small circle as it settles. The white, coral, blue and gold circles then pop in one after another on a gentle spring. The highlighter sweeps under "Trace", and the small portraits slide in one by one.
2. **The group's numbers count up.** As the scattered notes land, "40+", "9", "3" and "4" count up from nothing. They stop at the real figure, which is already in the HTML, so nothing changes without JavaScript. The rule is `cfhg_counts_up()` in `inc/components.php`: whole numbers above zero, with an optional prefix or suffix, but not years ("2011") and not "£0". It applies to whatever figures are typed into the Customizer.
3. **Highlighter strokes draw in when you reach them.** "Your first visit is free" gets its yellow stroke drawn left to right as the meeting card comes into view.
4. **"COME ALONG" opens out as it scrolls past.** The giant words widen and drift sideways across the membership band, and the photograph behind moves more slowly. This uses CSS scroll-driven animation where supported, with a JavaScript fallback elsewhere.
5. **Considered reveals.** The theme's fade-up on every section is retired. The scattered notes and tilted snapshots keep their landing, because that is B's character. Everything else simply sits there.

## Classes and data attributes

| Hook | Put it on | Effect |
|---|---|---|
| `cfhg-load-iris` | A round figure, or an Elementor Image widget | Opens from a small circle on load |
| `cfhg-load-pop` + `style="--i:n"` | Decorative shapes | Pop in on a spring, staggered by `--i` |
| `cfhg-load-fade` + `style="--d:n"` | Opening panel blocks | Fade and rise, staggered by `--d` |
| `cfhg-load-d1` to `d3`, `cfhg-load-i1` to `i3` | With the two above | The same stagger as a class, for Elementor (which can't add inline styles) |
| `data-cfhg-mark` | An inline span with the highlighter background | The stroke draws in when seen |
| `data-cfhg-count` | A number | Counts up once, keeping any prefix or suffix |
| `data-cfhg-track` | Giant display words | Letter-spacing and position follow scroll |
| `data-cfhg-parallax` | A frame containing an `img` | The image drifts more slowly than the page |
| `data-cfhg-carousel` (existing) | The carousel | Gains a progress bar (`.ui-progress`) |
| `ui-col-heading` | Footer headings | Sentence-case column heading |
| `ui-meeting-title` | The meeting card heading | Spacing above the big date |
| `ui-caption` | A `figcaption` over a photograph | Plain caption on a scrim |

States added by the script: `is-in`, `is-css-scroll`.

Elementor's free version can add classes to a widget but not attributes, so it has its own hooks. See "Polish effects" in `ELEMENTOR.md`.

## Checks

The hand-made preview was checked before the client saw it: 375, 768, 1024 and 1440 wide with no sideways scrolling, a visible focus ring on every tab stop, reduced motion, JavaScript off, and the scroll-timeline fallback.

After the port (14 November 2026, Local, WordPress 7.1.4, Elementor 4.4.0):

- The preview rebuilt from the theme matches the hand-made one, apart from the build date and the hero image's stated size (now the WebP's real 1400 × 788).
- The theme homepage and the Elementor-built homepage both load the polish with no PHP errors and no console errors. The iris, highlighter, counters, carousel progress bar, and both scroll effects are running.

## Not done

- **Text-size control.** The brief mentions a text-size control for B, but B has never had one; only C does. Adding one is a feature change rather than polish, so it is left for a decision.
