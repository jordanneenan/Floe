# Statement (`floe/statement`)

**Figma:** design [149:2079](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=149-2079), [149:2099](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=149-2099) · dark [149:2090](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=149-2090) · wireframe [149:3750](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=149-3750)

One big line in large type: a mission, a belief, the one thing a visitor should remember. Put it between busier blocks to give the page a pause.

| Field | Notes |
| --- | --- |
| Eyebrow | Optional. |
| Statement | H1-sized text (52/56 on large desktop, scales down), up to 1080px wide, left-aligned. Bold words show in the surface's accent colour (blue on light surfaces); italic is available too. Aim for one to three sentences. |
| Button | Optional, Secondary style. Hidden until it has a label and a link. |
| Light up as you scroll | On by default (Settings). The words start faint and light up in reading order as the statement moves up the screen, and dim again if the visitor scrolls back. Turn it off for a plain statement. |

The statement is a paragraph, not a heading, so it doesn't change the page's heading outline. The block renders nothing if the statement is empty. It works on every surface: put it in a **Background** block (Tint, Inverse or Accent) to set it apart.

**Light up as you scroll.** `statement.js` wraps each word in a span on the front end (the saved markup stays plain) and sets one custom property, `--statement-fill`, as the page scrolls; `statement.scss` turns that into each word's opacity. The fill runs from the statement's top at 85% of the window to its bottom at 60%, and a statement near the end of a page that can't scroll that far still finishes fully lit. Without JavaScript, with reduced motion, and in the editor, the statement is shown in full.

**Mobile:** the same layout, with the type scaling down fluidly and a 24px gap (40px from desktop).

**Components used:** Eyebrow, Button.
