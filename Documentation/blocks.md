# Block library

Every block is a folder under `Blocks/` with its own README describing its fields, variants and responsive behaviour. This page is the index and the shared contract. Figma node IDs are in the [build brief](build-brief.md#9-figma-reference).

## Sections

| Block | README | In short |
| --- | --- | --- |
| Home Banner | [home-banner](../Blocks/home-banner/README.md) | Eyebrow, Display XL H1, body, one or two actions, full-width media. |
| Page Banner | [page-banner](../Blocks/page-banner/README.md) | Breadcrumb, Display H1, intro, link, optional media, surface control. |
| In-page navigation | [in-page-nav](../Blocks/in-page-nav/README.md) | Sticky bar of section anchors (automatic or by hand), active item highlighted. |
| Article | [article](../Blocks/article/README.md) | Core blocks in a 760px reading column, optional button. |
| Image + Copy | [image-copy](../Blocks/image-copy/README.md) | Media left or right, eyebrow, heading, body, action. |
| Cards | [cards](../Blocks/cards/README.md) | Feature (numbered) or Media cards; child **Card**. |
| Posts | [posts](../Blocks/posts/README.md) | Latest, category or hand-picked posts (1–12), per-post image override. |
| Stats | [stats](../Blocks/stats/README.md) | 2–4 figures with accent units; child **Stat**. |
| Steps | [steps](../Blocks/steps/README.md) | 3–5 numbered, connected steps; child **Step**. |
| Logo strip | [logo-strip](../Blocks/logo-strip/README.md) | Label and 4–8 logos in one muted tone. |
| Testimonial | [testimonial](../Blocks/testimonial/README.md) | One long quote with optional portrait. |
| Testimonials | [testimonials](../Blocks/testimonials/README.md) | Quote cards: grid up to three, slider beyond; child **Quote card**. |
| Team | [team](../Blocks/team/README.md) | People (portrait, name, role); child **Person**. |
| Table | [table](../Blocks/table/README.md) | Core Table with Floe styling, highlighted column, ✓/— icons. |
| FAQ | [faq](../Blocks/faq/README.md) | Core Details items, one-open option, FAQPage structured data. |
| Document Download | [document-download](../Blocks/document-download/README.md) | File rows from the media library; child **Download**. |
| Images | [images](../Blocks/images/README.md) | Rows of 1–3 media (child **Image row** holding **Media**), fill or natural fit. |
| Video | [video](../Blocks/video/README.md) | YouTube cover and play control; privacy-enhanced player loads on click. |
| Contact | [contact](../Blocks/contact/README.md) | Details, optional map image, form slot. |
| Newsletter | [newsletter](../Blocks/newsletter/README.md) | Tint panel with a form slot, or a slim CTA. |
| CTA | [cta](../Blocks/cta/README.md) | Rounded Ink or Accent panel. |
| Spacing | [spacing](../Blocks/spacing/README.md) | Exact gap between two sections, per breakpoint. |

**Media** (`floe/media`, [README](../Blocks/media/README.md)) is the shared child used by Article and Image rows, so it sits at the top level of `Blocks/`.

## Contract every block follows

- **Server-rendered.** `render` in `block.json` points at `<name>.php`. Blocks with children save only `InnerBlocks.Content`; everything else saves nothing, so markup can change without block validation errors.
- **Components, not copies.** Buttons, eyebrows, section headers, media, cards, file rows and icons come from the components (`Floe\component()` in PHP, `@floe/components/*` in the editor). No block writes its own button markup or CSS.
- **Identical markup** in the editor preview and on the front end: editor twins use the same element tree and classes.
- **Headings.** Exactly one H1 per page, owned by the banners (their title falls back to the page title). Section headings default to H2 with a heading-level control; child titles are one level below their section.
- **Empty is invisible.** A section with nothing to show (no posts, no files, no quote…) renders nothing; optional parts leave no gap. Hiding media (e.g. Page Banner "Show media") removes its space even when media is selected.
- **Links** are `{ label, url, newTab }` objects edited with WordPress's link picker (page search included); a button without a URL isn't rendered.
- **Media** is stored as `{ id, posterId, alt }`. Alt text is read from the library at render time unless the slot overrides it. WordPress decides lazy or eager loading.
- **Keyboard and focus.** Every interactive element has a visible focus style; custom controls (header menu, slider, video, media pause) are real buttons with accessible names.
- **`example`** in every `block.json` for inserter previews, and a README in every folder.

## Adding a block

1. Create `Blocks/<name>/` with `block.json` (`"name": "floe/<name>"`, category `floe-sections` for a section or `floe-parts` for a child with `parent`), `<name>.php`, `<name>.scss`, `<name>-editor.js` and `README.md`. Point `editorScript`, `style` and `render` at `file:./assets/<name>-editor.js`, `file:./assets/<name>.css` and `file:./<name>.php`.
2. In PHP, open the wrapper with `Floe\block_attributes( $block, … )`; in the editor use `useFloeBlockProps( name, … )` from `@floe/editor`.
3. `npm run build`. The block appears in the inserter; nothing else needs registering.

Deleting the folder removes it completely; content that used it simply stops rendering it.
