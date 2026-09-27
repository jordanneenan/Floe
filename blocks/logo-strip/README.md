# Logo strip (`floe/logo-strip`)

**Figma:** design [43:226](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=43-226), [108:2966](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=108-2966) · dark [91:826](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-826), [113:3597](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-3597) · wireframe [46:274](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-274), [113:3275](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-3275)

A short label and four to eight logos in a row (Figma `43:226`), or any number up to 24 scrolling past as a ticker.

| Field | Notes |
| --- | --- |
| Label | e.g. "Trusted by teams at" (mono, no dot, centred). |
| Logos | Choose from the media library ("Edit logos" in the toolbar reorders, adds or removes). Up to eight in a row, up to 24 as a ticker. |
| Scroll as a ticker | Off by default. On, the logos scroll past continuously (the [Carousel](../../components/carousel/README.md) component in ticker mode), fading out at both edges. Hovering or focusing pauses it, and a small pause button appears in the top-right corner (always visible on touch screens). For anyone who prefers reduced motion, or without JavaScript, the logos sit still in a centred row that wraps. |

**One muted tone:** logos with transparency (PNG, WebP, GIF, SVG) are recoloured to muted ink at 75% with a CSS mask, whatever their original colours. Other files (e.g. JPG) are shown in greyscale. Each logo's accessible name is its alt text (or its title). Logos are up to 32px tall and 150px wide.

**Responsive:** one row spread across the width from 768px; below that the logos wrap and centre (three per row on mobile, so six logos make two rows).

**Components used:** Carousel (ticker only).
