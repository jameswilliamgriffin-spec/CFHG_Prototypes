# CFHG Site plugin

The Caribbean Family History Group's own content, kept in a plugin rather than
the theme so it survives a change of design.

Version 1.2.0 has **Events**, the **Island** tag, **Surname entries** and
**island guides** (the parish explorer, "Where should I look?" and the
records timeline), plus the **surname ribbon** for the homepage. The
members' upload form is planned to follow.

## Install

Upload `cfhg-site.zip` in Plugins > Add New > Upload Plugin, then activate. On
activation it adds the starting groups, event types and islands, and refreshes
the site's addresses. Deactivating keeps every event.

## For volunteers

| To | Go to |
|---|---|
| Add one event | Events > Add event: a name, then **Event details** (date, times, where), an optional short description, and leave the write-up empty |
| Add the year's meetings | Events > **Add a series**: pick the group and a pattern ("last Saturday of each month", skipping May, August and December) or paste in a list of dates, then **Show me the dates**, untick any that are wrong, and **Create these events** |
| Move a meeting | Open the event and change its date |
| Cancel a meeting | Open the event and tick **This event is cancelled**. It stays on What's On marked Cancelled, calendar subscribers see it cancelled, and the homepage moves on to the next meeting |
| Say what happened | After the event, open it and write a few paragraphs (and add photos) in the write-up. It then shows "Read what happened" on What's On |
| See what's coming | Events > All events > **Upcoming** (or **Past**) |

Never paste a Zoom link into an event: everything there is public. The usual
wording ("Email us and we'll send you the link") is used instead.

| To | Go to |
|---|---|
| Add surnames a member is researching | Surnames > Add surnames: the names (several spellings with a slash, e.g. Comrie / Comerie), tick the island, type the parish or town, and in **Researched by (private)** the member's name and email, and tick that they agreed to be put in touch |
| Answer "who's researching Grant?" | Surnames > All surnames, search for the name: the **Researched by** column says who |

Only the surnames, island and parish ever appear on the site. The member's
name, email and consent stay in the admin.

## How it works

- **Events** (`cfhg_event`) are at `/whats-on/event-name-date/`. Their fields
  are post meta: `_cfhg_date`, `_cfhg_start`, `_cfhg_end`, `_cfhg_attend`
  (library, online, both or elsewhere), `_cfhg_venue`, `_cfhg_online` and
  `_cfhg_status`. On save it works out `_cfhg_sort` (start) and
  `_cfhg_ends_at` (finish), both `Y-m-d H:i` in the site's timezone; with no
  finish time a meeting is taken to last two hours. An event counts as
  upcoming until it has finished.
- **Groups** (`cfhg_event_group`): Main group and the Jamaica, Barbados, and
  St Kitts and Nevis sub-groups. **Events > Settings** chooses which group is
  shown as "Next meeting".
- **Event types** (`cfhg_event_type`): meeting, talk, exhibition, family
  history fair, social.
- **Islands** (`cfhg_island`) are shared by events and posts, at
  `/island/jamaica/`, so island pages can show everything about an island.
  The starting list matches the old site's island categories.

- **Surnames** (`cfhg_surname`) are not public pages. The title is the
  surnames; `_cfhg_parish` is the parish or town as typed; the private fields
  `_cfhg_member_name`, `_cfhg_member_email` and `_cfhg_consent` are
  registered with `show_in_rest` off and are never printed. The 24 entries
  from the old site's Surname Interest page were imported in November 2026.
- **Island guides** are files: `data/islands/{island}.json` holds the
  introduction, the parishes (with their main town, the old parishes merged
  into them in 1867, and other spellings), the record sources, and the rules
  for "Where should I look?". A surname entry's parish text is matched to a
  parish by any of those names, so "Lucea" and "Trelawney" land in Hanover
  and Trelawny. `"draft": true` shows "The group is checking these details";
  once checked, set it to false and put the date in `"checked"`.
- **Parish maps** are optional: `data/maps/{island}.svg`, with one shape per
  parish carrying `data-parish-path="{parish slug}"`. Jamaica's map was made
  from geoBoundaries' Jamaica ADM1 boundaries (from OpenStreetMap, licensed
  CC BY-SA 2.0), simplified, and must keep its credit line (`"map_credit"`
  in the island file). The map is CC BY-SA 2.0 too.

### Adding the next island

1. Copy `data/islands/jamaica.json` to, say, `barbados.json` and rewrite it:
   the parishes, the sources, and the finder's records and dates. Keep
   `"draft": true` until the group has checked it.
2. For a map, get the island's parish boundaries (geoBoundaries ADM1 is a
   good source), convert them to an SVG with one path per parish using the
   same `data-parish-path` slugs, and save it as `data/maps/barbados.svg`.
3. In WordPress, Appearance > CFHG Setup > Create an island page.

### On the site

| | |
|---|---|
| `[cfhg_events]` | Upcoming events. Options: `when="upcoming"`, `"past"` or `"all"`; `limit="10"`; `group="jamaica"`; `island="barbados"`; `type="talk"`; `show_group="no"`; `empty="Text when there are none"`, or `empty="none"` to show nothing at all |
| `[cfhg_calendar_links]` | "Add all our meetings to your calendar": a `webcal://` subscription |
| `[cfhg_surnames]` | Surnames being researched, with a search box. `island="jamaica"` for one island; `search="no"` to hide the box |
| `[cfhg_parish_map island="jamaica"]` | The parish explorer: the map (if there is one), a button per parish, and each parish's details and surnames |
| `[cfhg_record_finder island="jamaica"]` | "Where should I look?": suggestions in order, the old parish names to search, and whether a member researches the surname typed |
| `[cfhg_record_timeline island="jamaica"]` | "What survives, and from when": one bar per record set in the island's finder list, from 1650 to today, coloured by what it is used for, with the island's key years marked (`"timeline"` in the island file: a year and a sentence each) |
| `[cfhg_surname_ribbon]` | A slow band of the surnames members are tracing, in capitals, first spelling of each entry. `island="jamaica"` for one island; `label="…"` for the words at the start; `url="/research/"` to make those words a link. Needs six names or more. It stands still for anyone who prefers reduced motion |
| `[cfhg_island_sources island="jamaica"]` | The island's main record sources as cards |
| `[cfhg_island_posts island="jamaica"]` | Posts tagged with the island. `limit="4"`, `empty="Text when there are none"` |
| `/?cfhg_ics=123` | One event as a calendar file ("Add to my calendar") |
| `/?cfhg_ics=feed` | Every upcoming event plus the last six, for calendar apps. Cancelled events are sent as cancelled. Times are in UTC so every app reads them correctly |
| `cfhg_next_meeting_info` filter | Gives the CFHG theme's "Next meeting" (opening panel, meeting card, menu, footer) the real next event. With no upcoming events for that group, the theme's own pattern from the Customizer is used |
| Event pages | Carry schema.org Event data, so search engines can show the meeting |

### Functions

`cfhg_site_events( $args )`, `cfhg_site_next_event( $group )`,
`cfhg_site_event( $post )` (everything about one event, ready to show) and
`cfhg_site_create_event( $data )`.

## Before going live

If the host caches whole pages, set the cache to expire at least daily, or
the homepage could show a meeting for a while after it has passed.
