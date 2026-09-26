# Architecture and ownership

## Request path

```text
functions.php (loader only)
  -> includes/modules.php     discover blocks and components by folder
  -> includes/theme.php       theme supports, menus, editor styles
  -> includes/assets.php      base CSS; on-demand block assets
  -> includes/editor.php      editor lockdown and block categories
  -> includes/templates.php   one H1 per page; reading column fallback
  -> includes/blocks.php      load each enabled block's <name>-server.php, register
                              the block; Floe\block_attributes()
  -> includes/components.php  load enabled components; Floe\component()
  -> includes/*/*.php         self-contained features (Admin/, Media/), auto-loaded
template: header.php -> Floe\component( 'header' )
          index.php (the only template) -> includes/templates.php
          footer.php -> Floe\component( 'footer' )
blocks:   blocks/<name>/<name>.php renders each block on the server
```

## Modules

**Blocks** are folders under `blocks/` with a `block.json`, one or two levels deep (children live in their parent's folder). **Components** are folders under `components/`. Folders starting with `_` are ignored. `includes/modules.php` scans at runtime, so a deleted folder is gone on the next request; there's no manifest to rebuild.

Every module can be switched off without deleting it: the `floe_disabled_modules` option holds ids like `block:faq` or `component:breadcrumb`, and the list passes through the `floe_enabled_modules` filter. A disabled block isn't registered (so it isn't in the inserter); a disabled parent disables its children; a disabled component's PHP and CSS aren't loaded. An admin screen only needs to write that option.

| Convention | Rule |
| --- | --- |
| Names | Folder = block short name = wrapper class = file names (`blocks/page-banner/page-banner.php`, class `page-banner`). Components the same (`components/button/button.php`, root class `button`). |
| Block files | `block.json`, `<name>.php` (render), `<name>.scss` (front end and editor), optional `<name>-editor.scss`, `<name>-editor.js` (editor), optional `<name>.js` (front end, `viewScript`), optional `<name>-server.php` (server code such as a REST route, loaded when the block is enabled), `README.md`, compiled `assets/`. |
| Component files | `<name>.php` defining `Floe\Components\<name>( array $args ): string` (hyphens become underscores), `<name>.scss`, optional `<name>-editor.js` (React twin for editor previews), optional `<name>.js`, `README.md`, compiled `assets/`. |
| Calling a component | `Floe\component( 'button', $args )` returns escaped HTML, or `''` (logged under `WP_DEBUG`) if the component is missing or off. `Floe\icon( 'arrow' )` is shorthand. |
| Editor twins | Block editor scripts import `@floe/components/<name>`; the build resolves it to `components/<name>/<name>-editor.js` and fails with a clear message if it's missing. Shared editing UI (link buttons, media slots, the editable section header) is `@floe/editor` in `assets/js/editor/`. |
| Wrapper attributes | Block templates call `Floe\block_attributes( $block, array( 'surface' => 'tint', 'class' => … ) )`: adds the block's own class, `floe-section` for top-level sections, the surface class and the section anchor as `id`. The editor uses `useFloeBlockProps()` for the same classes. |
| Cross-module references | A block never names another block except its own children (`parent`, `allowedBlocks`). The shared `floe/media` helper lists its parents. Components are called by name through `Floe\component()`, which fails soft. |

## Assets

