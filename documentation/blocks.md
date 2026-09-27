# Block library

Every block is a folder under `blocks/` with its own README describing its fields, variants and responsive behaviour. This page is the index and the shared contract. Figma node IDs are in the [build brief](build-brief.md#9-figma-reference).

## Sections

| Block | README | In short |
| --- | --- | --- |
| Home Banner | [home-banner](../blocks/home-banner/README.md) | Eyebrow, Display XL H1, body, one or two actions, full-width media. |
| Page Banner | [page-banner](../blocks/page-banner/README.md) | Breadcrumb, Display H1, intro, link, optional media, surface control. |
| In-page navigation | [in-page-nav](../blocks/in-page-nav/README.md) | Sticky bar of section anchors (automatic or by hand), active item highlighted. |
| Block intro | [block-intro](../blocks/block-intro/README.md) | Eyebrow, heading, intro and button for the block below it (stacked), or beside one block in two columns. |
| Background | [background](../blocks/background/README.md) | Full-width band of colour (Subtle, Tint, Inverse or Accent) behind the blocks inside it; Force light text, Auto spacing. |
| Article | [article](../blocks/article/README.md) | Core blocks (Shortcode included) in an 860px left-aligned reading column, optional button; width or full width in Advanced. |
| Image + Copy | [image-copy](../blocks/image-copy/README.md) | Media left, or right with Switch side; eyebrow, heading, body, action. |
| Cards | [cards](../blocks/cards/README.md) | Feature (numbered), Icon or Media cards, 2–4 across; child **Card**. Also in the inserter as **Icon grid**. Block intro above. |
| Posts | [posts](../blocks/posts/README.md) | Latest of any post type (a number or all, load more by button or scroll, instant filters), hand-picked posts, or manual entries; child **Post card**. Block intro above. |
| Stats | [stats](../blocks/stats/README.md) | 2–4 figures with optional prefixes and accent units; child **Stat**. Block intro above. |
| Steps | [steps](../blocks/steps/README.md) | 3–5 numbered, connected steps; child **Step**. Block intro above. |
| Logo strip | [logo-strip](../blocks/logo-strip/README.md) | Label and 4–8 logos in one muted tone, or up to 24 scrolling past as a ticker. |
| Testimonial | [testimonial](../blocks/testimonial/README.md) | One long quote with optional portrait. |
| Testimonials | [testimonials](../blocks/testimonials/README.md) | Quote cards: grid up to three, slider beyond; child **Quote card**. Block intro above. |
| Team | [team](../blocks/team/README.md) | People (portrait, name, role); child **Person**. Block intro above. |
| Table | [table](../blocks/table/README.md) | Core Table with Floe styling, highlighted column, ✓/— icons. Block intro above. |
| FAQ | [faq](../blocks/faq/README.md) | Core Details items, one-open option, FAQPage structured data. In a two-column Block intro. |
| Document Download | [document-download](../blocks/document-download/README.md) | File rows from the media library; child **Download**. In a two-column Block intro. |
| Image carousel | [image-carousel](../blocks/image-carousel/README.md) | Images or MP4s one at a time with captions, previous/next buttons, swipe; child **Slide**. Optional Block intro above, or in two columns. |
| Gallery | [image-gallery](../blocks/image-gallery/README.md) | Images from the library as a grid, mosaic or masonry; each opens in a full-screen lightbox slideshow. Optional Block intro above, or in two columns. |
| Images | [images](../blocks/images/README.md) | Rows of 1–3 media (child **Image row** holding **Media**), fill or natural fit. Optional Block intro above, or in two columns. |
| Video | [video](../blocks/video/README.md) | YouTube cover and play control; privacy-enhanced player loads on click. Block intro above, or in two columns. |
| Contact | [contact](../blocks/contact/README.md) | Details, optional map image, form slot (child **Form**, or a form plugin). |
| Newsletter | [newsletter](../blocks/newsletter/README.md) | Tint panel with a form slot (child **Form**, or a mailing provider), or a slim CTA. |
| CTA | [cta](../blocks/cta/README.md) | One to four panels side by side (Ink, Accent, Tint or Subtle); child **CTA panel**. |
| Spacing | [spacing](../blocks/spacing/README.md) | Exact gap below a block, per breakpoint, in place of its margin. |

**Form** (`floe/form`, [README](../blocks/form/README.md)) is Floe's built-in enquiry form and newsletter signup. It's a child of both Contact and Newsletter, so it sits at the top level of `blocks/`.

**Media** (`floe/media`, [README](../blocks/media/README.md)) is the shared child used by Article and Image rows, so it sits at the top level of `blocks/`.

## Contract every block follows

- **Server-rendered.** `render` in `block.json` points at `<name>.php`. Blocks with children save only `InnerBlocks.Content`; everything else saves nothing, so markup can change without block validation errors.
- **Components, not copies.** Buttons, eyebrows, section headers, media, cards, file rows and icons come from the components (`Floe\component()` in PHP, `@floe/components/*` in the editor). No block writes its own button markup or CSS.
- **Identical markup** in the editor preview and on the front end: editor twins use the same element tree and classes.
- **Headings.** Exactly one H1 per page, owned by the banners (their title falls back to the page title). A section's heading group (eyebrow, heading, intro, button) is a **Block intro**: stacked above the section it introduces, or in two columns holding it, so every heading group is edited and laid out the same way. Blocks whose heading is part of their own design (banners, Image + Copy, CTA, Contact, Newsletter) keep theirs. Headings default to H2 with a heading-level control. Child titles (Cards, Posts, Steps, Team) read the `floe/headingLevel` context, default 2, plus one, so they're H3; only a block held in a two-column Block intro gets a different level from it.
- **Nesting.** A block that works in the 760px right-hand column of a two-column Block intro declares `"supports": { "floeBlockIntro": true }` and `"usesContext": [ "floe/nested" ]`, and passes `context` to `useFloeBlockProps()`. Held, it drops `floe-section` (so its margin) and its gutter, and takes the Block intro's spacing. A stacked Block intro holds nothing: the block it introduces is simply the next block. Sections go only at the top level or in a Floe block's slot, never inside a core block such as Details or Quote ([D67](decisions.md#d67-floe-sections-stay-out-of-core-blocks)).
- **Spacing and backgrounds.** Apart from the banners, sections have no vertical padding or background colour of their own. `.floe-section` (base.css) gives each a bottom margin of `--floe-section-space`, and a Spacing block replaces the margin of the section above it. For colour, editors put sections in a **Background** block; a section that can't go in one declares `"supports": { "floeBackground": false }` (the banners, In-page navigation, Background). Page Banner, CTA panels and the Newsletter panel keep their own colours as part of their design.
- **Empty is invisible.** A section with nothing to show (no posts, no files, no quote…) renders nothing; optional parts leave no gap. Hiding media (e.g. Page Banner "Show media") removes its space even when media is selected.
- **Links** are `{ label, url, newTab }` objects edited with WordPress's link picker (page search included); a button without a URL isn't rendered.
- **Media** is stored as `{ id, posterId, alt }`. Alt text is read from the library at render time unless the slot overrides it. WordPress decides lazy or eager loading. Each slot's shape and crop, and the sizes to upload, are in [Images](images.md).
- **Keyboard and focus.** Every interactive element has a visible focus style; custom controls (header menu, slider, video, media pause) are real buttons with accessible names.
- **Hide from visitors.** Every Floe block has a Hide from visitors setting (Block settings → Advanced) without doing anything itself: the [Hidden from visitors](../components/hidden-from-visitors/README.md) component adds the attribute, skips the block for people who can't edit the page and marks it for those who can. A block that links to other blocks asks the `floe_block_visible` filter first (In-page navigation does), so it never links to something the viewer can't see.
- **`example`** in every `block.json` for inserter previews, and a README in every folder.

## Adding a block

1. Create `blocks/<name>/` with `block.json` (`"name": "floe/<name>"`, category `floe-sections` for a section or `floe-parts` for a child with `parent`), `<name>.php`, `<name>.scss`, `<name>-editor.js` and `README.md`. Point `editorScript`, `style` and `render` at `file:./assets/<name>-editor.js`, `file:./assets/<name>.css` and `file:./<name>.php`.
2. In PHP, open the wrapper with `Floe\block_attributes( $block, … )`; in the editor use `useFloeBlockProps( name, … )` from `@floe/editor`.
3. If the block needs server code (a REST route, shared query helpers), put it in `<name>-server.php` in the same folder; it's loaded only while the block is enabled.
4. `npm run build`. The block appears in the inserter; nothing else needs registering.

Deleting the folder removes it completely; content that used it simply stops rendering it.
