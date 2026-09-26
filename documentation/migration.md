# Made 4 reference and migration decisions

Made is the default reference where the [build brief](build-brief.md) is silent. It is installed read-only beside Floe at `wp-content/themes/made` in the same LocalWP site: read it, but never edit, activate, commit or depend on it. [Made notes](made-notes.md) describe its architecture, and [decisions](decisions.md) records each choice to follow or depart from it. This page keeps the earlier area-by-area mapping.

## Current mapping

| Made area | Floe | Where / why |
| --- | --- | --- |
| `blocks/<type>/<variant>/` discovered at runtime | Adopted, natively | `blocks/<name>/` with `block.json`, discovered at runtime by `includes/modules.php`; folder, class and file names match (D18). |
| ACF blocks and field groups | Replaced | `block.json` attributes, RichText/InnerBlocks editing, PHP render templates. |
| Per-block assets only where used | Adopted | `block.json` assets with on-demand loading; `viewScript` for front-end JS. |
| `components/` (button, image-video, header, footer) | Adopted and extended | `components/<name>/`, discovered, namespaced, escaped, fail-soft via `Floe\component()`, with React twins for the editor. |
| Allowed-blocks list built from discovery | Adopted | `includes/editor.php`, plus core blocks restricted to Floe slots. |
| Build: Sass glob, Rollup, BrowserSync watch | Adapted | `build.mjs`: discovery-based Sass and webpack, `npm run start` with BrowserSync. |
| Comments disabled | Adopted (fixed to apply on the front end) | `includes/admin/comments.php`. |
| Admin tidy-up | Adopted in full | `includes/admin/` (Appearance and Customize stay visible). |
| Code injection options | Adopted, natively | Customizer fields, `includes/admin/code-injection.php`. |
| Image sizes and processing | Adopted | `includes/media/images.php`. |
| Brand colours, logo, favicon, fonts options | Replaced | `theme.json`, custom logo, Site Icon, self-hosted Geist. |
| Spacing block and section gaps | Adapted | Sections own their padding; `floe/spacing` replaces the facing padding. |
| Header layouts as subfolders | In reserve | One header layout for now (D14). |
| Animate on scroll, Ajax Load More, Relevanssi search | Not ported | Not in the brief; native search is used when search is designed. |

## Decision rule for future ports

For each requested Made feature: identify its user-visible behavior, check the current core WordPress equivalent, decide whether it belongs in the theme or a plugin, then implement only what Floe needs. Document any new contract or deliberate difference here. Do not import ACF field arrays, global CSS, JS libraries, or menu restrictions just because they are present in Made.
