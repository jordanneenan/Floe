# Configuration map

Find a behaviour here before adding another hook.

## Core setup (`includes/*.php`, loaded in order by `functions.php`)

| File | What it does |
| --- | --- |
| `modules.php` | Discovers blocks and components; enabled state from `floe_disabled_modules` + `floe_enabled_modules` filter. |
| `theme.php` | Theme supports (title tag, thumbnails, custom logo, responsive embeds, HTML5, editor styles), removes core block patterns, disables remote patterns, registers menu locations. |
| `assets.php` | Enqueues `assets/css/base.css`; turns on per-block, on-demand asset loading. |
| `editor.php` | Allowed blocks (Floe + the core slot blocks + any third-party block), keeps non-Floe blocks out of the top level, "Floe sections" / "Floe parts" categories, Page Banner + Article + CTA template for new posts, no Openverse. |
| `templates.php` | Page title H1 when the content has none; reading-column wrapper for non-section content. |
| `blocks.php` | Registers enabled blocks; `Floe\block_attributes()`. |
| `components.php` | Loads enabled components, registers their CSS/JS and loads any component editor script in the block editor; `Floe\component()` and `Floe\icon()`. |

## Feature files (auto-loaded from `includes/*/`)

Delete a file to remove that behaviour.

| File | Behaviour |
| --- | --- |
| `admin/comments.php` | Comments and pingbacks off site-wide (front end and admin), comment screens redirected, menu/toolbar/dashboard items removed, no comment feed or `X-Pingback`. |
| `admin/branding.php` | No WordPress logo (toolbar, login), "Howdy" removed, login label "Email", footer credit "Built with Floe." |
| `admin/menu.php` | Dashboard hidden, menu order Pages, Posts, Media, Plugins, Users, Settings; logins and the dashboard go to Pages; no admin-email check. Appearance and Customize are never hidden. |
| `admin/editor.php` | No block directory, no tags on posts, wider editor sidebar. |
| `admin/frontend.php` | No emoji scripts; admin bar sits in the page flow. |
| `admin/code-injection.php` | Customizer → Code injection: Head, Start of body and Footer fields (administrators only; stored as options so they survive a theme change). |
| `mail/smtp.php` | Sends WordPress email through SMTP when `wp-config.php` defines `FLOE_SMTP_HOST` (plus `FLOE_SMTP_USER`, `FLOE_SMTP_PASS`, optional `FLOE_SMTP_PORT` and `FLOE_SMTP_FROM`); sender name is the site name instead of "WordPress". |
| `media/images.php` | Image pipeline: `mobile` 800, `laptop` 1440, `desktop` 2400 sizes only (plus thumbnail), JPEG quality 70, opaque PNG uploads converted to JPEG. |

## Behaviour that lives in a component

| Folder | Behaviour |
| --- | --- |
| `components/hidden-from-visitors/` | Hide from visitors (Block settings → Advanced) on every Floe block: the `hiddenFromVisitors` attribute, not rendering hidden blocks for people who can't edit the page, the marker for those who can, and the `floe_block_visible` filter. [README](../components/hidden-from-visitors/README.md). |
| `components/reveal/` | Scroll reveals on every front-end page. [README](../components/reveal/README.md). |
| `components/smooth-scroll/` | Smooth scrolling to anchors on every front-end page, for in-page links and for pages opened with an anchor in the URL. [README](../components/smooth-scroll/README.md). |

## Site settings the theme reads

| Setting | Where | Used for |
| --- | --- | --- |
| Logo | Customize → Site Identity | Header and footer (falls back to the Floe logo). |
| Site Icon | Customize → Site Identity | Browser icon. |
| Tagline | Settings → General | Footer sign-off. |
| Header menu | Appearance → Menus, location "Header menu" | Main navigation. |
| Header and footer button | Menu location of that name | First item becomes the header and footer button. |
| Footer menus 1–3 | Menu locations | Footer link columns; each menu's name is its heading. |
| Header options | Customize → Header | Sticky header (on by default; shrinks over the first 300px of scrolling), show the button (on), menu position (Centred or Right). |
| Footer legal line | Customize → Footer | Text after "© year site name." |
| Code injection | Customize → Code injection | Scripts in head, body and footer. |
| Date format | Settings → General | Post card and post dates. |
| Administration Email Address | Settings → General | Where Form entries are emailed (`floe_form_recipient` filter to change). |
