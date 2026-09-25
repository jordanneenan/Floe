# Made 4 reference and migration decisions

Made is the default reference where the [build brief](build-brief.md) is silent. It is installed read-only beside Floe at `wp-content/themes/made` in the same LocalWP site: read it, but never edit, activate, commit or depend on it. [Made notes](made-notes.md) describe its architecture, and [decisions](decisions.md) records each choice to follow or depart from it. This page keeps the earlier area-by-area mapping.

## Current mapping

| Made 4 area | Floe status | Where / why |
| --- | --- | --- |
| `blocks/<group>/<id>/` | Restructured | One folder per block directly under `Blocks/`; the library now includes 12 sections and 4 nested helpers. |
| ACF block fields and `acf_register_block_type()` | Replaced | WordPress `block.json`, native block attributes, editor controls, PHP render. |
| `blocks/spacing/spa1/` | Reimplemented | `floe/spacing` preserves size choices, default values, responsive ratios, and per-block overrides. It is not a file-for-file copy. |
| `components/` | Partially adopted | Header and footer template parts under `Components/`. Other Made components have not been ported. |
| `functions.php` plus `functions/` | Reorganized | Thin root loader and focused files in `Config/`. |
| Admin comments suppression / light UI cleanup | Partially adopted | `Config/Comments.php` and `Config/AdminUI.php`. Dashboard and normal WordPress menus remain. |
| Made option pages, ACF global fields, brand colors | Not ported | Prefer core settings and `theme.json`; add project-specific controls only for a confirmed need. |
| Made image conversion and size rewrite | Not ported | Use native media handling initially. |
| Made front-end libraries and build pipeline | Not ported | Current build uses `@wordpress/scripts` and Sass for each custom block, plus shared section CSS. |
| Made anchor-point block | Not ported | Use core block HTML anchors. |

## Decision rule for future ports

For each requested Made feature: identify its user-visible behavior, check the current core WordPress equivalent, decide whether it belongs in the theme or a plugin, then implement only what Floe needs. Document any new contract or deliberate difference here. Do not import ACF field arrays, global CSS, JS libraries, or menu restrictions just because they are present in Made.
