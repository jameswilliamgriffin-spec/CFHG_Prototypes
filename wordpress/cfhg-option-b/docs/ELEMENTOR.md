# Building and editing CFHG Option B in Elementor

## How the pieces fit

- **Design tokens** are in Elementor's Site Settings (Global Colours and Global Fonts)
  and in `assets/css/tokens.css`, which reads Elementor's globals back. Changing
  **CFHG B Coral** in Elementor changes the coral everywhere.
- **Layout** (columns, widths, padding, backgrounds, radius) is native Elementor.
- **Type, buttons and image shapes** come from theme CSS classes added in each
  widget's Advanced > CSS Classes.
- **Designed components** come in through shortcodes in the free Shortcode widget:
  - the next meeting date (always correct);
  - prices;
  - snapshot frames;
  - the story carousel;
  - the hibiscus and palm frond shapes.
- **Lists** (numbers, steps, islands, partners) are plain HTML in Text Editor
  widgets, so they're edited directly in Elementor.

## Setting up

1. Appearance > CFHG Setup > **Apply CFHG colours and fonts to Elementor**.
2. Appearance > CFHG Setup > **Create the Elementor homepage**.
3. Optional, with Elementor Pro: a Theme Builder header or footer replaces the theme's.

## Global Colours

| Name | Hex | CSS variable | Used for |
|---|---|---|---|
| Primary (headings) | #10213B | | Default for new Heading widgets |
| Secondary (green) | #137A4D | | |
| Text | #10213B | | Default for new Text Editor widgets |
| Accent (buttons) | #137A4D | | Default for new Button widgets (white text, 5.4:1) |
| CFHG B Cream | #F6F1EA | --cfhg-cream | Page background |
| CFHG B Sand | #ECE4D9 | --cfhg-sand | Research help card, first story slide |
| CFHG B White | #FFFFFF | --cfhg-white | Cards, menu panel |
| CFHG B Ink | #10213B | --cfhg-ink | Text |
| CFHG B Ink soft | #4B5567 | --cfhg-ink-soft | Labels, secondary text |
| CFHG B Coral | #F26B57 | --cfhg-coral | Section colour 1 |
| CFHG B Gold | #F6C343 | --cfhg-gold | Section colour, island window |
| CFHG B Highlighter | #FFE873 | --cfhg-highlight | Buttons, highlights |
| CFHG B Green | #2FA36B | --cfhg-green | Section colour, label bars |
| CFHG B Green deep | #137A4D | --cfhg-green-deep | Green buttons, the day of the next meeting |
| CFHG B Sea | #45BCD0 | --cfhg-sea | Section colour, opening panel circle |
| CFHG B Blue | #5B8DEF | --cfhg-blue | Section colour |
| CFHG B Line | #D9CFC1 | --cfhg-line | Hairline rules |

## Global Fonts

| Slot | Name | Family | Weight | Desktop / tablet / mobile |
|---|---|---|---|---|
| Primary | Heading | Jost, uppercase | 400 | 64 / 48 / 34px |
| Secondary | Display | Jost, uppercase | 400 | 104 / 76 / 46px |
| Text | Body | Outfit | 300 | 17px |
| Accent | Label | Jost, uppercase | 500 | 15 / 15 / 14px |
| Custom | Lead | Outfit | 300 | 22 / 20 / 19px |
| Custom | Numbers | Jost | 400 | 96 / 80 / 64px |

## CSS classes

