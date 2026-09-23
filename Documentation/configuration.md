# Configuration map

Find a behavior here before adding another hook. File names are part of the architecture contract.

| File | Hook or API | Current behavior |
| --- | --- | --- |
| `functions.php` | `require_once` | Loads the five `Config/` files; no behavior of its own. |
| `Config/Theme.php` | `after_setup_theme` | Text domain, title tag, thumbnails, custom logo, HTML5 markup, wide alignment, editor styles, `primary` nav menu. |
| `Config/Assets.php` | `wp_enqueue_scripts` | Enqueues root `style.css` using the theme version. Individual block assets come from block metadata. |
| `Config/Blocks.php` | `init` | Scans only immediate directories in `Blocks/`; calls `register_block_type()` for each directory with `block.json`. |
| `Config/Comments.php` | `init`, comments and pings filters | Removes comments/trackbacks support from post types, closes both, and hides existing comment arrays. |
| `Config/AdminUI.php` | `admin_menu`, `admin_bar_menu`, `admin_footer_text` | Removes Comments from admin menu; removes Comments and WordPress logo from toolbar; shows “Built with Floe.” footer text. |
| `theme.json` | Native theme settings | Enables appearance tools, sets 720px content and 1200px wide layout, offers 40/80/120px spacing sizes. |

## Placement rules

- Admin menu, toolbar, login, editor chrome, or admin copy changes belong in `Config/AdminUI.php` (or a new focused `Config/` file if it grows large).
- Theme supports, menus, and text-domain setup belong in `Config/Theme.php`.
- Global asset registration belongs in `Config/Assets.php`. Per-block asset declarations belong in that block's `block.json`.
- Content behavior such as the comments policy should stay outside `AdminUI.php`; hiding a menu is not the same as changing content behavior.
- Load any new `Config/` file explicitly from `functions.php`; there is no automatic config discovery.

## Defaults intentionally left native

Floe does not remove Dashboard, Posts, Pages, Media, Plugins, Users, or Settings. It does not restrict allowed block types or replace WordPress's image pipeline. A new admin restriction needs a concrete product requirement; do not port it solely because Made had one.
