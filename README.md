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

## UI polish pass (do not rebuild yet)

The three preview folders have had a hand-edited polish pass that is not yet in the WordPress themes: AI-tell removal, `assets/css/ui-pass.css`, `assets/js/ui-pass.js` and some HTML changes. Each preview has a `README-ui-pass.md` describing it. **Running any build script below will overwrite that work.** Port the changes into the themes first. The untouched originals are kept locally in `preview-backup-a`, `preview-backup-b` and `preview-backup-c` (git- and Vercel-ignored).

## Rebuilding

The previews are generated from the WordPress themes in `wordpress/`:

```bash
npm run build:site
```

This rebuilds all three previews, adds `site/meeting-dates.js` to every preview page (so "next meeting" dates stay current after deployment), and checks the landing page links. Commit the output; Vercel serves the files as they are, with no build step.

## Not in this repository

Client documents, the brand pack, the image library and raw image sources stay on the local machine (see `.gitignore`). The theme zips are build artefacts and are not committed either.
