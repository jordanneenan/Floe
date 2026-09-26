# Block library

Every block is a folder under `blocks/` with its own README describing its fields, variants and responsive behaviour. This page is the index and the shared contract. Figma node IDs are in the [build brief](build-brief.md#9-figma-reference).

## Sections

| Block | README | In short |
| --- | --- | --- |
| Home Banner | [home-banner](../blocks/home-banner/README.md) | Eyebrow, Display XL H1, body, one or two actions, full-width media. |
| Page Banner | [page-banner](../blocks/page-banner/README.md) | Breadcrumb, Display H1, intro, link, optional media, surface control. |
| In-page navigation | [in-page-nav](../blocks/in-page-nav/README.md) | Sticky bar of section anchors (automatic or by hand), active item highlighted. |
| Article | [article](../blocks/article/README.md) | Core blocks in an 860px left-aligned reading column, optional button. |
| Image + Copy | [image-copy](../blocks/image-copy/README.md) | Media left or right, eyebrow, heading, body, action. |
| Cards | [cards](../blocks/cards/README.md) | Feature (numbered) or Media cards; child **Card**. |
| Posts | [posts](../blocks/posts/README.md) | Latest of any post type (a number or all, load more by button or scroll, instant filters), hand-picked posts, or manual entries; child **Post card**. |
| Stats | [stats](../blocks/stats/README.md) | 2–4 figures with accent units; child **Stat**. |
| Steps | [steps](../blocks/steps/README.md) | 3–5 numbered, connected steps; child **Step**. |
| Logo strip | [logo-strip](../blocks/logo-strip/README.md) | Label and 4–8 logos in one muted tone. |
| Testimonial | [testimonial](../blocks/testimonial/README.md) | One long quote with optional portrait. |
| Testimonials | [testimonials](../blocks/testimonials/README.md) | Quote cards: grid up to three, slider beyond; child **Quote card**. |
| Team | [team](../blocks/team/README.md) | People (portrait, name, role); child **Person**. |
| Table | [table](../blocks/table/README.md) | Core Table with Floe styling, highlighted column, ✓/— icons. |
| FAQ | [faq](../blocks/faq/README.md) | Core Details items, one-open option, FAQPage structured data. |
| Document Download | [document-download](../blocks/document-download/README.md) | File rows from the media library; child **Download**. |
| Images | [images](../blocks/images/README.md) | Rows of 1–3 media (child **Image row** holding **Media**), fill or natural fit. |
| Video | [video](../blocks/video/README.md) | YouTube cover and play control; privacy-enhanced player loads on click. |
| Contact | [contact](../blocks/contact/README.md) | Details, optional map image, form slot (child **Form**, or a form plugin). |
| Newsletter | [newsletter](../blocks/newsletter/README.md) | Tint panel with a form slot (child **Form**, or a mailing provider), or a slim CTA. |
| CTA | [cta](../blocks/cta/README.md) | One to three panels side by side (Ink, Accent, Tint or Subtle); child **CTA panel**. |
| Spacing | [spacing](../blocks/spacing/README.md) | Exact gap between two sections, per breakpoint. |

**Form** (`floe/form`, [README](../blocks/form/README.md)) is Floe's built-in enquiry form and newsletter signup. It's a child of both Contact and Newsletter, so it sits at the top level of `blocks/`.

**Media** (`floe/media`, [README](../blocks/media/README.md)) is the shared child used by Article and Image rows, so it sits at the top level of `blocks/`.

## Contract every block follows

- **Server-rendered.** `render` in `block.json` points at `<name>.php`. Blocks with children save only `InnerBlocks.Content`; everything else saves nothing, so markup can change without block validation errors.
- **Components, not copies.** Buttons, eyebrows, section headers, media, cards, file rows and icons come from the components (`Floe\component()` in PHP, `@floe/components/*` in the editor). No block writes its own button markup or CSS.
- **Identical markup** in the editor preview and on the front end: editor twins use the same element tree and classes.
- **Headings.** Exactly one H1 per page, owned by the banners (their title falls back to the page title). Section headings default to H2 with a heading-level control; child titles are one level below their section.
- **Empty is invisible.** A section with nothing to show (no posts, no files, no quote…) renders nothing; optional parts leave no gap. Hiding media (e.g. Page Banner "Show media") removes its space even when media is selected.
- **Links** are `{ label, url, newTab }` objects edited with WordPress's link picker (page search included); a button without a URL isn't rendered.
- **Media** is stored as `{ id, posterId, alt }`. Alt text is read from the library at render time unless the slot overrides it. WordPress decides lazy or eager loading. Each slot's shape and crop, and the sizes to upload, are in [Images](images.md).
- **Keyboard and focus.** Every interactive element has a visible focus style; custom controls (header menu, slider, video, media pause) are real buttons with accessible names.
- **`example`** in every `block.json` for inserter previews, and a README in every folder.

## Adding a block

1. Create `blocks/<name>/` with `block.json` (`"name": "floe/<name>"`, category `floe-sections` for a section or `floe-parts` for a child with `parent`), `<name>.php`, `<name>.scss`, `<name>-editor.js` and `README.md`. Point `editorScript`, `style` and `render` at `file:./assets/<name>-editor.js`, `file:./assets/<name>.css` and `file:./<name>.php`.
2. In PHP, open the wrapper with `Floe\block_attributes( $block, … )`; in the editor use `useFloeBlockProps( name, … )` from `@floe/editor`.
3. If the block needs server code (a REST route, shared query helpers), put it in `<name>-server.php` in the same folder; it's loaded only while the block is enabled.
4. `npm run build`. The block appears in the inserter; nothing else needs registering.

Deleting the folder removes it completely; content that used it simply stops rendering it.
