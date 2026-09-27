# Video (`floe/video`)

A YouTube video behind a cover image (Figma `37:201`). Nothing loads from YouTube until the visitor presses play; then a `youtube-nocookie.com` player replaces the cover. For an eyebrow, heading and intro, put the Video in an **Intro** block (layout: heading above, as in Figma; two columns also works).

| Field | Notes |
| --- | --- |
| YouTube link | Sidebar. Watch, youtu.be, Shorts, live and embed links all work. Without a valid link, the cover shows with no play button. |
| Cover image | 16:9, radius 32. Without one, a dark placeholder panel shows. |
| Duration | Optional, shown under "Play video", e.g. 2:14. |
| Surface | Inverse (default), Base or Subtle. Inside an Intro, the Intro's surface is used instead (set the Intro to Inverse for the Figma look). |

The Intro's heading is also the video's accessible name ("Play video: <heading>"), passed down as block context; without an Intro heading the button reads "Play video".

**Components used:** Media, Play control.
