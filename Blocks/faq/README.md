# FAQ (`floe/faq`)

A heading group beside a list of questions (Figma `43:183`). Each question is WordPress's own **Details** block (native `<details>`/`<summary>`), so it works with the keyboard and without JavaScript.

| Field | Notes |
| --- | --- |
| Eyebrow, Heading, Intro | Stacked heading group on the left. |
| Link | Optional text link under the intro (e.g. "Contact us"). |
| Questions | Details blocks: the summary is the question, the content is the answer (paragraphs or lists). Tick "Open by default" on a Details block to start it open. |
| Only one answer open at a time | Opening one question closes the others (native `name` attribute on `<details>`). |
| Add FAQ structured data | Outputs FAQPage JSON-LD from the questions and answers. |

**Responsive:** below 768px the questions sit under the heading group.

**Components used:** Section header, Button, Accordion (styles).
