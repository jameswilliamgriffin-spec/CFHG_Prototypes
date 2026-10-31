# CFHG Option A: WordPress theme

Design option A for the Caribbean Family History Group. A bold, warm theme built
for Elementor, based on the structure of outil.org and coloured from the CFHG
palm logo.

## Install

1. Appearance > Themes > Add New > Upload Theme, choose `cfhg-option-a.zip`, Install, Activate.
2. Install and activate **Elementor** (free) from Plugins > Add New. Elementor Pro is
   optional; it is only needed to rebuild the header or footer in Theme Builder.
3. Go to **Appearance > CFHG Setup** and work down the page:
   1. **Apply CFHG colours and fonts to Elementor.** Writes the palette and type
      styles into Elementor > Site Settings. (This also happens automatically the
      first time the theme is activated with Elementor running.)
   2. **Create the Elementor homepage.** Builds a page called Home from Elementor
      containers and widgets, copies the photographs into the Media Library with
      their alt text, and can set it as the homepage straight away.
   3. **Create starter menu**, or build your own in Appearance > Menus.

The theme works without Elementor too. Until a page is set as the homepage, the
theme shows its own homepage, edited in Appearance > Customize > CFHG homepage.

Requires WordPress 6.3+ and PHP 7.4+. No build step: plain PHP, CSS and JavaScript.

## Where to edit things

| What | Where |
|---|---|
| Homepage (Elementor version) | Pages > Home > Edit with Elementor |
| Homepage (theme version) | Appearance > Customize > CFHG homepage |
| Meeting venue, time, prices, months off | Appearance > Customize > Meetings and prices |
| Move a meeting date | Same screen, "Override the next date" |
| Email, social links, footer text | Appearance > Customize > Contact, social and footer |
| Main menu, footer links, small print | Appearance > Menus (locations: Main menu, Footer links, Footer small print) |
| Logo | Appearance > Customize > Site Identity (falls back to the CFHG logo in the theme) |
| Colours and fonts | Elementor > Site Settings > Global Colours / Global Fonts |

### The next meeting date is automatic

It is worked out every time a page loads: the last Saturday of the month,
skipping May, August and December. On the day itself, that day is still shown.
It appears in the opening panel, the meeting section, the menu and the footer, so
nobody has to remember to update it. If a meeting moves, set "Override the next
date"; the override stops applying once the date has passed.

### Stories fill themselves

The Stories section shows the latest three posts in the **Stories** category
(change the category slug in the Customizer). Until any exist, it shows three
starter cards written from the group's sitemap. Give each post a featured image
and a short excerpt.

## Page templates

| Template | Use it for |
|---|---|
| Default | Normal pages: title band plus a readable text column. Pages built with Elementor automatically run edge to edge. |
| CFHG Full width | Elementor pages that should start below the header. |
| CFHG Canvas (no header or footer) | Landing pages built entirely in Elementor. |
| Elementor Full Width / Elementor Canvas | Elementor's own templates also work. The homepage uses Elementor Full Width. |

## Theme structure

```
cfhg-option-a/
  style.css                 Theme header only
  functions.php             Loads the files in /inc
  header.php, footer.php    Defer to Elementor Pro Theme Builder if it has a header/footer
  front-page.php            Elementor homepage if there is one, else the theme sections
  index.php, page.php, single.php, archive.php, search.php, 404.php, searchform.php
  page-templates/           CFHG Full width, CFHG Canvas
  template-parts/home/      One file per homepage section
  template-parts/site/      Theme header and footer
  template-parts/content/   Page, post and list layouts
  inc/defaults.php          ALL default copy, in one place
  inc/customizer.php        Customizer fields (defined as data)
  inc/components.php        Shared markup: meeting date, prices, islands, steps, stories, partners
  inc/shortcodes.php        The same components as shortcodes, for Elementor
  inc/elementor.php         Theme Builder locations, fonts, design tokens sync
  inc/elementor-template.php  The homepage as Elementor containers and widgets
  inc/admin.php             Appearance > CFHG Setup
  inc/images.php            Image list with sizes, alt text and focal points
  assets/css/tokens.css     Design tokens as CSS custom properties
  assets/css/main.css       All styles
  assets/js/main.js         Menu, header, reveal, photo strip (vanilla JS)
  assets/fonts/             Self-hosted Montagu Slab and Hanken Grotesk (OFL)
  assets/images/            Compressed photographs and logos
  elementor/cfhg-design-tokens.json  Token values for reference or manual entry
  docs/                     This guide and the Elementor guide
```

## Design notes

**What came from outil.org:** the type ratios (a 10px-based scale running from a
13px label to 128px+ display), the spacing rhythm (5, 10, 15, 20, 30, 45, 60,
80px), radii (5, 10, 20, 40px), the 0.2s / 0.3s / 0.5s plain `ease` motion, the
bold coloured opening panel with rounded lower corners, the vertical scroll cue,
the "label plus disc" button whose disc grows 15% on hover while the arrow shrinks
back, the full-screen menu as a rounded card with large ruled links, the staggered
sideways photo strip, the ruled list beside a photograph, the large rounded story
cards in alternating paper tones, slab numerals in numbered lists, and the footer
with arrowed link columns.

**What was dropped or changed:** the house-shaped image mask became an arch (a
doorway or library window, not a building). The brown and squash palette became
navy and gold from the CFHG logo, with paper and sand neutrals and the logo's
red, green and sea used sparingly. Partner logo tiles became ruled rows with the
names set in the slab face, because no partner logos were supplied. The cookie
banner is not part of a theme; use a consent plugin.

