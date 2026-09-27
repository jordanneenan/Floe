# Video (`floe/video`)

**Figma:** design [37:201](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=37-201), [102:1581](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-1581) · dark [91:731](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-731) · wireframe [46:1991](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-1991)

A YouTube video behind a cover image (Figma `37:201`). Nothing loads from YouTube until the visitor presses play; then a `youtube-nocookie.com` player replaces the cover. For an eyebrow, heading and intro, add a **Block intro** above the Video (as in Figma), or put the Video in a Block intro set to Two columns.

| Field | Notes |
| --- | --- |
| YouTube link | Sidebar. Watch, youtu.be, Shorts, live and embed links all work. Without a valid link, the cover shows with no play button. |
| Cover image | 16:9, radius 32. Without one, a dark placeholder panel shows. |
| Duration | Optional, shown under "Play video", e.g. 2:14. |

For the Figma look, put the Video and its Block intro in a **Background** block set to Inverse.

In a two-column Block intro, the Block intro's heading is also the video's accessible name ("Play video: <heading>"), passed down as block context. Otherwise the button reads "Play video".

**Components used:** Media, Play control.
