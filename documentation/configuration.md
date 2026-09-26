# Configuration map

Find a behaviour here before adding another hook.

## Core setup (`includes/*.php`, loaded in order by `functions.php`)

| File | What it does |
| --- | --- |
| `Modules.php` | Discovers blocks and components; enabled state from `floe_disabled_modules` + `floe_enabled_modules` filter. |
| `Theme.php` | Theme supports (title tag, thumbnails, custom logo, responsive embeds, HTML5, editor styles), removes core block patterns, disables remote patterns, registers menu locations. |
| `Assets.php` | Enqueues `assets/css/base.css`; turns on per-block, on-demand asset loading. |
| `Editor.php` | Allowed blocks (Floe + the core slot blocks + any third-party block), keeps non-Floe blocks out of the top level, "Floe sections" / "Floe parts" categories, Page Banner + Article + CTA template for new posts, no Openverse. |
| `Templates.php` | Page title H1 when the content has none; reading-column wrapper for non-section content. |
| `Patterns.php` | "Floe pages" pattern category and pattern helpers (`image_id()`, `media()`, `link()`, `block()`, core block markup helpers). |
| `Blocks.php` | Registers enabled blocks; `Floe\block_attributes()`. |
| `Components.php` | Loads enabled components, registers their CSS/JS; `Floe\component()` and `Floe\icon()`. |

## Feature files (auto-loaded from `includes/*/`)

Delete a file to remove that behaviour.

| File | Behaviour |
| --- | --- |
| `Admin/Comments.php` | Comments and pingbacks off site-wide (front end and admin), comment screens redirected, menu/toolbar/dashboard items removed, no comment feed or `X-Pingback`. |
| `Admin/Branding.php` | No WordPress logo (toolbar, login), "Howdy" removed, login label "Email", footer credit "Built with Floe." |
| `Admin/Menu.php` | Dashboard hidden, menu order Pages, Posts, Media, Plugins, Users, Settings; logins and the dashboard go to Pages; no admin-email check. Appearance and Customize are never hidden. |
| `Admin/Editor.php` | No block directory, no tags on posts, wider editor sidebar. |
| `Admin/Frontend.php` | No emoji scripts; admin bar sits in the page flow. |
| `Admin/CodeInjection.php` | Customizer → Code injection: Head, Start of body and Footer fields (administrators only; stored as options so they survive a theme change). |
| `mail/smtp.php` | Sends WordPress email through SMTP when `wp-config.php` defines `FLOE_SMTP_HOST` (plus `FLOE_SMTP_USER`, `FLOE_SMTP_PASS`, optional `FLOE_SMTP_PORT` and `FLOE_SMTP_FROM`); sender name is the site name instead of "WordPress". |
| `Media/Images.php` | Made's image pipeline: `mobile` 800, `laptop` 1440, `desktop` 2400 sizes only (plus thumbnail), JPEG quality 70, opaque PNG uploads converted to JPEG. |

## Site settings the theme reads

| Setting | Where | Used for |
| --- | --- | --- |
| Logo | Customize → Site Identity | Header and footer (falls back to the Floe logo). |
| Site Icon | Customize → Site Identity | Browser icon. |
| Tagline | Settings → General | Footer sign-off. |
| Header menu | Appearance → Menus, location "Header menu" | Main navigation. |
| Header and footer button | Menu location of that name | First item becomes the header and footer button. |
| Footer menus 1–3 | Menu locations | Footer link columns; each menu's name is its heading. |
| Footer legal line | Customize → Footer | Text after "© year site name." |
| Code injection | Customize → Code injection | Scripts in head, body and footer. |
| Date format | Settings → General | Post card and post dates. |
| Administration Email Address | Settings → General | Where Form entries are emailed (`floe_form_recipient` filter to change). |
