# In-page navigation (`floe/in-page-nav`)

A sticky bar of links to the sections on a long page (Figma `45:345`). Place it after the banner.

| Field | Notes |
| --- | --- |
| Label | e.g. "On this page" (mono). Also the bar's accessible name. Hidden below 768px. |
| Links | **Automatic** by default: every Floe section on the page with an HTML anchor (Block settings → Advanced → HTML anchor) gets a link, labelled with its eyebrow (or heading). **By hand**: "Set links by hand" in the sidebar, then edit labels and anchors. |
| Action | Optional Primary button without an arrow. Hidden below 550px. |

**Behaviour:** sticks to the top of the window while scrolling (the header isn't sticky). The link for the section in view is highlighted (ink pill, `aria-current`). Below 768px the links scroll sideways and the active one scrolls into view. Nothing renders if there are no links.

**Components used:** Button.
