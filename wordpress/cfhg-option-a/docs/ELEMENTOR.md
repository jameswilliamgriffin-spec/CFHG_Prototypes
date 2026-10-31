# Building and editing CFHG Option A in Elementor

## How the pieces fit

- **Design tokens** live in two places that stay in sync: Elementor's Site Settings
  (Global Colours and Global Fonts) and CSS custom properties in
  `assets/css/tokens.css`. The CSS reads Elementor's globals back, so changing
  **CFHG Gold** in Elementor changes the gold everywhere, including the theme's own
  templates, menu and footer.
- **Layout** (columns, widths, gaps, padding, backgrounds, radius) is set natively in
  Elementor, so editors can change it with the normal controls.
- **Type, buttons and image shapes** come from theme CSS classes, added in each
  widget's Advanced > CSS Classes field. This keeps the bold type scale and the disc
  buttons consistent without editors re-entering sizes on every widget.
- **Live content** uses shortcodes in Elementor's free Shortcode widget: the next
  meeting date (always correct), prices, and the latest stories.

## Setting up

1. Appearance > CFHG Setup > **Apply CFHG colours and fonts to Elementor**.
2. Appearance > CFHG Setup > **Create the Elementor homepage**. Tick "make it the
   homepage" to publish it straight away, or leave it as a draft to review.
3. Optional, with Elementor Pro: build a header or footer in Templates > Theme
   Builder. The theme hands over automatically. Without one, the theme's own header
   and footer (with the full-screen menu) are used.

To move the design to another site, use **Download homepage template (.json)** on
the same screen, then Elementor > Templates > Saved Templates > Import.

## Global Colours

| Name | Hex | CSS variable | Used for |
|---|---|---|---|
| Primary (headings) | #0E1F38 | | Default for new Heading widgets |
| Secondary (gold) | #F2B42A | | |
| Text | #0E1F38 | | Default for new Text Editor widgets |
| Accent (buttons) | #C8402F | | Default for new Button widgets |
| CFHG Gold | #F2B42A | --cfhg-gold | Opening and membership panels, button discs |
| CFHG Navy | #0E1F38 | --cfhg-navy | Text, meeting panel, solid buttons |
| CFHG Navy soft | #3B4A60 | --cfhg-navy-soft | Secondary text |
| CFHG Paper | #FBF6EC | --cfhg-paper | Menu panel, text on dark |
| CFHG Sand | #F4ECDD | --cfhg-sand | Footer, title bands, cards |
| CFHG Sand deep | #EADFCB | --cfhg-sand-deep | Alternate cards |
| CFHG Gold soft | #FCE6AF | --cfhg-gold-soft | Research help card |
| CFHG Clay red | #C8402F | --cfhg-clay | Step numbers, hovers |
| CFHG Green | #187A43 | --cfhg-green | "Free" on light backgrounds |
| CFHG Sea | #0F7896 | --cfhg-sea | Notes, first tag |
| CFHG Line | #CDBFA8 | --cfhg-line | Hairline rules |

Primary is navy on purpose: Elementor's Heading widget defaults to Primary, so a
new heading is readable on white rather than gold.

## Global Fonts

| Slot | Name | Family | Weight | Desktop / tablet / mobile |
|---|---|---|---|---|
| Primary | Heading | Hanken Grotesk | 400 | 76 / 56 / 38px |
| Secondary | Display | Montagu Slab | 600 | 136 / 96 / 52px |
| Text | Body | Hanken Grotesk | 400 | 17px |
| Accent | Label | Hanken Grotesk | 600, uppercase | 13 / 13 / 12px |
| Custom | Lead | Hanken Grotesk | 400 | 23 / 21 / 19px |
| Custom | Heading small | Hanken Grotesk | 400 | 46 / 36 / 26px |

The CSS classes below use fluid sizes (they scale smoothly with the screen), which
is why the homepage relies on classes rather than these fixed sizes.

## CSS classes

Add these in a widget's **Advanced > CSS Classes**.

| Class | On | Effect |
|---|---|---|
| `cfhg-display` | Heading | Montagu Slab display size (the opening headline) |
| `cfhg-h1`, `cfhg-h2`, `cfhg-h3` | Heading | The large grotesk heading sizes |
| `cfhg-kicker` | Heading (set to P) | Small uppercase label above a heading |
| `cfhg-kicker--plain` | Heading, with cfhg-kicker | The larger two-line label in the opening panel |
| `cfhg-lead`, `cfhg-body` | Text Editor | Lead paragraph and body text |
| `cfhg-el-btn cfhg-el-btn--solid` | Button | Navy pill with a gold disc (main call to action) |
| `cfhg-el-btn cfhg-el-btn--disc` | Button | Label with a gold arrow disc |
| `cfhg-el-btn cfhg-el-btn--text` | Button | Underlined text link |
| `cfhg-el-img cfhg-el-img--arch` | Image | Arched frame (4:5) |
| `cfhg-el-img cfhg-el-img--rounded` | Image | Rounded corners; add `--portrait` (4:5), `--wide` (16:10) or `--fill` |
| `cfhg-on-dark` | Container | Light text and gold labels on a navy background |
| `cfhg-el-sticky` | Container | Stays in view while the neighbouring column scrolls (desktop) |

Text Editor lists styled by the theme (paste the HTML in the Text tab):

