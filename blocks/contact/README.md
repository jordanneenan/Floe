# Contact (`floe/contact`)

Contact details beside a form (Figma `44:263`).

| Field | Notes |
| --- | --- |
| Eyebrow, Heading, Intro | Stacked heading group. |
| Email | Optional. Linked (`mailto:`), in the accent colour. Obfuscated against simple spam bots. |
| Phone | Linked (`tel:`). |
| Address | The label is editable (Figma uses "Studio"; default "Address"). Line breaks allowed. |
| Hours | Line breaks allowed. |
| Map | Optional image (23:10), e.g. a static map. |
| Form slot | Floe's **Form** block (Enquiry form), or a form plugin's block or Shortcode block. It sits in a grey rounded panel and picks up the Form component's field styles. |

Empty rows are left out; with an empty form slot the panel isn't shown.

**Responsive:** the form panel moves under the details below 768px.

**Components used:** Section header, Media, Form (styles), Button (submit styling).
