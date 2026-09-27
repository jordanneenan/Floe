# Gallery (`floe/image-gallery`)

Images chosen from the media library, laid out as an even grid, a mosaic or a masonry layout. Clicking an image opens it full screen in the [Lightbox](../../components/lightbox/README.md), where visitors can step through the whole gallery. For a heading, add a **Block intro** above it, or put the gallery in a two-column Block intro.

| Field | Notes |
| --- | --- |
| Images | Choose several at once from the media library. **Edit images** in the block toolbar adds, removes or reorders them. Alt text and captions come from the media library. |
| Layout | **Grid**: even tiles cropped to 4:3. **Mosaic**: a large image beside two small ones, with the large one swapping sides each time (images 1, 4, 7… are the large ones). **Masonry**: each image at its own proportions, in columns. |
| Columns | 2, 3 (default) or 4 from 768px wide, for Grid and Masonry. |
| Open images in a lightbox | On by default. Off, the images are just pictures. |

**Lightbox:** full screen on the Inverse surface, with the caption under the image, a "3 / 12" count, previous/next buttons, the arrow keys, swipe, and Escape or a click beside the image to close.

**Responsive:** Grid and Masonry are two across below 768px. The mosaic is two across there too, with each large image spanning the full width.

**Empty is invisible:** the block renders nothing without images.

For a coloured band, put it in a **Background** block.

**Components used:** Media, Lightbox (and Carousel through it).