- `<ul class="cfhg-places">` with `<li><a href="..."><strong>Jamaica</strong> <span>note</span></a></li>`
- `<ol class="cfhg-steps">` with `<li><h3>Step title</h3><p>Text</p></li>`
- `<ul class="cfhg-partners__list">` with `<li><h3><a href="...">Name</a></h3><p>Text</p></li>`

## Shortcodes

| Shortcode | Shows |
|---|---|
| `[cfhg_next_meeting style="inline"]` | "Next meeting Saturday 31 October, Solihull Core Library and Zoom" |
| `[cfhg_next_meeting style="big"]` | The large date for the meeting panel |
| `[cfhg_next_meeting style="facts"]` | Where, online and when |
| `[cfhg_prices]` | Prices as a ledger |
| `[cfhg_stories count="3"]` | Latest stories as cards |
| `[cfhg_islands]`, `[cfhg_steps]`, `[cfhg_partners]` | The lists, from the Customizer copy |
| `[cfhg_email]` | The group's email address as a link |

## The homepage, section by section

All sections are top-level containers. Unless noted, each is **Boxed, 1680px**,
direction **Column**, padding **160 / 60 / 0 / 60** (tablet 120 / 40, mobile 88 / 20),
CSS class `cfhg-el-section` plus the class below.

### 1. Opening panel (`cfhg-el-hero`, CSS ID `top`)
Full width. Row (column on tablet and mobile), gap 60, min height 92vh.
Background CFHG Gold. Radius 0 / 0 / 40 / 40. Padding 150 / 60 / 60 / 60 (makes
room for the header).
- Column 58%, justify space-between:
  Heading P `cfhg-kicker cfhg-kicker--plain`; Heading H1 `cfhg-display`;
  Text Editor `cfhg-lead`; Row of Button `--solid` and Button `--text`;
  Shortcode `[cfhg_next_meeting style="inline"]`.
- Column 42%, justify end: Image `cfhg-el-img--arch`.
- Not rebuilt in Elementor: the vertical "Read on" cue. It is decorative.

### 2. Who we are (`cfhg-el-intro`)
Heading P `cfhg-kicker`; Heading H2 `cfhg-h1 cfhg-statement` (the small photo is an
`<img>` inside a `<span class="cfhg-inline-tile">` in the heading text); Row
justified end, with a 34% Column of Text Editor and Button `--disc`.

### 3. Photo strip (`cfhg-el-strip`)
Full width, Row (stays a row on mobile), no wrap, overflow Auto, align start,
gap 40. Five Image widgets with Custom width (30, 20, 23, 32, 22vw; 72vw on
mobile), Flex size None, and top margins (0, 7, 2, 10, 4rem) for the stagger.
Classes `cfhg-el-strip__item--1` to `--5` set each photo's shape.

### 4. Next meeting (`cfhg-el-meeting-wrap`, CSS ID `next-meeting`)
Padding top 120. One Row container `cfhg-el-meeting cfhg-on-dark`, background
CFHG Navy, radius 40, padding 60:
- Column 28%: Heading P `cfhg-kicker`; Shortcode `[cfhg_next_meeting style="big"]`.
- Column 42%: Heading H2 `cfhg-h2`; Shortcodes `facts` and `[cfhg_prices]`;
  Row of Button `--solid` and Button `--text` (email).
- Column 30%: Image `cfhg-el-img--rounded cfhg-el-img--portrait`.

### 5. Research by island (`cfhg-el-islands`)
Row (space-between, align end): Column 56% with kicker and H2 `cfhg-h2`;
Column 32% with Text Editor. Then Row (stretch): Column 42% with a Text Editor
holding the `cfhg-places` list; Column 58% with Image `--rounded --fill`.

### 6. Start here (`cfhg-el-start`, CSS ID `start-here`)
Row (space-between): Column 40% `cfhg-el-sticky` with kicker, H2, Text Editor
`cfhg-lead` and Image `--rounded --portrait`; Column 52% with a Text Editor holding
the `cfhg-steps` list, then Button `--disc`.

### 7. Stories (`cfhg-el-stories`)
Row (space-between, align end): Column 60% with kicker and H2 `cfhg-h1`;
Button `--disc` ("All stories"). Then Shortcode `[cfhg_stories count="3"]`.

### 8. Research help (`cfhg-el-help`)
Row, gap 0, align end: Column 62% with Image `--rounded --wide`; Column 44%
`cfhg-el-help__card`, background CFHG Gold soft, radius 20, padding 48, margin
left -6% so it overlaps the photo (on tablet and mobile it overlaps from above
instead). Inside: kicker, H2, Text Editor, Button `--solid`.

### 9. Membership (`cfhg-el-join-wrap`)
One Row container `cfhg-el-join`, background CFHG Gold, radius 40, padding 60,
align centre: Column 48% with kicker, H2 `cfhg-h1`, Text Editor `cfhg-lead` and a
Row of Button `--solid` and Button `--text`; Column 52% with Image `--rounded --wide`.

### 10. Partners (`cfhg-el-partners`)
Padding 160 / 60 / 160 / 60. Heading P `cfhg-kicker`; H2 `cfhg-h2`; Text Editor
holding the `cfhg-partners__list` list.

## Differences between the Elementor and theme homepages

- The vertical "Read on" scroll cue is theme-only.
- In the theme version the island, step and partner lists come from the
  Customizer. In the Elementor version they are plain HTML in Text Editor widgets,
  edited directly in Elementor.
- Section fade-in animation applies to the theme version and to story cards,
  steps, islands and partners in both. Use Elementor's Motion Effects for more.
