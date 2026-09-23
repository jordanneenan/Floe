# Architecture and ownership

## Request path

```text
WordPress loads Floe/functions.php
  -> requires Config/Theme.php, Assets.php, Blocks.php, Comments.php, AdminUI.php
  -> after_setup_theme: theme support, editor style, primary menu
  -> init: register each immediate Blocks/* directory with block.json
  -> wp_enqueue_scripts: load root style.css
  -> template request: index.php -> header.php / footer.php -> Components/*
  -> floe/spacing render: Blocks/Spacing/spacing.php + compiled block CSS
```

`functions.php` is intentionally a small loader. Do not put substantial feature logic there. Each `Config/*.php` file registers its own WordPress hooks when loaded.

## Directory contracts

| Path | Owns | Does not own |
| --- | --- | --- |
| `Blocks/` | Custom Gutenberg blocks, one immediate folder per unique block | ACF fields, nested group/version directories |
| `Components/` | Shared PHP template parts such as header and footer | Block registration or site-wide hooks |
| `Config/` | Theme setup, hooks, asset registration, admin behavior | Page markup and block-specific render logic |
| `style.css` | WordPress theme metadata and small base styles | Per-block styling |
| `theme.json` | Native editor settings and design presets | ACF options or custom admin forms |
| `header.php`, `footer.php`, `index.php` | Required classic theme templates and fallback content loop | Feature registration |
| `Documentation/` | Agent-oriented contracts, decisions, workflow | Runtime code |

## Current template behavior

- `header.php` prints the document opening, `wp_head()`, `wp_body_open()`, skip link, then `Components/Header/header.php`.
- The header component renders the custom logo when set; otherwise it links the site name. It renders the `primary` menu and uses WordPress's page-menu fallback.
- `index.php` is the fallback template. It loops posts, shows linked titles for non-singular views, renders `the_content()`, and prints pagination.
- `footer.php` renders `Components/Footer/footer.php`, calls `wp_footer()`, and closes the document.

## Current boundaries

- Floe is a theme. Reusable content types, data models, APIs, and site functionality that must persist across theme switches should be implemented in a separate plugin when needed.
- Native WordPress Site Icon, media behavior, menus, block editor, and `theme.json` controls are the starting point.
- No existing WordPress installation or browser-based visual test is part of this repository. A successful asset build proves compilation, not runtime integration.