- `npm run build` finds every module by folder and compiles its SCSS (Sass, `assets/scss` on the load path so modules `@use 'floe' as *`) and JS (webpack, one bundle per file, WordPress packages external) into that module's `assets/`. Global SCSS in `assets/scss/*.scss` compiles to `assets/css/`. Geist fonts are copied from the `geist` package.
- Block assets are declared in `block.json` and load only on pages that use the block (`should_load_block_assets_on_demand`). Front-end block JS uses `viewScript`.
- Component CSS loads on every page and in the editor canvas (it's small and used everywhere). Component front-end JS (header menu, media) is registered as `floe-<name>` and enqueued by the component when it renders.
- `assets/css/base.css` (reset, surfaces, text-style classes, template fallbacks) loads from the template directory, so child themes don't break it.

## Styling system

- Tokens live in `theme.json`: palette, fluid font sizes (`display-xl`, `display`, `heading-1`…`heading-4`, `quote`, `body-l`, `body`, `small`, `label`, `eyebrow`), spacing, radii (`--wp--custom--radius--*`) and content width/gutter.
- `assets/scss/floe/` holds mixins only (no CSS output): `from( tablet|desktop|large )` / `below( … )` breakpoints (550, 768, 1280), `type( h2 )` text styles, `container`, `section-padding`, `focus-ring`, `visually-hidden`.
- **Surfaces.** A section gets `surface-base|subtle|tint|inverse|accent`. The surface sets CSS variables (`--surface-text`, `--surface-text-muted`, `--surface-accent`, `--surface-line`, `--surface-raised`, `--surface-eyebrow`, `--surface-link`, focus colour) that every component reads, so text, eyebrows, links and buttons switch automatically. Primary buttons take the Inverse look on Inverse and Accent.
- **Dark mode.** `<html data-theme="dark">` switches the Base, Subtle and Tint surfaces to the dark tokens in `theme.json` (`settings.custom.dark`, output as `--wp--custom--dark--*`); Inverse goes a step deeper so dark sections and the footer still stand apart, and Accent stays blue. Components never use fixed palette colours for anything that should change: they read surface variables (`--surface-background`, `-text`, `-text-muted`, `-line`, `-line-strong`, `-raised`, `-strong` / `-on-strong` for pressed pills, `-field`, `-chip`, `-tile`, `-highlight`, `-glass`, `-placeholder`). The [theme toggle](../components/theme-toggle/README.md) sets `data-theme` before first paint: the visitor's saved choice, else their device setting. The editor stays light.
- **Motion.** One timing for every colour change and hover: `--floe-duration` (0.4s) with `--floe-ease`; reveals use `--floe-reveal` (0.9s). All three come from `settings.custom.motion`. Banners fade up on load (the `rise()` mixin, front end only), content below the fold fades up as it scrolls into view ([Reveal](../components/reveal/README.md)), accordions open smoothly (`::details-content`), the header dropdown and mobile panel fade and slide (`@starting-style`), new Posts cards fade in, and Steps connectors draw in. `prefers-reduced-motion: reduce` turns all of it off.
- **Section rhythm.** Sections own their vertical padding (`--floe-section-space`: 120 large desktop, 96 desktop, 80 tablet, 64 mobile; some sections use a fraction, as in Figma). A Spacing block removes the padding on the edges facing it.

## Editor model

- Floe sections (category "Floe sections") are the only top-level blocks. Pages and posts are both built from sections; new posts start with Page Banner, Article and CTA. `includes/editor.php` gives every non-Floe block an `ancestor` of the Floe blocks, so core blocks only appear inside Floe slots, and removes core blocks Floe doesn't use from the inserter. Each Floe block's `allowedBlocks` decides what its slot accepts (Article: headings, paragraphs, lists, quotes, images, Media, table, separator, buttons, embed).
- Custom colours, font sizes, spacing and the default palette are off. New posts start with an Article.
- Editor and front end share markup: blocks render on the server, and editor previews use each component's React twin with the same classes.

## Templates

`index.php` is the only template. For pages and posts it calls `Floe\Includes\Templates\the_content()`: if the content has no `<h1>` (no banner), it prints the title as the H1; content that isn't made of Floe sections is wrapped in a reading column. 404s, archives and search results use the same page-title pattern. There's no posts page: listings are built with the Posts block on a normal page (e.g. Journal).

## Boundaries

- Floe is a theme. Behaviour that must survive a theme change belongs in a small plugin Jordan owns (ask first). Flagged candidates: a Team content type, and anything client-specific.
- Floe doesn't ship a form. Contact and Newsletter have form slots for the site's form plugin.
- The repository doesn't provision WordPress or ship content: pages live in the site's database.
