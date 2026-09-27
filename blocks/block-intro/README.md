# Block intro (`floe/block-intro`)

**Figma:** design [100:1117](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=100-1117), [100:1089](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=100-1089) · dark [102:2705](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-2705) · wireframe [103:28](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=103-28)

The eyebrow, heading, intro and button that introduce a block. Blocks such as Cards, Posts and FAQ don't have headings of their own: their heading is a Block intro.

| Field | Notes |
| --- | --- |
| Eyebrow, Heading, Intro | Edited in place. The heading is H2 by default (sidebar: Heading level). |
| Button | Optional. A Secondary button when stacked, a text link in two columns. |
| Layout | **Stacked** (default): heading and eyebrow on the left, intro and button on the right; add the block it introduces as the next block (Figma: Cards, Posts, Stats, Steps, Team, Table, Testimonials). **Two columns**: the heading group in a 400px column on the left and one block in a 760px column on the right (Figma: FAQ, Document Download). |
| HTML anchor | Block settings → Advanced. In-page navigation links to it, labelled with its eyebrow (or heading). |

**Spacing.** Stacked, it's followed by a smaller gap than other blocks (64px on desktop, 40px below 768px), so the heading sits close to its block. A Spacing block after it replaces that gap. In two columns it has the normal block spacing below.

**Switching layout** carries the block along: choosing Two columns moves the block below into the right-hand column (if it suits a column), and choosing Stacked puts it back below. A block that suits the column can also be wrapped in one from the block toolbar (Transform to → Block intro).

**Which blocks go in two columns.** A block opts in with `"supports": { "floeBlockIntro": true }` in its `block.json` (FAQ, Document Download, Images, Video), so the Block intro never names other blocks and a deleted block simply stops being offered. The held block gets the `floe/nested` context: `Floe\block_attributes()` (and `useFloeBlockProps()` in the editor) then leaves out its `floe-section` class, so it takes the Block intro's spacing, and `.block-intro__block` zeroes its gutter. The held block's titles are one level below the heading (`floe/headingLevel`), and Video names its play button after the heading (`floe/heading`).

**Empty is invisible.** Holding a block that renders nothing (no files, or hidden from this visitor), the Block intro renders nothing. Stacked, it can't know what follows, so a heading whose block shows nothing stays; you'll see it in the editor.

**Backgrounds.** It has none of its own: put it and its block in a **Background** block for a coloured band.

**Components used:** Section header, Button.
