# Article (`floe/article`)

Long-form content written with WordPress's own blocks, in an 860px reading column, left-aligned with the other sections (Figma `34:28`, widened). New posts start with an Article.

**Allowed inside:** Heading, Paragraph, List, Quote, Pullquote, Image, Media (Floe), Table, Separator, Buttons, Embed, Shortcode. Floe sections can't be nested inside.

| Styling | |
| --- | --- |
| Headings | H2/H3/H4 in the Floe type scale, 64px above (40 on mobile). |
| Paragraphs | Body, muted. Choose the **Lead** style for an opening paragraph (Body L, ink). |
| Links | Underlined in the accent colour. |
| Lists | Unordered: short blue dash markers. Ordered: blue mono numbers (01, 02…) (not drawn in Figma; follows the same language). |
| Quote / Pullquote | 3px blue rule, Quote type, mono citation. |
| Images | Rounded (24px) with a small muted caption. |
| Table | Simple ruled table that scrolls sideways on small screens. |

| Field | Notes |
| --- | --- |
| Optional button | Primary button under the content (hidden until it has a link). |
| Full width (Advanced) | Uses the whole content width (1248px) instead of the reading column. For the odd wide image, embed or shortcode that doesn't need a section of its own. |
| Maximum width (Advanced) | Any width from 320 to 1248px. Reset returns to the 860px reading column. Hidden while Full width is on. |

For a coloured band, put it in a **Background** block.

**Responsive:** the column is up to 860px wide (or the width chosen in Advanced) and always starts at the content's left edge, so headings line up with the sections above and below. The width is the `content.narrow` token in `theme.json` (`--wp--custom--content--narrow`). The block renders nothing if it's empty.

**Components used:** Button (Media via the Media block).
