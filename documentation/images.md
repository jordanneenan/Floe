# Images

What size and shape to make images for a Floe site, how each slot crops them, and how to write alt text. It applies to people preparing images and to agents generating, choosing or placing them.

## The rule: two shapes

| Shape | Use for | Upload at |
| --- | --- | --- |
| **16:9** (wide) | Anything that runs the full content width: Home Banner, Page Banner, Images with one per row, Video cover, CTA panel, Contact map, and **post featured images** | **2560 × 1440** (at least 1920 × 1080) |
| **4:3** (standard) | Anything smaller: Image + Copy, Cards, Images with two or three per row, Media in an Article | **2000 × 1500** (at least 1600 × 1200) |

Two exceptions, because the slots are drawn that way in Figma:

| Shape | Use for | Upload at |
| --- | --- | --- |
| **4:5** (portrait) | Team portraits | 1200 × 1500 |
| **1:1** (square) | Testimonial and quote photos | 800 × 800 |

Logos aren't photos: upload a transparent PNG or an SVG, trimmed tight to the artwork (see [Logo strip](../blocks/logo-strip/README.md)).

Upload the image at its shape even when a slot crops it further. The same file then works in every slot of that family, on every screen.

## How each slot crops

Every slot except Article media has a fixed shape and fills it (`object-fit: cover`), cropping from the centre. Floe has no focal-point control, so **the centre of the image is what survives**. Measured on floe.local; the content width tops out at 1248px.

| Slot | Upload | Desktop and up | Tablet | Mobile | What's lost from the upload |
| --- | --- | --- | --- | --- | --- |
| Home Banner | 16:9 | 1248 × 600 (2.08:1) | 2.08:1 | 4:3 | Desktop 15% of the height; mobile 25% of the width |
| Page Banner | 16:9 | 1248 × 440 (2.84:1) | 2.84:1 | 4:3 | Desktop **37% of the height**; mobile 25% of the width |
| Images, 1 per row (Fill) | 16:9 | 1248 × 560 (2.23:1) | 2.23:1 | 4:3 | Desktop 20% of the height; mobile 25% of the width |
| Video cover | 16:9 | 1248 × 702 (16:9) | 16:9 | 16:9 | Nothing. The play button sits in the centre. |
| CTA panel | 16:9 | up to 352 × 198 (16:9) | 16:9 | 16:9 | Nothing |
| Contact map | 16:9 | 23:10 | 23:10 | 23:10 | 23% of the height |
| Image + Copy | 4:3 | 624 × 560 (10:9) | 10:9 | 10:9 | 16% of the width |
| Cards (media style) | 4:3 | 395 × 296 (4:3) | 4:3 | 4:3 | Nothing |
| Images, 2 or 3 per row (Fill) | 4:3 | 612 × 456 / 400 × 300 | 4:3 | 4:3 | Nothing |
| Article media | 4:3 | 860 wide, own shape | own shape | own shape | Nothing: shown uncropped |
| Posts (cards) | 16:9 featured image | 395 × 296 (4:3) | 4:3 | 4:3 | 25% of the width |
| Post banner | 16:9 featured image | as Page Banner | | | as Page Banner |
| Team person | 4:5 | 294 × 368 (4:5) | 4:5 | 4:5 | Nothing |
| Testimonial, quote | 1:1 | square | square | square | Nothing |

Images set to **Natural** fit keep their own shape and are never cropped.

### Safe area

- **16:9:** keep the subject, faces and anything that matters inside the **middle 75% of the width and middle 60% of the height**. That survives the Page Banner on desktop and every banner on mobile. Use the outer edges for sky, water, floor, wall: things that can be lost.
- **4:3:** keep the subject inside the **middle 80% of the width**, for Image + Copy.
- A post's featured image appears as a wide banner on the post and as a 4:3 card in Posts, so it follows the 16:9 safe area.

## Files

- **JPEG** for photographs. Floe re-encodes at quality 70 and generates 800, 1440 and 2400px-wide copies ([D12](decisions.md#d12-image-sizes-and-processing-mades-pipeline)), so upload a high-quality JPEG (quality 85–90) and let WordPress do the rest. WebP uploads work too.
- **PNG** only when the image needs transparency. Opaque PNGs are converted to JPEG on upload and the PNG is deleted.
- Anything over 2560px is scaled down by WordPress, so there's nothing to gain from bigger files.
- sRGB colour, no embedded text, no borders or rounded corners (slots add their own radius).
- File names describe the picture in lower case with hyphens (`studio-team-review.jpg`), because they become the URL and the default title.

## Looping MP4s

A media slot can hold a silent looping MP4 instead of an image. Make it the same shape as the image it replaces (16:9 or 4:3), 1920px wide at most, 5 to 15 seconds long, H.264, no audio track, and ideally under 4 MB. Always set a **poster** image of the same shape: it shows while the video loads and for visitors who prefer reduced motion. Nothing essential should depend on the motion.

## Alt text

Alt text is set once in the media library and read wherever the image is used ([Media](../components/media/README.md)). Describe what the picture shows and why it's there in one short sentence, without "image of". Leave it empty only for purely decorative images, such as abstract textures. A slot can override it when the same image means something different on one page.

## What makes a good Floe image

Floe blocks are visually neutral; the images and brand tokens give a site its character ([design system brief](design-system-brief.md)). For any site:

- **One family.** Pick a short style and use it for every image: the same kind of light, palette and treatment. A consistent set reads as designed; a mixed set reads as stock.
- **Match the tokens.** Choose or grade images to sit with the site's palette, and let one accent colour recur.
- **Real subjects over abstract ones.** People, places and objects hold attention; abstract textures work as a supporting layer, not the whole set.
- **Room to breathe.** Headings sit beside images, not on top of them, so images don't need empty space for text, but a calm area around the subject survives cropping better.
- **No words in the picture.** Text in an image can't be translated, read by screen readers or restyled, and it gets cropped. Screens and documents in shot should be out of focus.

### Generating images with AI

- Ask for the shape and the safe area in the prompt ("16:9 landscape, subject in the centre, edges can be cropped").
- Image models often return a fixed size (ChatGPT usually gives 1536 × 1024, which is 3:2). Crop to the exact shape before uploading: a 3:2 image loses a little top and bottom for 16:9, or a little from each side for 4:3. Upscale it to the sizes above if the tool allows.
- Keep a style paragraph and repeat it in every prompt so the set stays consistent.
- Check hands, faces, text and reflections before using an image, and never use generated people as real staff, clients or testimonial authors. Team and testimonial photos should be real.
