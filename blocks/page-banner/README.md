# Page Banner (`floe/page-banner`)

**Figma:** design [33:65](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=33-65) · dark [91:424](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-424) · wireframe [46:254](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-254)

The opener for inner pages (Figma `33:65`). Owns the page's H1.

| Field | Notes |
| --- | --- |
| Breadcrumb | Built automatically from the page hierarchy (core Breadcrumbs block). Switch off in the sidebar. |
| Title | H1 in Display. Falls back to the page title if left empty. |
| Intro | Body L, muted. Optional. |
| Link | Optional text link with arrow (Button, Link style). |
| Media | Optional image or looping MP4 under the title row, 1248×440 at 1440. "Show media" off hides it and removes the space completely, even if media is still selected. |
| Surface | Tint (default), Subtle, Base or Inverse, in the sidebar. |

Its colour runs edge to edge and is part of its design, so it can't go in a Background block. Its space is padding inside it, top and bottom, with the usual block margin below.

**Responsive:** the intro and link stack under the title below 768px; media becomes 4:3.

**Components used:** Breadcrumb, Button, Media.
