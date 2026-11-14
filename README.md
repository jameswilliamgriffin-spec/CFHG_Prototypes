# Caribbean Family History Group: website design options

Static site for client review, deployed on Vercel.

| Path | What it is |
|---|---|
| `/` (`index.html`) | Landing page linking to the three options |
| `/preview/` (or `/a`) | Option A preview |
| `/preview-b/` (or `/b`) | Option B preview |
| `/preview-c/` (or `/c`) | Option C preview |
| `/presentation.html` | The original pitch deck |

Every page is `noindex, nofollow`, and `robots.txt` blocks crawlers.

## UI polish pass

Each option had a hand-edited polish pass in its static preview: AI-tell removal, `assets/css/ui-pass.css`, `assets/js/ui-pass.js` and some HTML changes.

- **Option B (chosen by the client):** ported into the theme, `wordpress/cfhg-option-b` version 1.1.0 (see its `docs/UI-PASS.md`). `/preview-b` is now rebuilt from the theme.
- **Options A and C:** still only in their previews, each described in a `README-ui-pass.md`. The build skips a preview while that file is there, so the hand-made work can't be overwritten. The untouched originals are kept locally in `preview-backup-a`, `preview-backup-b` and `preview-backup-c` (git- and Vercel-ignored).

## Rebuilding

The previews are generated from the WordPress themes in `wordpress/`:

```bash
npm run build:site
```

Or just one option, for example B:

```bash
npm run build:site -- b
```

This rebuilds the previews, adds `site/meeting-dates.js` to every preview page (so "next meeting" dates stay current after deployment), and checks the landing page links. Commit the output; Vercel serves the files as they are, with no build step.

## Not in this repository

Client documents, the brand pack, the image library and raw image sources stay on the local machine (see `.gitignore`). The theme zips are build artefacts and are not committed either.