| Class | On | Effect |
|---|---|---|
| `cfhg-display`, `cfhg-h1`, `cfhg-h2`, `cfhg-h3` | Heading | Uppercase Jost at the fluid sizes |
| `cfhg-label` | Heading (set to P) | Small uppercase label with a green bar. The homepage no longer uses these (see `UI-PASS.md`), but the class still works. |
| `cfhg-lead`, `cfhg-body` | Text Editor | Lead and body text |
| `cfhg-el-btn cfhg-el-btn--mark` | Button | Highlighter button with an icon square (main action) |
| `cfhg-el-btn cfhg-el-btn--green` | Button | Deep green with white text |
| `cfhg-el-btn cfhg-el-btn--plain` | Button | White, for coloured backgrounds |
| `cfhg-el-btn cfhg-el-btn--text` | Button | Underlined link |
| `cfhg-el-btn--big` | Button, with the above | Larger size for primary actions |
| `cfhg-el-img cfhg-el-img--oval` | Image | Oval window (3:4) |
| `cfhg-el-img cfhg-el-img--rounded` | Image | Rounded corners; add `cfhg-el-img--fill` to fill its column |
| `cfhg-el-img cfhg-el-img--disc` | Image | Photograph in a sea-blue circle; runs off the right edge on desktop when its column has class `cfhg-el-hero-art` |
| `cfhg-el-hero-title` | Heading | The opening heading; one word in `<em>` is set italic on a highlighter band |
| `cfhg-el-kicker` | Text Editor holding `<p class="cfhg-hero__kicker">` | White pill label with a coral dot (no longer on the homepage) |
| `cfhg-el-chip` | Heading, positioned absolute | White pill label on a photograph (replaced on the homepage by `cfhg-el-caption`) |
| `cfhg-el-scrim` | Container holding a photograph | Soft dark fade along the bottom, for a caption |
| `cfhg-el-caption` | Heading (set to P), positioned absolute, inside `cfhg-el-scrim` | Plain white caption over the photograph |
| `cfhg-el-big` | Heading | Giant light word across a photograph band |
| `cfhg-el-card-title` | Heading | Card title with a hairline beneath |
| `cfhg-el-shape--gold`, `--green`, `--coral` | Shortcode widget with `[cfhg_shape]` | Colours the shape |
| `cfhg-intro__snap--left`, `--right` | Shortcode widget with `[cfhg_snapshot]` | Tilts the snapshot |

Text Editor lists styled by the theme (paste the HTML in the Text tab):

- `<ul class="cfhg-notes">` with `<li class="cfhg-note"><span class="cfhg-note__num">40+</span> <span class="cfhg-note__text">…</span></li>`
- `<ol class="cfhg-steps">` with `<li><h3>Title</h3><p>Text</p></li>`
- `<ul class="cfhg-places">` with `<li><a href="…"><strong>Jamaica</strong> <span>note</span></a></li>`
- `<ul class="cfhg-partners__list">` with `<li><h3><a href="…">Name</a></h3><p>Text</p></li>`

Colours for notes, steps, islands and partners are applied in order automatically.

## Polish effects

The motion and finishing touches from the polish pass (`UI-PASS.md`) work in Elementor through classes, added in **Advanced > CSS Classes**. Elementor's free version can't add the data attributes the theme templates use, so these classes stand in for them.

| Effect | How, in Elementor |
|---|---|
| Fade and rise on load | `cfhg-load-fade`, plus `cfhg-load-d1`, `d2` or `d3` to stagger it |
| Circle opens like an iris on load | `cfhg-load-iris` on the Image widget |
| Shape pops in on load | `cfhg-load-pop`, plus `cfhg-load-i1`, `i2` or `i3` |
| Big words open out as they scroll past | `cfhg-ui-track` on the Heading |
| Background photograph drifts more slowly | `cfhg-ui-parallax` on the container with the background image. It runs where the browser supports CSS scroll-driven animation (Chrome, Edge, Safari 26), and otherwise stays still. |
| Highlighter stroke draws in when seen | In the Heading's text: `<span class="cfhg-highlight" data-cfhg-mark>Words</span>` |
| Numbers count up | Automatic: the `cfhg-notes` list from `[cfhg_numbers]` or the Customizer copy already carries `data-cfhg-count`. If you type the list by hand, add `data-cfhg-count` to each `cfhg-note__num` span. |
| Carousel progress bar | Automatic in `[cfhg_stories]` |

