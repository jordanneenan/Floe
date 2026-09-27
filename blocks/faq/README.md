# FAQ (`floe/faq`)

**Figma:** design [43:183](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=43-183), [101:1113](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=101-1113), [102:1456](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=102-1456) · dark [91:783](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-783) · wireframe [46:1688](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=46-1688)

A list of questions (Figma `43:183`). Each question is WordPress's own **Details** block (native `<details>`/`<summary>`), so it works with the keyboard and without JavaScript. For the eyebrow, heading, intro and link (e.g. "Contact us"), put the FAQ in a **Block intro** set to Two columns (heading beside the questions), as in Figma. A stacked Block intro above the FAQ also works.

| Field | Notes |
| --- | --- |
| Questions | Details blocks: the summary is the question, the content is the answer (paragraphs or lists). Tick "Open by default" on a Details block to start it open. |
| Only one answer open at a time | Opening one question closes the others (native `name` attribute on `<details>`). |
| Add FAQ structured data | Outputs FAQPage JSON-LD from the questions and answers. |

The list is up to 760px wide with a line on top; in a two-column Block intro it fills the right-hand column. For a coloured band, put the Block intro (with the FAQ) in a **Background** block.

**Responsive:** below 768px a two-column Block intro puts the questions under its heading group.

**Components used:** Accordion (styles).
