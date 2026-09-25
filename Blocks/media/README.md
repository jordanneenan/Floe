# Media (`floe/media`)

One image or silent looping MP4, as a child of **Article** and of **Images** rows. It's a shared helper, so it lives at the top level of `Blocks/`.

| Field | Notes |
| --- | --- |
| Media | Pick from the media library. Replace, remove, set a poster (MP4) or override the alt text from the overlay. |
| Caption | Article only. Small, muted, under the media. |

Inside an Images section set to "Fill", the row sets the shape and the media covers it. Otherwise it keeps its natural shape.

**Components used:** Media.
