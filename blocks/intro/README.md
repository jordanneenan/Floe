# Intro (`floe/intro`)

The eyebrow, heading, intro and button for the block it introduces. Add an Intro, choose a layout, then add the block into its slot. Blocks such as Cards, Posts and FAQ don't have headings of their own: their heading group is always an Intro.

| Field | Notes |
| --- | --- |
| Eyebrow, Heading, Intro | Edited in place. The heading is H2 by default (sidebar: Heading level). Titles inside the held block (cards, steps, people, posts) are one level below it. |
| Button | Optional. A Secondary button with the heading above, a text link with two columns. |
| Layout | **Heading above the block**: heading and eyebrow on the left, intro and button on the right, the block below (Figma: Cards, Posts, Stats, Steps, Team, Table, Testimonials). **Two columns**: the heading group in a 400px column on the left, the block in a 760px column on the right (Figma: FAQ, Document Download). |
| Block | One block, chosen from those that suit the layout. |
| Surface | Base (default), Subtle, Tint or Inverse. The held block takes the Intro's surface and spacing. |
| HTML anchor | Block settings → Advanced. In-page navigation links to the Intro, labelled with its eyebrow (or heading). |

**Which blocks fit.** A block opts in through `"supports": { "floeIntro": [ "above", "beside" ] }` in its `block.json`, so the Intro never names other blocks and a deleted block simply stops being offered. The first layout listed is the default when a block is turned into an Intro (block toolbar → Transform to → Intro), so FAQ lists `"beside"` first. Two columns is limited to blocks that work in a narrow column (FAQ, Document Download, Images, Video); banners, navigation and blocks with their own composed heading (Image + Copy, CTA, Contact, Newsletter, Testimonial, Logo strip) don't go in an Intro at all.

**Empty is invisible.** An Intro with no block is a heading on its own. If its block renders nothing (no posts, no files), the Intro renders nothing either.

**Responsive:** two columns stack below 768px, heading first.

**How holding works.** The Intro passes `floe/nested` (its layout), `floe/headingLevel` and `floe/heading` as block context (Video uses the heading to name its play button). A held block that uses `floe/nested` gets neither `floe-section` nor a surface class from `Floe\block_attributes()` (and `useFloeBlockProps()` in the editor), and `.intro__block` zeroes `--floe-section-space` and the content gutter, so the block's own padding and container collapse into the Intro's.

**Components used:** Section header, Button.
