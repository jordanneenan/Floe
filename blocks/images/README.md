# Images (`floe/images`)

Rows of one, two or three images or looping MP4s (Figma `37:174`).

| Field | Notes |
| --- | --- |
| Eyebrow, Heading | Optional heading group. |
| Rows | Add **Image row** blocks. Each row holds 1–3 **Media** blocks; the number of columns always matches the number of items. |
| Fit | **Fill** (default): each row has a set shape (one: 1248×560, two: 612×456 each, three: 4:3) and images are cropped to it. **Natural**: each image keeps its own proportions. |
| Row: gap | Per row, in the row's sidebar. Off joins the images edge to edge inside one rounded frame. |

**Responsive:** three-up rows become one-up below 550px, two-up rows stay side by side from 550px. The section renders nothing if there are no rows.

**Components used:** Section header, Media.
