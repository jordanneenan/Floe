# Page Banner (`floe/page-banner`)

The opener for inner pages (Figma `33:65`). Owns the page's H1.

| Field | Notes |
| --- | --- |
| Breadcrumb | Built automatically from the page hierarchy (core Breadcrumbs block). Switch off in the sidebar. |
| Title | H1 in Display. Falls back to the page title if left empty. |
| Intro | Body L, muted. Optional. |
| Link | Optional text link with arrow (Button, Link style). |
| Media | Optional image or looping MP4 under the title row, 1248×440 at 1440. "Show media" off hides it and removes the space completely, even if media is still selected. |
| Surface | Tint (default), Subtle, Base or Inverse, in the sidebar. |

**Responsive:** the intro and link stack under the title below 768px; media becomes 4:3.

**Components used:** Breadcrumb, Button, Media.
