# Option B: image still to make

The reworked opening panel shows a photograph in a large sea-blue circle. For now it uses the existing library photograph `three-generations-with-an-album-on-the-sofa`, which fills the circle. The intended image is a cut-out portrait on a transparent background, so the person sits on the sea blue like the reference layout.

ChatGPT image generation (`mcp__codex-images__generate_image`) was at its usage limit when the panel was built, so this has not been made yet. No other image source was used.

## Prompt

> Photorealistic studio portrait photograph, portrait orientation, aspect ratio 2:3. A warm, friendly Black British woman of Caribbean heritage in her mid sixties, short natural grey hair, small gold earrings, reading glasses on a beaded chain, smiling gently and looking slightly off to the left of camera (not at the lens). She is shown from the waist up, body turned three-quarters to the left, holding a closed, plain burgundy cloth-covered photo album against her chest with both hands. She wears a navy blue cardigan over a blouse with a small gold and coral floral pattern. Soft, natural, flattering light from the left. The background is a completely plain, seamless, evenly lit flat turquoise studio backdrop (colour close to #45BCD0), with no texture, no gradient, no shadow on the backdrop and nothing else in the frame. Leave clear space above her head. She is a fictional person, not anyone recognisable. No text, no logos, no watermark, no props other than the closed album. Clothing must contain no turquoise or teal so she separates cleanly from the backdrop.

The backdrop is close to the circle's colour (CFHG B Sea, `#45BCD0`), so any fringe left after cutting her out blends into the circle.

## After generating

1. Keep the raw PNG in `image-sources/option-b/` (outside the theme).
2. Key out the turquoise backdrop to transparency.
3. Place her on a square transparent canvas, bottom-centred, with her head about 15% from the top.
4. Save as WebP with alpha at 1200px and 640px.
5. Point `hero_image` at it, either in `inc/images.php` (the theme loads `.jpg` by name, so add WebP support there and in `scripts/build-preview-b.mjs`) or by uploading it under **Customize > CFHG homepage > Opening panel > Photograph in the circle**.
