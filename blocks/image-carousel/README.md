# Image carousel (`floe/image-carousel`)

Large images or looping MP4s shown one at a time, each with an optional caption. For a heading, add a **Block intro** above it, or put the carousel in a two-column Block intro.

| Field | Notes |
| --- | --- |
| Slides | **Slide** blocks, each with one image or MP4 and an optional caption. **Add images** in the block toolbar picks several from the media library at once and adds a Slide for each. Reorder slides with the block movers or List View. |
| Shape | Wide (16:9), Landscape (3:2, the default), Classic (4:3) or Square (1:1). Every slide is cropped to it, so the carousel keeps one height as it moves. |

**Behaviour:** the [Carousel](../../components/carousel/README.md) component. Previous/next buttons sit below the slides on the right and disable at either end. The row can also be swiped, scrolled with a trackpad, or stepped with the arrow keys once focused. Each slide is announced as "2 of 6". With a single slide there are no buttons.

**Responsive:** one slide across the content width from 768px. Below that a sliver of the next slide shows, so the row reads as swipeable.

**Empty is invisible:** slides without media are skipped, and the block renders nothing without any.

In the editor the slides sit in a row that scrolls sideways; the buttons are a preview only.

For a coloured band, put it in a **Background** block.

**Components used:** Carousel, Media.
