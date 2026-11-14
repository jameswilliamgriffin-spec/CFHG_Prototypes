# CFHG Option B: WordPress theme

Design option B for the Caribbean Family History Group. A warm, album-like theme
built for Elementor, based on the structure of the UPA DI 2023-2024 annual report
site and coloured from the CFHG palm logo.

Option B is fully self-contained. It can be installed alongside Option A; only
one theme is active at a time, and each keeps its own Customizer settings.

## Install

1. Appearance > Themes > Add New > Upload Theme, choose `cfhg-option-b.zip`, Install, Activate.
2. Install and activate **Elementor** (free). Elementor Pro is only needed to rebuild
   the header or footer in Theme Builder.
3. Go to **Appearance > CFHG Setup**:
   1. **Apply CFHG colours and fonts to Elementor.** (Also happens automatically on
      first activation when Elementor is running.) Option B's colours use their own
      IDs (`cfb…`), so they sit alongside Option A's rather than replacing them.
   2. **Create the Elementor homepage.** Builds a page called Home from Elementor
      containers and widgets and copies the photographs into the Media Library.
   3. **Create starter menu**, or build your own in Appearance > Menus.

Without Elementor, the theme shows its own homepage, edited in
Appearance > Customize > CFHG homepage.

Requires WordPress 6.3+ and PHP 7.4+. No build step.

Version 1.3.0 works with the **CFHG Site** plugin (`wordpress/cfhg-site`),
which adds Events. With it, "Next meeting" everywhere shows the real next
event and links to it, each event has its own page (`single-cfhg_event.php`),
and the "CFHG What's On" page template lists upcoming and past events. Without
the plugin, the theme works exactly as before. See "Events" below.

Version 1.2.0 adds the group's real five steps (from their "Starting your
family tree" presentation), an icon sticker on every number and step card, and
the "Starting your family tree" guide page with the explainer video. See
"Card icons" and "The five steps guide page" below.

Version 1.1.0 adds the polish pass the client approved in the static preview:
the motion, the labels taken off the headings, WebP images and new icons. See
`UI-PASS.md` for what changed and `ELEMENTOR.md` ("Polish effects") for how to
use it in Elementor.

## Where to edit things

| What | Where |
|---|---|
| Homepage (Elementor version) | Pages > Home > Edit with Elementor |
| Homepage (theme version) | Appearance > Customize > CFHG homepage |
| The homepage's title in the browser tab, the opening heading, the line beside the portraits and the circle photograph | Customize > CFHG homepage > Opening panel |
| The group in numbers | Customize > CFHG homepage > Our group in numbers (one per line: number, a bar, what it means, and optionally a bar and an icon name) |
| The five steps on the homepage | Customize > CFHG homepage > Start here (one per line: title, a bar, text, and optionally a bar and an icon name) |
| The five steps guide page ("Starting your family tree") | Pages > Starting your family tree > Edit with Elementor. Created from Appearance > CFHG Setup > Create the five steps page. |
| Meeting venue, time, prices, months off, a moved date | Customize > Meetings and prices |
| Email, social links, header Join button, footer | Customize > Contact, social, header and footer |
| Menus | Appearance > Menus. Menu items are colour-coded automatically, in order. |
| Colours and fonts | Elementor > Site Settings > Global Colours / Global Fonts |

The next meeting date is worked out automatically (last Saturday of the month,
skipping May, August and December), exactly as in Option A. Stories fill
themselves from the latest posts in the Stories category.

## What came from the reference, and what didn't

**Kept:** the warm cream ground and white cards at a 12.5px radius; colour-coding,
with a short coloured bar against each menu item, island and partner; the logo on a
white pill in the header; the "Menu" button with a plus that turns into a
yellow "Close" with a minus; the menu as a tall white side panel over a blurred page; photographs as tilted snapshots in coloured frames with a
small sticker; a "year in numbers" section of scattered coloured notes; colour-blocked
tiles; a story carousel with a "1/3" counter; highlighter-yellow buttons with a small
icon square; a giant word across a photograph band, with a white card overlapping it;
and quick ease-out motion (`cubic-bezier(0.22, 1, 0.36, 1)` at 0.25 to 0.35s).

**Opening panel (reworked):** laid out after a campaign-style landing page rather than
the reference: a white pill label, the heading with one italic word on a highlighter
band, the introduction, a row of small round portraits ending in the palm badge, the
buttons, and on the right a photograph in a large sea-blue circle that runs off the
edge of the page, with smaller white, coral, blue and gold circles around it. The
tokens are unchanged. The circle takes an ordinary photograph or a cut-out portrait
on a transparent background (PNG or WebP), which then sits on the sea blue.

**Changed or dropped:**
- **Colours:** their purple and lilac aren't in the CFHG logo, so the section colours
  are redrawn from the logo's ring instead: coral, gold, green, sea and blue, with
  navy text.
- **Shapes:** their flower and leaf cut-outs became a hibiscus and a palm frond,
  echoing the palm logo. They're drawn as SVG in code, used sparingly, and kept clear
  of text.
- **Motion:** GSAP is replaced by about 200 lines of plain JavaScript.
- **Fonts:** their licensed fonts are replaced by **Jost** (open-source, close to their
  geometric display face) and **Outfit** (matches their light body face, and is the
  font already used in the CFHG brand presentation). Both are self-hosted, so no
  visitor data goes to Google.

**Contrast:** navy text passes WCAG AA on every section colour (coral about 5.3:1,
blue 4.9:1, green 5:1, sea 7:1, gold and highlighter much higher). White text is only
used on the deep green (5.4:1).

## Images

All photographs were resized and compressed (24.5 MB of originals down to 1.8 MB),
renamed descriptively and given alt text and dimensions. Originals are untouched.
Option B deliberately leans on photographs Option A didn't use, so the two options
don't look like the same site recoloured.

