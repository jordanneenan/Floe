# Made architecture notes

Made is Jordan's previous platform (theme name "Made.", version 4.0.0, by Driven). Where the [build brief](build-brief.md) is silent, Floe follows Made and records the choice in [decisions.md](decisions.md). This page describes how Made works so that those choices can be made quickly.

Made is installed read-only beside Floe at `wp-content/themes/made` in the same LocalWP site. Read it; never edit, activate, commit or reference it at runtime. These notes describe Made at commit `ccd8b54` ("Add one-off build mode").

## Summary

Made is a classic PHP theme built on **ACF Pro blocks**. Blocks are found by scanning folders at runtime, so adding a folder adds a block. Everything else is wired by hand: components, global SCSS, global JS and field groups are listed in central files. Site settings live in ACF options pages. Floe keeps Made's "folder = feature" idea and its conventions, extends discovery to components and the build, and replaces ACF with native WordPress.

## 1. Module discovery and registration

**Folder layout: `blocks/<type>/<variant>/`.** The type folder is the section (`page-banner`) and each variant is a short code (`pba1`). Every variant is a separate block, which leaves room for alternative designs of the same section (`frm1`, `frm2`). A variant folder contains:

```
blocks/page-banner/pba1/
  pba1.php          render template
  pba1-fields.php   ACF field group, assigned to a variable named $pba1fields
  pba1.scss         styles (partials start with _, e.g. layouts/_one-col.scss)
  assets/           compiled pba1.css, optional pba1.js, optional _admin.scss
```

**Runtime scan** (`functions/blocks/blocks.php`):

- Loops over `blocks/*/*` with `opendir()` on every request and builds `$GLOBALS['made_blocks']` (group, name, title, whether `assets/<name>.js` exists).
- Calls `acf_register_block_type()` for each variant as `acf/<variant>`, all in the `layouts` category with the same icon. Title and description come from the field array, found by a variable-variable naming convention (`$<slug-without-dashes>fields`).
- Registers the field group with `acf_add_local_field_group()`.
- Uses one shared render callback that works out the template path from the block name.
- Deleting a folder removes the block on the next page load with no rebuild. Floe's brief asks for the same behaviour.

**Asset loading is per block.** ACF's `enqueue_assets` callback enqueues `assets/<name>.css` (and `.js` if present) only when the block is on the page. This is on-demand loading, the same idea Floe gets natively from `block.json` and `should_load_separate_core_block_assets`.

**Inserter previews.** A `<variant>.png` beside the variant switches the example to a static image. The check and the URL use different paths (`blocks/<group>/<name>.png` against `/img/block-previews/<name>.png`), so this is probably broken.

**Allowed blocks.** `allowed_block_types_all` returns every discovered `acf/*` block plus `core/shortcode`, `core/columns` and `gravityforms/form`. The list is built from discovery, so adding a block needs no edit. Core block patterns are removed.

**Module activation.** Made has **no enable/disable mechanism**. Every discovered block is registered and allowed. Floe will build the seam described in brief section 3.3 without a Made precedent.

**Rough edges.** The scan logs `print_r( $blocks )` to the error log on every request, and a missing fields file is logged rather than skipped cleanly.

## 2. Build

Made uses two tools, run with `npm run build` (one-off) or `npm run watch`:

- **`build.js` (Sass).** It globs `blocks/**/[!_]*.scss` and compiles each file into a sibling `assets/` folder. Files starting with `_` are partials and are never compiled on their own. It also compiles `includes/global.scss` to `includes/css/global.css` and `includes/admin/admin.scss` to admin CSS. Vendor CSS (normalize, fancybox, tiny-slider) is copied out of `node_modules` into `includes/vendor/*.scss` so it can be `@use`d. In watch mode it uses chokidar and BrowserSync, proxying a hard-coded `made-4.local`. A changed partial triggers a walk up the tree to recompile its parent file.
- **Rollup (JS).** This bundles `includes/js/main.js` and the admin JS into IIFE bundles with jQuery kept external. Block JS is *not* bundled: `assets/<name>.js` files are hand-written, jQuery-dependent scripts served as-is.

The block Sass is discovered, but **global styles and scripts rely on central lists**. `global.scss` `@use`s every component by path, and `main.js` imports every component script by path.

## 3. Components

Components live in `components/<name>/` as PHP functions plus SCSS, and sometimes JS or layout subfolders:

