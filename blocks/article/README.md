# Article (`floe/article`)

Long-form content written with WordPress's own blocks, in an 860px reading column, left-aligned with the other sections (Figma `34:28`, widened). New posts start with an Article.

**Allowed inside:** Heading, Paragraph, List, Quote, Pullquote, Image, Media (Floe), Table, Separator, Buttons, Embed. Floe sections can't be nested inside.

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
| Surface | Base (default) or Subtle. |

**Responsive:** the column is up to 860px wide and always starts at the content's left edge, so headings line up with the sections above and below. The width is the `content.narrow` token in `theme.json` (`--wp--custom--content--narrow`). The section renders nothing if it's empty.

**Components used:** Button (Media via the Media block).
