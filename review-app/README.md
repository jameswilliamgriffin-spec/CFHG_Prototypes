# CFHG website review app

This app displays the main site pages and five island research guides from `../wp-export` beside per-block feedback. Story posts, event details, and policy pages are omitted from review navigation. Reviewers choose pages from a dropdown and mark each page reviewed; progress is saved per reviewer and shown in the dropdown. It is a separate Vercel project; the public site remains deployed from the repository root.

## Local preview

From this directory, run `npm run build` then `npm run dev`. Open `http://127.0.0.1:3108/`. The local-only access code is `local-review`. Local notes and review progress are stored in ignored `.local-feedback.json` and `.local-progress.json` files.

## Vercel setup

Create a second Vercel project from this repository with Root Directory `review-app`. Keep **Include source files outside of the Root Directory in the Build Step** enabled so the build can read `../wp-export`. Vercel reads `review-app/vercel.json` and runs `npm run build`.

Set `REVIEW_ACCESS_CODE` to a strong private code in the Vercel project's environment variables. Connect the existing Upstash Redis store to this project, which should supply `KV_REST_API_URL` and `KV_REST_API_TOKEN`. The app also accepts `UPSTASH_REDIS_REST_URL` and `UPSTASH_REDIS_REST_TOKEN`. These values must stay server-side; never put them in browser JavaScript.

All review data uses the `cfhg-review:v1:` key prefix. The access code protects the API; reviewers enter their names once in the browser. The export link downloads all saved notes as JSON.
