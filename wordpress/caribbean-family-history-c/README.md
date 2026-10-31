# Caribbean Family History Group: Option C

A WordPress theme for the Caribbean Family History Group, styled like a small museum: a white header with Donate and Become a member, an A- / A / A+ text-size control, a sea-blue navigation bar, and an Islands section that scrolls sideways. It works on its own and with Elementor (free or Pro).

Everything uses the `cfhg_c_` / `cfhg-c-` prefix and the text domain `caribbean-family-history-c`, so it can be installed next to Options A and B.

## Install

1. In WordPress go to **Appearance > Themes > Add New > Upload Theme** and upload `caribbean-family-history-c.zip`. Activate it.
2. Go to **Appearance > Option C setup** and press the buttons you want, in order:
   1. **Create starter islands**: adds Jamaica, Barbados, St Kitts and Nevis, Trinidad and Tobago and Montserrat as Island posts, with their text and illustrations.
   2. **Create menus**: adds the Main navigation and Header utility menus and assigns them.
   3. **Sync to Elementor** (only if Elementor is active): writes the colours and fonts into Elementor's Site Settings.
   4. **Build homepage** (only if Elementor is active): creates an editable Home page in Elementor and sets it as the front page.
3. Go to **Settings > Permalinks** and press Save once, so `/islands/` works.
4. Create the ordinary pages the menus point to: Research, What's on, Stories (set as the Posts page under Settings > Reading), Membership, About us, Contact, Support us, Privacy policy.

Requires WordPress 6.3+ and PHP 7.4+.

## Header buttons, meeting details, contact

**Appearance > Customize > Caribbean Family History Group**:

- **Header buttons**: label and link for Donate and Become a member. Links can be full addresses or site paths such as `/membership/`.
- **Meetings**: time and place. Dates are worked out automatically (last Saturday of the month, not May, August or December). If one meeting moves, set **Move the next meeting to** and it replaces the usual date for that month.
- **Contact, newsletter and social**: contact email, the newsletter form address from your mailing list provider (until it is set, sign-ups open an email to the group), Instagram and X links.
- **Homepage text**: the hero heading and paragraph and the intro statement, used when the homepage is not built in Elementor.

The two menus are under **Appearance > Menus**: **Main navigation bar** (the blue bar, one level of dropdowns allowed) and **Header utility links** (the small links beside the buttons). The footer repeats the main menu.

## The homepage

Without Elementor, the homepage is printed by the theme from seven sections in `template-parts/home/`:

| Section | File | In Elementor |
|---|---|---|
| Hero (headline, arched print, next-meeting card, logo-colour rule) | `hero.php` | Containers: heading, text, two buttons, image with CSS class `cfhg-c-arch`, and a Shortcode widget with `[cfhg_c_next_meeting]` |
| Intro (centred) | `intro.php` | Container with heading (class `cfhg-c-statement`), text and a button |
| Islands scroller | `islands.php` | Heading and text, then a Shortcode widget with `[cfhg_c_islands]` |
| Three ways in (meeting, research, share) | `ways.php` | Three row containers, each an image beside text; the third has class `cfhg-c-el-share` for the dark panel |
| What's on | `whats-on.php` | Shortcode widget `[cfhg_c_section name="whats-on"]` (dates are calculated, so this stays a shortcode) |
| Membership and donations | `support.php` | Shortcode widget `[cfhg_c_section name="support"]`, or rebuild with a container, heading, text and two buttons |
| Stories and news | `news.php` | Shortcode widget `[cfhg_c_section name="news"]`, or a Posts / Loop Grid widget |

**Build homepage** on the setup screen creates exactly this in Elementor, so editors can change any heading, paragraph, image or button directly. Any section can also be dropped onto any page with `[cfhg_c_section name="hero"]` (or intro, islands, ways, whats-on, support, news).

Button styles come from CSS classes on the Button widget (Advanced > CSS Classes): `cfhg-c-el-btn cfhg-c-el-btn--gold`, `--outline`, `--solid` or `--text`. Eyebrow labels are Heading widgets set to `p` with class `cfhg-c-eyebrow`. The large hero headline uses `cfhg-c-display`.

Page templates for Elementor layouts:

- **Elementor Full Width** or **Full width (header and footer)**: the site header and footer, with your content edge to edge.
- **Canvas (no header or footer)**: content only.

With Elementor Pro, header and footer templates made in **Theme Builder** replace the theme's own header and footer automatically.

## Islands

Islands live under **Islands** in the admin menu. Each island has:

- a **title**, the main **text**, and an **Island image** (featured image, 3:2 landscape works best);
- in the **Island card** box: a one or two sentence **Summary** for the scroller and a short **Card label** such as "Sub-group" or "Research guide";
- an **Order** number under Page Attributes, which sets the order in the scroller.

