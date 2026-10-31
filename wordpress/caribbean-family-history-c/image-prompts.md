# Generated images: prompts and settings (Option C)

All images in `assets/images/generated/` were made with ChatGPT image generation through the Codex image tool (`mcp__codex-images__generate_image`). The tool uses OpenAI's built-in image model; it takes only a prompt and an output path, so size and aspect ratio are given in the prompt itself. No reference images were supplied.

Raw outputs are kept outside the theme in `image-sources/option-c/`. The theme ships resized, compressed copies (WebP plus a JPEG fallback, 1600px on the long side or smaller).

## Rules these follow

- Places and textures only. No people, no faces, no figures.
- No fake archive material: no old photographs, documents, records or handwriting.
- No text, lettering or logos inside the images.
- Each island is shown through its own geography or building tradition, not a generic beach and palm tree.

## One treatment for every image

Every prompt ends with the same style block, so the set reads as one series:

> Style: a screenprinted poster illustration, as if printed by hand in a few flat inks on warm off-white cotton paper. Inks: deep navy #001830, sea blue #006CB4, teal #18B4CC, palm green #0C9048 and sun gold #F0B424, with only a touch of red #E42424. Flat layered shapes, soft paper grain, slight ink misregistration, no gradients, no black outlines. Calm and observed, like a museum print. Strictly no people, no faces, no human figures, no text, no lettering, no logos, no borders or frames.

The inks are the six colours of the palm logo's ring, so the illustrations sit inside the brand palette rather than beside it.

## Why these islands

The client sitemap names three island sub-groups (Barbados, Jamaica, St Kitts and Nevis) and lists Trinidad under "Researching other countries". Montserrat comes from the members' stories list ("Ursula's story: Montserrat to Africa"). Those five are the islands the group already researches and writes about. Adding another island later only needs a new Island post with a featured image; nothing in the theme is tied to these five.

## Prompts

### Hero: `hero-hillside-bay` (portrait, 4:5)

A hillside garden high above a Caribbean bay at first light: a single tall royal palm in the foreground, a low wall of rough coral stone, a spill of bougainvillea over the wall, and beyond it a chain of islands stepping away across a calm sea towards the horizon. Portrait composition, aspect ratio 4:5, the top third mostly open pale sky. [style block]

### Jamaica: `island-jamaica-blue-mountains` (landscape, 3:2)

Morning in the Blue Mountains of Jamaica: steep ridges fading into layers of blue haze, terraced rows of coffee bushes on the near slope, a single narrow road switching back along the hillside, a small pale farm building with a corrugated tin roof, tall tree ferns, and a low cloud resting in the valley. Landscape composition, aspect ratio 3:2. [style block]

### Barbados: `island-barbados-chattel-houses` (landscape, 3:2)

A quiet roadside in rural Barbados: a row of three small wooden chattel houses with steep gabled roofs, louvred jalousie windows and painted fretwork trim, raised on loose blocks of coral stone, each painted a different soft colour; tall sugar cane behind them, a flat limestone landscape under a high afternoon sky, and a breadfruit tree casting shade on the road. Landscape composition, aspect ratio 3:2. [style block]

### St Kitts and Nevis: `island-st-kitts-nevis-narrows` (landscape, 3:2)

The view from the south-east peninsula of St Kitts across The Narrows to Nevis: the round cone of Nevis Peak wearing its cap of white cloud, a still salt pond and dry scrub hills in the foreground, a few century plants with tall flower stalks, and the strait between the two islands in bands of teal and blue. Landscape composition, aspect ratio 3:2. [style block]

### Trinidad and Tobago: `island-trinidad-caroni` (landscape, 3:2)

Caroni Swamp in Trinidad at dusk: winding mangrove channels and tangled red mangrove roots, still green-brown water, the Northern Range hills low on the horizon, and a flock of scarlet ibis flying home to roost across a gold evening sky, the birds as small red shapes. Landscape composition, aspect ratio 3:2. [style block]

### Montserrat: `island-montserrat-soufriere` (landscape, 3:2)

Montserrat seen from the green northern hills: lush forested slopes and a winding road in the foreground, and across the valley the grey ash-covered flanks of the Soufriere Hills volcano with a thin plume of steam rising from the summit, the sea a band of deep blue to one side. Landscape composition, aspect ratio 3:2. [style block]

### Texture: `texture-madras` (square, 1:1)

A close, flat, straight-on view of Caribbean madras cotton cloth: a woven plaid of gold, red, green and blue stripes crossing on an off-white ground, with the soft irregular lines of hand-woven threads. Fills the whole frame edge to edge. Square, aspect ratio 1:1. [style block, without the "museum print" line]

## Review log

Each image was opened at full size and checked for stray lettering, malformed shapes, people or faces, and anything that misrepresents the place. Results are recorded below.

| Image | Result | Notes |
|---|---|---|
| hero-hillside-bay | Kept | Clean. A thin paper strip at the top and bottom edges was trimmed. |
| island-jamaica-blue-mountains | Kept | Coffee cherries, terraces, tin-roof house and valley cloud all read correctly. |
| island-barbados-chattel-houses | Kept | Gabled chattel houses on coral-stone blocks, with cane and breadfruit. A printed border was trimmed. |
| island-st-kitts-nevis-narrows | Kept | Nevis Peak with its cloud cap across the strait; century plants in flower. A printed border was trimmed. |
| island-trinidad-caroni | Kept | Red mangrove roots and scarlet ibis read clearly; no stray marks in the sky. |
| island-montserrat-soufriere | Kept | Ash-grey volcano flank and steam plume beside the green north. A printed border was trimmed. |
| texture-madras | Kept | Even plaid with no lettering; used as a narrow strip, not as a full background. |

No image needed regenerating. None contains people, faces, figures, text or logos.

## Model settings

- Tool: `generate_image` on the Codex image server (ChatGPT / OpenAI built-in image generation).
- Inputs: prompt and output path only. No reference images, no seed (the tool does not take one).
- Output: PNG, 1536 x 1024 for landscapes, 1122 x 1402 for the portrait hero, 1254 x 1254 for the texture.
- Post-processing: border trim, crop to 3:2 or 4:5, resize to 1200px (islands), 900px (hero) or 600px (texture), plus a 640px or 560px copy for small screens, saved as WebP at quality 80.
