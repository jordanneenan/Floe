# Images (`floe/images`)

**Figma:** design [37:174](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=37-174), [102:1551](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-1551) · dark [91:710](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-710) · wireframe [46:1930](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-1930)

Rows of one, two or three images or looping MP4s (Figma `37:174`). For an eyebrow and heading, add a **Block intro** above the Images, or put the Images in a Block intro set to Two columns for a narrow set of images beside the heading.

| Field | Notes |
| --- | --- |
| Rows | Add **Image row** blocks. Each row holds 1–3 **Media** blocks; the number of columns always matches the number of items. |
| Fit | **Fill** (default): each row has a set shape (one: 1248×560, two: 612×456 each, three: 4:3) and images are cropped to it. **Natural**: each image keeps its own proportions. |
| Row: gap | Per row, in the row's sidebar. Off joins the images edge to edge inside one rounded frame. |

**Responsive:** three-up rows become one-up below 550px, two-up rows stay side by side from 550px. The block renders nothing if there are no rows. For a coloured band, put the Images and their Block intro in a **Background** block.

**Components used:** Media.
