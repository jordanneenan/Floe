# CTA (`floe/cta`)

**Figma:** design [35:75](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=35-75), [108:2893](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=108-2893), [119:1865](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=119-1865), [119:1897](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=119-1897), [119:1950](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=119-1950) · dark [91:525](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-525), [113:3563](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-3563), [122:1957](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=122-1957), [122:1975](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=122-1975), [122:2004](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=122-2004) · wireframe [46:971](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-971), [113:3193](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-3193)

One to four call-to-action panels in a row (Figma `35:75`, extended to multiple columns). Add **CTA panel** blocks inside it; the columns follow the number of panels.

| Panels | Layout |
| --- | --- |
| 1 | The wide Figma banner: copy on the left (H1-sized heading), button and note on the right. |
| 2 | Two equal columns; each panel stacks its content with the button at the bottom. |
| 3 | Three equal columns; headings drop to H3 size. |
| 4 | Four equal columns from 1280px, two by two between 768 and 1279px; headings drop to H3 size. |

Each panel has its own settings (see [cta-panel](cta-panel/README.md)). The panels keep their own colours: they're part of the design.

**Responsive:** panels stack below 768px, and a single panel's button moves under its copy.

**Components used:** Eyebrow, Button, Media (through CTA panel).
