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
| `cfhg-label` | Heading (set to P) | Small uppercase label with a green bar |
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
| `cfhg-el-kicker` | Text Editor holding `<p class="cfhg-hero__kicker">` | White pill label with a coral dot |
| `cfhg-el-chip` | Heading, positioned absolute | White pill label on a photograph |
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
   - A 50% column: the `cfhg-el-kicker` Text Editor, the H1 `cfhg-el-hero-title`, the lead, the `[cfhg_hero_proof]` Shortcode, a Row of buttons (`--mark --big`, `--text`) and the inline meeting Shortcode.
   - A 50% column with class `cfhg-el-hero-art`, holding Image `cfhg-el-img--disc`.
   - Not rebuilt: the small white, coral, blue and gold circles (decorative).
2. **Who we are** (`cfhg-el-intro`). A Row: snapshot Shortcode (24% width, `cfhg-intro__snap--left`), a white card (44%), and a second snapshot (`--right`).
3. **Next meeting** (`cfhg-el-meeting`, ID `next-meeting`). A Row: a white card (label, `title` Shortcode, H2 with a `cfhg-highlight` span, `facts` and prices Shortcodes, buttons) and a 55% column with Image `--rounded --fill` plus an absolutely positioned `cfhg-el-chip` heading.
4. **Our group in numbers** (`cfhg-el-numbers`). A centred H2, then a Text Editor with the `cfhg-notes` list.
5. **Start here** (`cfhg-el-start`, ID `start-here`). A split head Row, a Text Editor with the `cfhg-steps` list, and a button.
6. **Research by island** (`cfhg-el-islands`). A 54% column (label, H2, text, `cfhg-places` list) and a 42% CFHG B Gold column (radius 20, Image `--oval`, positioned palm frond Shortcode).
7. **Stories** (`cfhg-el-stories`). A head Row, then the `[cfhg_stories]` Shortcode.
8. **Research help** (`cfhg-el-help`). A CFHG B Sand Row (radius 20, padding 56) with a copy column, an Image `--rounded`, and a coral hibiscus anchored top-right.
9. **Membership** (`cfhg-el-join`, ID `join`).
   - A container with the photograph as its background (cover, navy overlay at 30%), holding the `cfhg-el-big` heading.
   - Then a white card (60%) with a top margin of -140px so it overlaps the band.
10. **Partners** (`cfhg-el-partners`). Label, H2, then a white card holding the `cfhg-partners__list` list.

## Differences between the Elementor and theme homepages

- **The opening panel's small circles are theme-only.** They're decorative.
- **Reveal animations differ slightly.** Elements in Elementor fade in through the theme's script where the list markup is used (notes, steps, islands, partners). Use Elementor's Motion Effects for anything else.
- **Where the lists are edited depends on the version.** In the theme version they come from the Customizer; in the Elementor version they're HTML you edit in place.