### Used

| File in theme | Original | Where |
|---|---|---|
| three-generations-with-an-album-on-the-sofa | cfhg-01-generations-hero-landscape | Opening panel, the circle; and one small portrait (`avatar-album`) |
| avatar-flat-cap, avatar-smiling | cropped from the park bench and dusk photographs below | Opening panel, small portraits |
| sharing-an-old-photograph-on-a-bench | cfhg-candid-10-park-bench-memories-portrait | "Who we are", left snapshot |
| hands-holding-a-family-album | cfhg-04-family-archive-landscape | "Who we are", right snapshot |
| group-at-a-library-table | cfhg-candid-04-library-table-landscape | Next meeting |
| marking-a-caribbean-map | cfhg-candid-14-map-research-landscape | Research by island, oval window |
| copying-details-from-a-register | cfhg-candid-06-archive-notes-square | Story: tracing ancestors |
| notebook-and-old-photographs | cfhg-candid-16-research-in-progress-flatlay-landscape | Story: Ursula's story |
| helping-with-research-at-a-laptop | cfhg-candid-07-research-help-landscape | Research help |
| local-history-walk | cfhg-candid-12-local-history-walk-landscape | Membership band |
| conversation-at-dusk | cfhg-candid-01-conversation-landscape | Footer band (dark, under light text) |
| cfhg-logo-horizontal.png, cfhg-logo-badge.png | Logo ideas/logo 1.png, logo 1.1.png | Header, footer, snapshot stickers, site icon |

### Left out, and why

| Original | Reason |
|---|---|
| 01 to 06 (the branded set) | Same reasons as Option A: earlier logos, frames or lettering baked into the picture, and 01 shows invented records that genealogists would read as real. |
| cfhg-10 young researcher | Used in Option A's Start here; B's steps are colour tiles without photographs. |
| cfhg-17 DNA guidance | No DNA section on the homepage. Keep it for the DNA page. |
| candid-02, 03, 05, 08, 11, 13, 17, 18 | Used by Option A. Leaving them out keeps the two options visibly different. |

## Assumptions to check with the group

All are marked `ASSUMPTION` in `inc/defaults.php` and editable without code:
- the meeting time;
- the Zoom link being sent by email;
- the page addresses;
- "Trinidad and Tobago" instead of "Trinidad";
- the five steps;
- the story summaries, and "SDP" being Stephen D. Porter;
- the West India / West Indies Regiment naming;
- the social media addresses;
- the "group in numbers" wording. Each figure is from the group's document: over 40 years of research, the 2011 FamilySearch affiliation, 9 meetings a year, 3 island groups, 4 countries, a free first visit.

## Before launch

- Replace the AI-generated photographs with real ones, with consent under the group's
  photography policy. The people shown are not members.
- Get the logo as SVG. The current files are cut-out PNGs.
- The logo is not final. Option B's palette is built from the original palm logo
  (shortlist 01/02). If a coral refinement (04 or 05) is chosen, the coral and gold
  are already in this palette, so it should need only small adjustments in Elementor's
  Global Colours.

## Card icons

Every card in "Our group in numbers" and "Start here" carries a small icon
sticker, drawn for this theme in `assets/images/icons/` (SVG, about 700 bytes
each). Each list line can name its icon after a second bar:

    40+ | years: the longest any of us has been researching | pocket-watch

Leave the icon off to get the usual one for that position, or write `none`.
The icons are: pocket-watch, book-stack, calendar, three-islands, globe-plane,
ticket, notepad-pencil, thought-question, signpost, magnifier-record and
family-tree. The list lives in `cfhg_card_icons()` in `inc/components.php`; to
add one, draw a 96 × 96 SVG in the same style, add it there and add its
`.cfhg-icon--name::after` rule in `assets/css/main.css` (section 6.5a).

The icon is a class on the card (`cfhg-icon--calendar`), shown with CSS rather
than an inline image, so the list HTML survives being pasted into Elementor's
Text Editor.

## The five steps guide page

"Starting your family tree" (`/starting-your-family-tree/`) is the page the
homepage's "Read the five steps in full" button links to. It has the explainer
video, the two "Your family tree" questions, the five steps in full with a
photograph each, how to complete pedigree charts and family group records, and
a call to come to a meeting.

- **Words:** `inc/guide-content.json`, taken from the group's presentation.
  The static preview reads the same file.
- **Create it:** Appearance > CFHG Setup > Create the five steps page. With
  Elementor, it is built from Elementor widgets so every word is editable
  there. Without Elementor it uses the "CFHG Five steps guide" page template.
- **Video:** `assets/video/cfhg-five-steps.mp4` (720p, 5.7 MB, from the
  18 MB master in `2 Brand Assets`) and its poster frame. It has no captions
  yet: if a voiceover is added, add captions too.

## Events

Events come from the CFHG Site plugin (see its README). The theme:

- **Next meeting** (opening panel, meeting card, menu and footer) asks
  `cfhg_next_meeting_info()` in `inc/template-tags.php`. The plugin fills it
  with the next event of the group chosen in Events > Settings, with its time,
  where, and a link to its page. With no upcoming events, the Customizer's
  pattern (last Saturday, skipping May, August and December) is used, so the
  homepage is never empty.
- **One event:** `single-cfhg_event.php`. The title and date, a cancelled
  notice if it is cancelled, the short description, the write-up afterwards,
  and a "When and where" card with Add to my calendar and Ask for the Zoom
  link.
- **What's On:** a page using the **CFHG What's On** template (Page
  attributes > Template). It shows the page's own words, then Coming up, a
  calendar subscription link, then Recent events.
- **Styles:** `assets/css/main.css` section 7.2.
