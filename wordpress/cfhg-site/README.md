# CFHG Site plugin

The Caribbean Family History Group's own content, kept in a plugin rather than
the theme so it survives a change of design.

Version 1.0.0 adds **Events** and the **Island** tag. Surname entries and the
members' upload form are planned to follow.

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

### On the site

| | |
|---|---|
| `[cfhg_events]` | Upcoming events. Options: `when="upcoming"`, `"past"` or `"all"`; `limit="10"`; `group="jamaica"`; `island="barbados"`; `type="talk"`; `show_group="no"`; `empty="Text when there are none"` |
| `[cfhg_calendar_links]` | "Add all our meetings to your calendar": a `webcal://` subscription |
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