To add an island, add an Island post. To remove one, unpublish it. The scroller, the islands index (`/islands/`) and the "Other islands" strip on each island page all update by themselves. If there are no Island posts at all, the five starter islands from the theme are shown so the homepage is never empty.

**Rebuilding the scroller in Elementor**

- **With Elementor Pro**: add a **Loop Carousel** widget, source **Islands**. Make a loop item template with the featured image (Image widget, dynamic: Featured Image), the title (Heading, dynamic: Post Title, linked to the post), the summary (Text, dynamic: Post Custom Field `cfhg_c_summary`) and the label (dynamic: Post Custom Field `cfhg_c_label`). Set slides to 3.5 on desktop and 1.2 on mobile, with arrows on.
- **With free Elementor**: use the Shortcode widget with `[cfhg_c_islands]`. It has the arrows, scroll-snap and keyboard support built in. If you prefer to build it by hand, a Loop Grid needs Pro; the nearest free option is a horizontal container (Direction: Row, Wrap: No wrap, Overflow: Auto) holding one Image Box per island.

## Text size control (A- / A / A+)

- A- and A+ step the page's root font size between 87.5% and 150% in 12.5% steps; A resets to 100%.
- Every size in the theme is in rem, so the whole page scales, including the header, which wraps rather than overflowing at 150%.
- The choice is saved in the visitor's browser (`localStorage`, key `cfhg-c-text-size`) and applied before the page is drawn, so there is no jump on the next visit. Nothing is sent to the server.
- Each button has a spoken label (Smaller text, Default text size, Larger text), the new size is announced to screen readers, and A- / A+ report themselves as unavailable at the limits.
- Code: `cfhg_c_text_size_control()` in `inc/template-tags.php`, and the first block of `assets/js/main.js`.

## Design tokens

- **CSS custom properties**: the top of `assets/css/main.css` (section 2, "Tokens"). Colours, fonts, the type scale, spacing, grid and motion all live there.
- **Elementor**: `cfhg_c_elementor_tokens()` in `inc/elementor.php`. **Sync to Elementor** writes them into Site Settings as:
  - Global colours: Primary `#13202D` (ink, used by Heading widgets), Secondary `#00568F` (sea blue), Text `#13202D`, Accent `#00568F` (button background; white text passes at 7.7:1), plus custom colours Paper, Sun gold, Shallows, Teal, Palm green, Ring red, Muted text and Rule.
  - Global fonts: Primary Newsreader 500, Secondary Source Sans 3 600, Text Source Sans 3 400 at 1.125rem, Accent Source Sans 3 600 uppercase.
  - To set them by hand instead, enter the same values in **Elementor > Site Settings > Global Colors / Global Fonts**.
- **Block editor**: the same colours are in the editor palette (`inc/setup.php`).

The fonts are self-hosted in `assets/fonts/` (Newsreader and Source Sans 3, both SIL Open Font License; licences included). Newsreader is preloaded. Elementor is told not to load Google Fonts.

## Images

- `assets/images/generated/`: seven screenprint-style illustrations made with ChatGPT image generation for this theme: five islands, the hero, and a madras cloth texture. Places and textures only: no people, no faces, no imitation archive photographs. Prompts, settings and the review log are in `image-prompts.md`.
- `assets/images/library/`: nine photographs copied, resized and compressed from the group's Website Image Library. These are AI-generated too, so their alt text starts "Illustrative photograph" and none of them is described as real members or real meetings. Replace them with real photographs (with permission) when you have them.
- Left out from the library: the six branded composites (01 to 06), because they carry logos or invented records, and the close portraits, to avoid presenting generated faces as members.
- `cfhg-c-logo.png` / `cfhg-c-logo-light.png`: the palm logo cut out from `Logo ideas/logo 1.png`. Upload a final logo under **Customize > Site Identity** and it replaces the dark version in the header.

## Assumptions to confirm

- **Donate**: the group's documents do not mention donations. The button is kept because the brief asked for it; relabel or point it at the Family History Federation payment page.
- **Meeting time** "10am to 1pm" is a placeholder.
- **Meeting months**: the website brief says no meetings in May, August or December; the April 2026 constitution says August and December. The theme follows the website brief. Change it with the `cfhg_c_meeting_skip_months` filter.
- **Address**: Solihull Core Library, Theatre Square, Solihull B91 3RG.
- **Sub-groups** are described as meeting "between the main meetings"; their real pattern is not in the documents.
- **News posts** in the preview are written from the sitemap and the group history in the brief; they need checking by the group before they go live. The DNA and military record guidance is general.
- **Newsletter**: described as quarterly, from the Newsletter Co-ordinator role. No mailing list provider is set yet.

## Development notes

- No build step. Plain CSS in `assets/css/main.css`, plain JavaScript in `assets/js/main.js` (loaded with `defer`).
- Default copy is in `inc/content.json`. The static client preview (`npm run build:preview-c` in the project) reads the same file.
- Image dimensions for the theme's own images are in `assets/images/manifest.json`.
