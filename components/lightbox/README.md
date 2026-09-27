# Lightbox

Wraps an image in a link that opens it large, in a full-screen viewer. Every image in the same group becomes a slide, so visitors can step through a whole gallery. The slideshow is the [Carousel](../carousel/README.md) component. Used by the Gallery block.

```php
echo Floe\component( 'lightbox', array(
	'id'      => $image_id,
	'content' => Floe\component( 'media', array( 'id' => $image_id, 'ratio' => '4/3' ) ),
	'group'   => 'gallery-1',
) );
```

| Argument | Notes |
| --- | --- |
| `id` | Image attachment ID. Anything that isn't an image is returned unchanged, without a link. |
| `content` | What the visitor clicks, usually the Media component. |
| `group` | Images with the same group open as one slideshow, in page order. |
| `alt`, `caption` | Optional overrides. Otherwise they're read from the media library when the page renders. |
| `class` | Extra classes on the link. |

**Viewer:** a modal `<dialog>` on the Inverse surface. The image is as large as the screen allows, loaded from the image's srcset, with its caption below. A "3 / 12" count and the close button sit at the top, and the Carousel's previous/next buttons are centred at the bottom. The opened image and its neighbours load straight away; the rest load as they're reached.

**Keyboard and focus:** ← and → step through the images, Escape closes, and Tab stays inside the viewer. Focus returns to the link that opened it. The link's accessible name is "Open larger image:" followed by the image's alt text.

**Closing:** Escape, the close button, or a click beside the image. The page doesn't scroll behind the viewer.

**Without JavaScript** the link opens the full-size image file.

The viewer's markup is printed once, as a `<template>`, at the end of any page that has a lightbox link. The script (`floe-lightbox`) loads only on those pages.

**Components used:** Carousel, Icon.
