# Table (`floe/table`)

A comparison table (Figma `45:255`). The table itself is WordPress's own **Table** block, styled by Floe. For an eyebrow, heading and intro above it, put the Table in an **Intro** block (layout: heading above).

| Field | Notes |
| --- | --- |
| Table | Core Table. Turn on the header section (Table block settings) for the plan names row. The first column holds the row labels. |
| ✓ and — | A cell containing only `✓` shows a blue check icon ("Included" for screen readers); only `—` (or `–`, `-`) shows a grey dash ("Not included"). |
| Highlighted column | Sidebar, 0–6 (0 = none; 1 = the first column after the labels). Tints the whole column. |
| Badge | Optional label on the highlighted column's header, e.g. "Popular". |
| Emphasise the last row | Shows the last row's values in H4 (for prices or totals). |

The section is on the Base surface; inside an Intro, the Intro's surface is used instead.

**Responsive:** the table keeps a 640px minimum width and scrolls sideways inside its rounded frame on small screens.

**Components used:** Icon.