Button presses, the highlighter stroke under text links, focus rings and type polish apply to the `cfhg-el-btn` buttons and theme classes with nothing to add.

## Card icons

The number notes and step tiles carry icon stickers through a class on each list item, so they come through Elementor's Text Editor unharmed:

- `<li class="cfhg-note cfhg-has-icon cfhg-icon--calendar">`
- `<li class="cfhg-steps__item cfhg-has-icon cfhg-icon--signpost">`

Icons: pocket-watch, book-stack, calendar, three-islands, globe-plane, ticket, notepad-pencil, thought-question, signpost, magnifier-record, family-tree. The `[cfhg_numbers]` and `[cfhg_steps]` shortcodes, and the homepage CFHG Setup builds, add them for you.

## The five steps guide page

Appearance > CFHG Setup > **Create the five steps page** builds "Starting your family tree" from Elementor widgets:

1. A title and introduction.
2. A Video widget (self hosted, from the theme's `assets/video` folder, with its poster).
3. "Your family tree": an H2 and a Text Editor holding `<ul class="cfhg-guide__questions">`.
4. "Five basic steps": one Row per step with class `cfhg-guide-step cfhg-guide-step--1` to `--5` (this sets the step's colour), alternating sides. The copy column holds the step number (a Text Editor with `<p class="cfhg-guide-step__num cfhg-has-icon cfhg-icon--…">`), an H3 `cfhg-h2 cfhg-guide-step__title`, the question (`cfhg-lead cfhg-guide-step__question`) and the list (`cfhg-guide__points`) or groups (`cfhg-guide__groups`). The image column holds an Image with `cfhg-guide-step__media`, which gets the coloured offset shadow.
5. "Completing charts": a section with class `cfhg-guide__charts` (the navy band) holding three white cards with class `cfhg-guide-card`. The middle card's Text Editor holds the even and odd key (`cfhg-guide__evenodd`) and the small pedigree chart (`cfhg-pedigree`).
6. A white card `cfhg-guide__cta-card` with the next meeting Shortcode and two buttons.

Running the button again rebuilds the same page, replacing any edits made in Elementor.

## Events (CFHG Site plugin)

Free Elementor can't list custom content, so events go in through Shortcode widgets:

| Shortcode | Shows |
|---|---|
| `[cfhg_events]` | Upcoming events. Add `limit="4"`, `island="jamaica"`, `group="barbados"`, `type="talk"` or `when="past"` |
| `[cfhg_calendar_links]` | "Add all our meetings to your calendar" |

An Elementor What's On page: a two-column hero (an H1, a Text Editor for the introduction, and an Image widget with the CSS class `cfhg-postcard`), an H2 "Coming up", `[cfhg_events when="upcoming" limit="20"]`, `[cfhg_calendar_links]`, an H2 "Recent events" and `[cfhg_events when="past" limit="12"]`. On an island page, `[cfhg_events island="jamaica" limit="4"]` lists that island's events. The `[cfhg_next_meeting]` shortcodes already show the real next event.

## Island pages

Appearance > CFHG Setup > **Create an island page** builds the page from a heading, a Text Editor and the illustration in the opening section, then one section per part, each holding a Shortcode widget: `[cfhg_record_finder island="jamaica"]`, `[cfhg_parish_map island="jamaica"]`, `[cfhg_island_sources island="jamaica"]`, `[cfhg_surnames island="jamaica"]`, then a Text Editor with the members' story cards (`<article class="cfhg-island-story">`; remove `is-placeholder` once a story is written), and the island's events and posts. The illustration Image widget has the class `cfhg-island__art` (the postcard). Running the button again rebuilds the page and replaces edits.

## Shortcodes

| Shortcode | Shows |
|---|---|
| `[cfhg_next_meeting style="inline"]` | One line with the next date |
| `[cfhg_next_meeting style="title"]` | The date set large ("Saturday 31 October") |
| `[cfhg_next_meeting style="facts"]` | Where, online, when |
| `[cfhg_hero_proof]` | Small round portraits and the palm badge, beside the members line |
| `[cfhg_prices]` | Prices, with the free first visit highlighted |
| `[cfhg_numbers]`, `[cfhg_steps]`, `[cfhg_islands]`, `[cfhg_partners]` | The lists, from the Customizer copy |
| `[cfhg_stories count="3"]` | The story carousel |
| `[cfhg_snapshot image="intro_photo_1" tone="coral" shape="portrait"]` | A tilted snapshot. Tones: coral, gold, green, sea, blue. Shapes: portrait, landscape. |
| `[cfhg_shape name="hibiscus"]` | Hibiscus, palm-frond or wave |
| `[cfhg_email]` | The group's email address as a link |

## The homepage, section by section

Sections are top-level containers: **Boxed 1600px**, Column, padding
**140 / 32 / 0 / 32** (tablet 110 / 24, mobile 80 / 18), class `cfhg-el-section`
plus the class below.

1. **Opening panel** (`cfhg-el-hero`, ID `top`). Boxed 1600px, Row (Column on tablet and mobile), min height 100vh on desktop, padding 120 / 32 / 64 / 32.
   - A 50% column: the H1 `cfhg-el-hero-title`, the lead, the `[cfhg_hero_proof]` Shortcode, a Row of buttons (`--mark --big`, `--text`) and the inline meeting Shortcode. Each has `cfhg-load-fade`, staggered `d1`, `d2`, `d3`, `d3`.
   - A 50% column with class `cfhg-el-hero-art`, holding Image `cfhg-el-img--disc cfhg-load-iris`.
   - Not rebuilt: the small white, coral, blue and gold circles (decorative).
2. **Who we are** (`cfhg-el-intro`). A Row: snapshot Shortcode (24% width, `cfhg-intro__snap--left`), a white card (44%), and a second snapshot (`--right`).
3. **Next meeting** (`cfhg-el-meeting`, ID `next-meeting`). A Row: a white card (H2 "Next meeting" with `cfhg-h3 ui-meeting-title`, the `title` Shortcode, a P heading `cfhg-h3` holding `<span class="cfhg-highlight" data-cfhg-mark>`, `facts` and prices Shortcodes, buttons) and a 55% column with class `cfhg-el-scrim`: Image `--rounded --fill` plus an absolutely positioned `cfhg-el-caption` heading.
4. **Our group in numbers** (`cfhg-el-numbers`). A centred H2, then a Text Editor with the `cfhg-notes` list.
5. **Start here** (`cfhg-el-start`, ID `start-here`). A split head Row, a Text Editor with the `cfhg-steps` list, and a button.
6. **Research by island** (`cfhg-el-islands`). A 54% column (H2, text, `cfhg-places` list) and a 42% CFHG B Gold column (radius 20, Image `--oval`, positioned palm frond Shortcode).
7. **Stories** (`cfhg-el-stories`). A head Row, then the `[cfhg_stories]` Shortcode.
8. **Research help** (`cfhg-el-help`). A CFHG B Sand Row (radius 20, padding 56) with a copy column, an Image `--rounded`, and a coral hibiscus anchored top-right.
9. **Membership** (`cfhg-el-join`, ID `join`).
   - A container with the photograph as its background (cover, navy overlay at 30%) and class `cfhg-ui-parallax`, holding the `cfhg-el-big cfhg-ui-track` heading.
   - Then a white card (60%) with a top margin of -140px so it overlaps the band.
10. **Partners** (`cfhg-el-partners`). H2, then a white card holding the `cfhg-partners__list` list.

## Differences between the Elementor and theme homepages

- **The opening panel's small circles are theme-only.** They're decorative.
- **The background drift needs a recent browser.** In the theme, the membership photograph drifts in every browser (with a JavaScript fallback). In Elementor it's a background image, which only CSS can move, so it stays still where scroll-driven animation isn't supported.
- **Reveal animations differ slightly.** Elements in Elementor fade in through the theme's script where the list markup is used (notes, steps, islands, partners). Use Elementor's Motion Effects for anything else.
- **Where the lists are edited depends on the version.** In the theme version they come from the Customizer; in the Elementor version they're HTML you edit in place.

## Added in 1.5.0

- **Surname ribbon:** a full-width container (padding 0, CSS class `cfhg-names`) holding one Shortcode widget, `[cfhg_surname_ribbon]`, straight after the opening panel.
- **Living photographs** need nothing in Elementor: an Image widget showing the homepage photograph or the Jamaica illustration gets its film automatically. Replace the image and the film goes.
- **Postcard:** an Image widget with the CSS class `cfhg-postcard` (add `cfhg-postcard--sea` or `cfhg-postcard--gold` to change the printed edge).
- **"On this page" links:** a Text Editor widget with the CSS class `cfhg-jump` holding a bulleted list of links such as `#cfhg-island-finder`. Give each section that ID in Advanced > CSS ID.
- **Records timeline:** a Shortcode widget, `[cfhg_record_timeline island="jamaica"]`.
- **Section spacing on inner pages:** padding-top 104px (tablet 84px, mobile 60px), no padding-bottom; the footer brings its own space. Don't add the theme's section classes that carry their own padding (`cfhg-section`) to an Elementor container.

## Added in 1.6.0: the film lightbox

On the five steps page the film is a section with words on the left and its poster on the right. The poster is a Shortcode widget, `[cfhg_video_lightbox]`. Any link with the CSS class `cfhg-video-open` and a `.mp4` address opens that film in a lightbox, so the "Play the film" button (an Elementor Button with that class) opens it too. Without JavaScript the links simply open the file. The words, button labels and file come from `inc/guide-content.json` (`video`).

## Added in 1.10.0: richer information pages

Pages built from `inc/pages-content.json` can now use, per section:

- `"style": "band"` with `"image"`: a full-width Container with a classic background image, a #10213B overlay at 0.72, padding 140px top and bottom (110 tablet, 80 mobile) and the CSS class `cfhg-band`. Its heading becomes big cream type.
- `"buttons": [[label, url], …]`: Button widgets; the first is highlighter yellow, the rest are text links.
- `"wide": true`: the Text Editor gets the class `cfhg-el-wide` instead of `cfhg-el-prose`, so cards and timelines use the full width while plain paragraphs keep to a readable width.
- In the page itself: `"hero_buttons"`, `"hero_links"` (pill links under the opening line) and `"image_width"`.

The Who we are timeline is an ordered list in a Text Editor (`<ol class="cfhg-milestones">`, each `<li class="cfhg-milestone">` holding a year, a title and a sentence), and What we do is a list of `cfhg-do-card` items with an icon from `assets/images/icons/`. Edit them in the Text Editor's Code tab.

## Added in 1.12.0: Contact and its form

- **Contact Form 7** is required for the form (Plugins > Add New). The theme makes one form, "CFHG contact form", the first time the Contact page is built: name, email, what it's about, message, sent to the group's email address with Reply-To set to the sender. Edit it in Contact > Contact Forms. The Shortcode widget `[cfhg_contact_form]` shows it, styled to match the site; without Contact Form 7 it shows the email address instead.
- `"style": "split"` with `"aside": [shortcodes]` puts a section's words on the left and a white card on the right (the form on Contact).
- `assets/images/icons/envelope.svg` joins the sticker icons.
- What's On shows the next five meetings, then posts from the **Events** category (`[cfhg_story_grid category="events" count="3"]`). Give any post about a meeting, talk, exhibition or open day that category.
