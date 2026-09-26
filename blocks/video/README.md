# Video (`floe/video`)

A YouTube video behind a cover image (Figma `37:201`). Nothing loads from YouTube until the visitor presses play; then a `youtube-nocookie.com` player replaces the cover.

| Field | Notes |
| --- | --- |
| Eyebrow, Heading, Intro | Section header. The heading is also the video's accessible name ("Play video: <heading>"). |
| YouTube link | Sidebar. Watch, youtu.be, Shorts, live and embed links all work. Without a valid link, the cover shows with no play button. |
| Cover image | 16:9, radius 32. Without one, a dark placeholder panel shows. |
| Duration | Optional, shown under "Play video", e.g. 2:14. |
| Surface | Inverse (default), Base or Subtle. |

**Components used:** Section header, Media, Play control.