| Component | What it is |
| --- | --- |
| `buttons` | `button( $linkField, $modifiers, $buttonType )` takes an ACF link array and returns `<div class="button_wrapper"><a class="button …">` or an arrow-icon button. The Component settings options page overrides colours, size, radius and height, and those values are printed as inline `<style>` with `!important`. |
| `image-video` | `imageVideo( $img, $videoUrl, $fill )` returns an `<img>`, a background-image `div` (for "fill"), or an autoplaying muted MP4 with a poster. |
| `animate-on-scroll` | `aos()` prints a `data-aos` attribute when the AOS option is on, and enqueues the AOS library. |
| `rounded-corners` | A helper driven by the "Rounded corners" option. |
| `header` | `header.php` (doctype, head, code injection, Typekit) plus `layouts/<layout>/header-body.php`. The layout (`default`, `mega-nav`, `split-nav`) is picked by an option and loaded by folder name. `register-navs.php` registers menus. |
| `footer`, `hamburger`, `search`, `forms`, `icons`, `gutenberg-blocks` | Mostly SCSS, with some JS. |

Patterns worth keeping:

- A component is **one function that returns a string**, which callers `echo`. Blocks call components instead of writing button or media markup themselves. This is the "one source of truth" the brief asks for.
- **Variants as layout subfolders** (`header/layouts/<name>/`), chosen at runtime.

Patterns to leave behind:

- Components are loaded by explicit `include_once` lines in `functions/core/core.php`, and their CSS and JS by the central lists above, so deleting a folder causes a fatal error.
- All component CSS ships in a single `global.css` on every page.
- There is no output escaping in `button()` or `imageVideo()`.
- Components use global functions with generic names (`button`, `aos`) instead of a namespace.

## 4. Header, navigation and footer

- `index.php` includes `components/header/header.php` and `components/footer/footer.php` directly rather than using `get_header()` and `get_footer()`, and just runs `the_content()`. There are no other templates apart from `404.php` and `search.php`.
- **Menus.** Made registers `main_navigation`, plus `main_navigation_right` when the split layout is on. The mobile nav asks for `main_nav`, and the footer for `footer_nav` and `terms_nav`, but none of these three is registered.
- The desktop nav uses `fallback_cb => false`, so an empty location prints nothing instead of listing every page. Floe's brief asks for the same.
- **Mobile menu.** A `div.hamburger` (not a button, with no `aria-expanded`) toggles classes through jQuery. Parent menu items with children have their link disabled so they can open the dropdown.
- **Logo** comes from an ACF option image. There is no custom-logo support.
- **Footer** has a footer menu, social icons (ACF repeater), a terms menu and a copyright line (`© Site 2025–<date('Y')>. Site by Driven.`). The year comes from the server clock, not the site timezone.
- The search icon and form appear only when the Relevanssi plugin is active. Post listings use Ajax Load More templates (`alm_templates/`).

## 5. Settings and site behaviour

**ACF options pages** (`functions/core/options-pages.php` and `includes/fields/`):

- **Options:** favicon, logo, social icons, Typekit fonts ID, nav layout, animate on scroll, rounded corners, mega-nav content, search, preview content, and header/body/footer code injection.
- **Brand colours:** a repeater of name + colour. `brandColours()` feeds colour choice fields, and `get_brand_colour( $slug )` resolves hex values. Renaming a colour breaks the blocks that use it.
- **Block settings:** global spacing values.
- **Component settings:** button styling overrides.

**Global behaviour** (`functions/core/core.php`, `functions/admin/admin.php`):

- **Comments are disabled.** Made removes comment and trackback support, closes comments and pings, empties comment arrays, redirects `edit-comments.php` and removes the menu and toolbar items. All of this sits in `admin.php`, which only loads when `is_admin()`, so the front-end `comments_open` filters never actually run on the front end. Floe's `Config/Comments.php` already applies them everywhere.
- **Admin tidy-up.** Made removes the WP logo (admin bar and login page), trims "Howdy", relabels the login field as "Email", replaces the footer credit, removes the emoji scripts, widens the editor sidebar, removes tags (unless Relevanssi is active), disables the block directory, reorders the admin menu, and hides Dashboard and Comments. The admin bar is moved to the bottom of the page on the front end.
- **Image pipeline** (`image-manager.php`, class `CustomImageSizesManager`).
  - It replaces WordPress's generated sizes with `mobile` 800, `laptop` 1440 and `desktop` 2400 (width only, no crop) plus `thumbnail`, and sets JPEG quality to 70.
  - It keeps WordPress's large-image threshold at 2560px. WordPress still scales bigger uploads to a `-scaled` copy and keeps the original file.
  - PNGs with no transparent pixels are converted to JPEG. The PNG original and its sizes are deleted and the attachment is switched to the JPEG. PNGs with transparency are left as PNGs.
  - A further step resizes the main file to a maximum of 2800px wide. It never has an effect, because WordPress has already scaled anything over 2560px.
  - The transparency check reads every pixel through GD, which is slow on large opaque PNGs.
