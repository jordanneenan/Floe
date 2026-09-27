# Background (`floe/background`)

**Figma:** design [102:1061](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-1061) · dark [102:2734](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-2734) · wireframe [103:41](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=103-41)

A full-width band of colour behind the blocks you put inside it, for example a Block intro and its Cards on grey, or a Video on ink. Blocks have no background colours of their own; banners and panels (Page Banner, CTA panels, the Newsletter panel) keep theirs because the colour is part of their design.

| Field | Notes |
| --- | --- |
| Surface | Subtle (grey, default), Tint (ice blue), Inverse (ink) or Accent (blue). Dark mode has its own version of each. |
| Force light text | Text, eyebrows, links, lines and buttons already switch to their light versions on Inverse and Accent. This forces them light on any colour (class `force-light-text`), for a client colour that needs it. |
| Auto spacing | On by default: the band has the section space above its first block, and its last block's normal margin gives the same space below. Turn it off and the blocks touch the band's edges; add Spacing blocks at the top and bottom inside to set the space yourself. |
| Blocks | Any Floe section except those that opt out with `"supports": { "floeBackground": false }`: the banners, In-page navigation and Background itself. |

Like every block, it has the section margin below it, so two bands in a row have white between them; put a Spacing block set to None between them to join them. It renders nothing when empty.

**Components used:** none (surfaces come from base.css).
