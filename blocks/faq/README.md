# FAQ (`floe/faq`)

A list of questions (Figma `43:183`). Each question is WordPress's own **Details** block (native `<details>`/`<summary>`), so it works with the keyboard and without JavaScript. For the eyebrow, heading, intro and link (e.g. "Contact us"), put the FAQ in an **Intro** block with the two-column layout (heading beside the block), as in Figma.

| Field | Notes |
| --- | --- |
| Questions | Details blocks: the summary is the question, the content is the answer (paragraphs or lists). Tick "Open by default" on a Details block to start it open. |
| Only one answer open at a time | Opening one question closes the others (native `name` attribute on `<details>`). |
| Add FAQ structured data | Outputs FAQPage JSON-LD from the questions and answers. |

The list is up to 760px wide with a line on top; beside an Intro heading it fills the right-hand column. The section is on the Base surface; inside an Intro, the Intro's surface is used instead.

**Responsive:** below 768px the Intro puts the questions under its heading group.

**Components used:** Accordion (styles).