- **Section spacing.** `.block` has a bottom margin of 120px by default (medium 80, small 40), reduced on tablet and mobile. The docs say ÷1.4 and ÷1.8, while `_layout.scss` uses ×0.625 and ×0.5. The Spacing block (`spa1`) adds a spacer with a size class or a per-instance pixel override in an inline `<style>`, with tablet and mobile values at ÷1.4 and ÷1.8 or set by hand. `remove-last-block-spacing.js` strips the margin from specific last blocks, but its list is empty.
- **Breakpoints.** Made has desktop 1400, small desktop 1000, tablet 768, mobile 600 and small mobile 440. The spacing code uses 768 and 550.
- **Design tokens** are SCSS variables (`$primary`, `$secondary`, `$tertiary`, greys, and a `1280px` content width with 20px gutters), increasingly overridden by admin options.

## 6. What Floe adopts

| Area | Made | Floe |
| --- | --- | --- |
| Block discovery | Runtime folder scan, deleting a folder removes the block | **Adopt.** Scan `Blocks/` for `block.json` one or two levels deep at runtime. Register natively with `register_block_type()` (no ACF). |
| Block naming | `<type>/<variant-code>` folders | **Adapt.** One PascalCase folder per block (`Blocks/PageBanner/`), with children nested inside their parent. Design variants use block styles or attributes rather than sibling variant folders. Made's pattern stays available if a second, truly different design is ever needed. |
| `_` prefix | Partials are never compiled on their own | **Adopt.** Folders and files starting with `_` are ignored by discovery (brief 3.1). |
| Per-block assets | Enqueued only when the block is present | **Adopt, natively.** Assets are declared in `block.json` and on-demand loading is enabled. Front-end JS uses `viewScript`, with no jQuery. |
| Allowed blocks | Allowlist built from discovery | **Adopt the shape.** Floe sections are the top-level inserter items, built from discovery with no central name list. Core blocks are allowed only inside designated slots (brief 4). |
| Build | Glob Sass, central global lists, Rollup and BrowserSync | **Adapt.** Discovery-based `@wordpress/scripts` and Sass builds for blocks *and* components, with no central lists, plus a real `npm run start` watch with BrowserSync live reload, as Made has, proxying `floe.local` by default (D7). |
| Components | Global functions returning strings, included by hand | **Adapt.** Namespaced functions returning escaped HTML, discovered from `Components/*/`, called through `Floe\component()`, which fails soft when a component is missing. Each component compiles and registers its own CSS. |
| Component variants | Layout subfolders chosen by option | **Keep in reserve** for the header if more layouts are wanted later. For now there is one header layout. |
| Module on/off | None | **New.** Add a `Config/Modules.php` seam, an option and a filter (brief 3.3). |
| Header/footer | Direct includes, ACF logo, unregistered menu locations | **Adapt.** Use `get_header()`/`get_footer()` templates calling Header, Navigation and Footer components, with the native custom logo, registered locations, no page fallback, an accessible disclosure button, and the year from `wp_date()`. |
| Brand settings | ACF options (logo, favicon, colours, fonts) | **Replace natively.** Custom logo, Site Icon, `theme.json` palette and self-hosted fonts. |
| Code injection | Options fields for head, body and footer | **Adopt, natively.** Customizer fields for head, body and footer, output through native hooks. Admins only (D10). |
| Comments off | In theme, admin-only file | **Keep in theme** permanently, in `Config/Admin/Comments.php`, applying on the front end too (D8, D17). |
| Admin tidy-up | Extensive | **Adopt all of it** in `Config/Admin/`, one file per tweak. Appearance and Customize are never hidden (D11, D17). |
| Image pipeline | Own sizes, quality 70, opaque PNG → JPEG | **Adopt all of it** (D12). The sizes and the processing live in the theme, with a faster transparency check. |
| Spacing | 120/80/40, per-block override, ÷1.4 and ÷1.8 | **Already reimplemented** in `floe/spacing`. The brief keeps its behaviour. Section padding follows brief section 4. |
| Scroll animation (AOS) | Option-driven library | **Not now.** It isn't in the brief, and any future version must respect reduced motion. |
