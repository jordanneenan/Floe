# Home Banner (`floe/home-banner`)

The home page opener (Figma `33:11`). Owns the page's H1.

| Field | Notes |
| --- | --- |
| Eyebrow | Short label above the headline. Optional. |
| Headline | H1 in Display XL. Falls back to the page title if left empty, so the page always has its H1. |
| Supporting copy | Body L, muted. Optional. |
| Primary action | Primary button with arrow. Label and link (with page search). Hidden if it has no link. |
| Second action | Optional Secondary button, no arrow. |
| Media | Image or silent looping MP4, full width, 1248×600 at 1440 (radius 32). Optional. |

**Responsive:** below 1280px the supporting copy and actions stack under the headline. Below 768px the media becomes 4:3 so it doesn't turn into a thin strip.

**Components used:** Eyebrow, Button, Media.
