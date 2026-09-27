# Accordion

**Figma:** design [42:196](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=42-196) · dark [91:755](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-755)

Styles only: the core Details block as the Floe accordion item (Figma `42:196`). Native `<details>`/`<summary>`, so it works without JavaScript and the summary is the keyboard control.

- Question: H4, ink. Answer: Body, muted, 680px measure, 14px below the question.
- 28px vertical padding, 1px line underneath.
- The round toggle is drawn with CSS on the summary (decorative): a canvas circle with a plus when closed, a blue circle with a minus when open.

Used by the FAQ block.
