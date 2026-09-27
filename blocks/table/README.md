# Table (`floe/table`)

**Figma:** design [45:255](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=45-255), [102:1359](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-1359) · dark [91:1028](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-1028) · wireframe [46:1531](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-1531)

A comparison table (Figma `45:255`). The table itself is WordPress's own **Table** block, styled by Floe. For an eyebrow, heading and intro, add a **Block intro** above the Table.

| Field | Notes |
| --- | --- |
| Table | Core Table. Turn on the header section (Table block settings) for the plan names row. The first column holds the row labels. |
| ✓ and — | A cell containing only `✓` shows a blue check icon ("Included" for screen readers); only `—` (or `–`, `-`) shows a grey dash ("Not included"). |
| Highlighted column | Sidebar, 0–6 (0 = none; 1 = the first column after the labels). Tints the whole column. |
| Badge | Optional label on the highlighted column's header, e.g. "Popular". |
| Emphasise the last row | Shows the last row's values in H4 (for prices or totals). |

For a coloured band, put the Table and its Block intro in a **Background** block.

**Responsive:** the table keeps a 640px minimum width and scrolls sideways inside its rounded frame on small screens.

**Components used:** Icon.