**Fonts:** outil uses Aeonik (licensed) and Montagu Slab. This theme uses
**Montagu Slab** (the same open-source family) for display and **Hanken Grotesk**
as the closest open-source match for Aeonik. Both are self-hosted, so no visitor
data goes to Google, which matters under UK GDPR. Elementor is told not to load
Google Fonts for the same reason; remove the filter in `inc/elementor.php` if
editors want other Google fonts.

**Contrast:** navy text on gold is about 8.9:1, clay red on white about 5:1, sea
on white about 5:1, green on white about 5.4:1.

**Motion:** sections fade up once as they arrive. Nothing is hidden if JavaScript
fails, and nothing moves for people who have asked their device for reduced motion.

## Images

All photographs were resized and compressed (31.5 MB of originals down to 2.4 MB),
renamed descriptively, and given real alt text and width/height attributes. The
originals in `Website Image Library` are untouched. Framing uses a focal point per
image (`object-position`) rather than cropping the files, so nobody's face is
permanently cut.

### Used

| File in theme | Original | Where |
|---|---|---|
| family-album-caribbean-photographs | cfhg-candid-17-family-album-flatlay-portrait | Opening panel, in the arch |
| members-sharing-a-moment | cfhg-candid-03-shared-moment-square | Small photo set into the "Who we are" statement |
| meeting-at-the-library-table | cfhg-candid-04-library-table-landscape | Photo strip |
| tea-break-at-a-meeting | cfhg-candid-08-meeting-break-portrait | Photo strip |
| courtyard-conversation | cfhg-candid-11-courtyard-conversation-square | Photo strip |
| walking-after-a-meeting | cfhg-candid-09-walking-after-meeting-landscape | Photo strip |
| comparing-notes-outdoors | cfhg-candid-13-outdoor-research-chat-square | Photo strip |
| reading-between-library-shelves | cfhg-candid-05-between-the-shelves-portrait | Next meeting |
| caribbean-map-research | cfhg-candid-14-map-research-landscape | Research by island |
| young-researcher-with-old-photograph | cfhg-10-young-researcher-portrait | Start here |
| research-notes-and-old-photographs | cfhg-candid-16-research-in-progress-flatlay-landscape | Story card: Ursula's story |
| researcher-taking-notes-from-records | cfhg-candid-06-archive-notes-square | Story card: tracing ancestors |
| member-helping-with-research | cfhg-candid-07-research-help-landscape | Research help |
| three-generations-with-family-album | cfhg-candid-18-three-generations-family-landscape | Membership |
| cfhg-logo-horizontal.png | Logo ideas/logo 1.png | Header (cut out from its white background) |
| cfhg-logo-badge.png | Logo ideas/logo 1.1.png | Footer and site icon |

### Left out, and why

| Original | Reason |
|---|---|
| 01 Archival family and migration collage | Shows invented records: a dated Kingston postmark and a named ship's passenger list. On a genealogy site, researchers will read these as real documents. |
| 02 Family research scene, navy framing | A navy frame is baked into the picture. |
| 03 Generations sharing photographs, coral branding | An earlier logo and a coral gradient are baked in. |
| 04 Collaborative research table, green branding | An earlier logo is baked in. |
| 05 Family portrait and archival papers, plum branding | "CF HG" lettering is baked in. |
| 06 Caribbean and diaspora connections, blue branding | A logo is baked in, and the glowing network lines read as generic tech. |
| cfhg-01 generations hero | Very close to the three-generations photo used for Membership; using both repeats. |
| cfhg-04 family archive | Very close to the album photo in the opening panel. |
| cfhg-17 DNA guidance | No DNA section on the homepage. Keep it for the DNA page. |
| candid-01 conversation | Much darker than the rest of the strip. |
| candid-02 listening | A tight single-face crop that suits neither the strip nor a card. |
| candid-10 park bench memories | Good; only left out because the strip has five places. Use on What's On. |
| candid-12 local history walk | As above; suits a Past events page. |
| candid-15 atlas detail | Overlaps with the map photo used for the islands. |

## Assumptions to check with the group

These are marked `ASSUMPTION` in `inc/defaults.php` and are all editable without code.

- **Meeting time** is not shown, because it wasn't supplied. Add it in the Customizer.
- **Zoom link** is described as sent by email on request, not published.
- **Page addresses** such as `/research/jamaica/`, `/whats-on/` and `/join/` follow the
  agreed structure but don't exist yet.
- **Trinidad** is written as "Trinidad and Tobago" in the island list. The group's own
  document says "Trinidad".
- **The five steps** are drafted from standard practice and the group's emphasis on
  talking to elders. They need checking against the 5-steps video.
- **Story summaries** are written from the titles alone. "SDP" is assumed to be
  Stephen D. Porter, from the guest speaker list.
- **The British West India Regiment** card is named as in the group's document. The
  photograph of Dennis in Belgium suggests the First World War, when the regiment
  was the British West *Indies* Regiment. Worth confirming the exact name.
- **Social media addresses** were not supplied, so those links are hidden until added.
- **Partner links**: Solihull Libraries and the Family History Federation link to their
  public sites; the WAWI Project has no link because no address was supplied.

## Before launch

- **Replace the photographs with real ones.** The image library is AI-generated. The
  people shown are not members, and the "archival" photographs are not real family
  records. For a family history group that is a trust issue: replace them with real
  meeting photographs (with consent, under the group's photography policy) and real
  archive images where members are happy to share them.
- Ask for the logo as an SVG or a large transparent PNG. The current files are flat
  images cut out of a white background, so they are good at header size but not
  beyond.
