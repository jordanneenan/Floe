# Media

An image or a silent looping MP4 from the media library (Figma `29:38`).

| Argument | Notes |
| --- | --- |
| `id` | Attachment ID. Images use `wp_get_attachment_image()` (srcset from Floe's image sizes). `video/mp4` renders a muted looping video. |
| `poster` | Poster image ID for MP4s. |
| `alt` | Optional override. Otherwise alt text is read from the media library when the page renders, so fixing it in the library fixes it everywhere. |
| `sizes` | The `sizes` attribute for the slot, e.g. `(min-width: 1440px) 624px, (min-width: 768px) 50vw, 100vw`. |
| `ratio` | CSS aspect ratio (`'4/3'`, `'16/9'`). The media covers the box. Empty keeps the file's own ratio. |
| `radius` | `lg` (default), `xl`, `md`, `sm`, `pill` or `none`. |
| `placeholder` | Show Figma's neutral gradient when nothing is set (portrait slots). |
| `caption` | Wraps the media in a `<figure>` with a Small caption. |

**Loading:** WordPress decides eager or lazy loading and `fetchpriority`. Floe never forces `lazy`, so banner images load straight away.

**Video:** muted, looping, inline, no controls. It plays only while on screen, stays paused for visitors who prefer reduced motion, and always has a visible pause/play button (WCAG 2.2.2). The script (`media.js`) loads only on pages with a video.

Editor: `MediaSlot` in `@floe/editor` picks media and renders `Media` from `@floe/components/media`.
