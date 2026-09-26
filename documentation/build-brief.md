# Floe build brief

This is the working brief for building Floe from the approved Figma designs. It is written for Claude Code working in Jordan's repository at `~/Local Sites/floe/app/public/wp-content/themes/floe`, but any developer can follow it.

Read this whole file before starting. Then read `AGENTS.md` and `documentation/README.md`.

## 1. How to make decisions

When sources disagree, use this order:

1. **Jordan's current instruction** in the session.
2. **This brief.**
3. **What Made does.** Made is Jordan's previous platform. If this brief doesn't cover something, find how Made handles it and do the same. Record the decision in `documentation/decisions.md` (create it) so it can be revisited. Made isn't perfect, but following it by default beats inventing a new pattern.
4. **Your own judgement**, recorded in `decisions.md` the same way.

One rule outranks Made: **native WordPress first.** If WordPress has a native way to do something, use it: core blocks, menus, the media library, `theme.json`, patterns, native search, the custom logo, the site icon, WP-CLI. If it can't be native, it goes in the theme. If it can't reasonably live in the theme, it becomes a small plugin that Jordan owns, but ask first. There's no ACF, and no third-party plugin is required for the theme to work.

### Where Made lives

Made is installed as a reference-only theme in the same WordPress site, beside Floe: `~/Local Sites/floe/app/public/wp-content/themes/` (the Made folder, likely `made` or `made-4`; list the directory to confirm). Read it for architecture: how modules are discovered, registered, built, named and styled, and how components are called. Don't copy code wholesale; use the patterns.

Made is outside the Floe repository and must stay untouched:

- Never edit it.
- Never activate it.
- Never commit it.
- Never import from it at runtime. Floe must not reference Made's files, functions or assets.

If you can't find Made, tell Jordan rather than guessing.

## 2. Environment

| Item | Value |
| --- | --- |
| Repository | `~/Local Sites/floe/app/public/wp-content/themes/floe`, remote `https://github.com/jordanneenan/Floe.git`, branch `main`. This is the **only** copy of the project: there is no `~/Projects/Floe` and no symlink. |
| Local site | LocalWP site "floe" at `~/Local Sites/floe/app/public`, URL `http://floe.local`. The theme folder *is* the repository, so edits show up at `floe.local` immediately. |
| Paths | `Local Sites` contains a space. Quote every path in shell commands and scripts (`"$HOME/Local Sites/floe/..."`), and never assume the repo sits at a fixed absolute path; resolve it relative to the script. |
| Figma | `https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n` (see section 9 for node IDs) |
| Toolchain | Node 20+, npm, PHP 8+, WordPress 6.8+ (confirm the LocalWP site's version and set `Requires at least` to match) |

### Before writing any code

- Confirm the theme folder is a git repository with the GitHub remote (`git remote -v`) and is clean, or report what's uncommitted.
- Confirm `floe.local` loads with Floe active.
- Run `git status`, `npm ci` and `npm run build`.
- Find how to run WP-CLI against the LocalWP site. Local's "Open site shell" provides it; from a normal terminal you may need Local's bundled PHP and the site's socket. If you can't make WP-CLI work, ask Jordan.

**Seeing changes.** Jordan reviews in the browser at `floe.local`. After each meaningful change:

- Rebuild.
- Reload the relevant preview page and tell Jordan what to look at.
- If Playwright is available, take screenshots at 375, 600, 1024 and 1440px wide into a git-ignored `.screenshots/` folder, and check them yourself before reporting.

**Git workflow.** Build it like a proper project:

- Work on a branch per phase (`phase-1-foundations`, `phase-2-modules`, …). Never commit straight to `main`, and never force-push `main`.
- Make small, focused commits with imperative messages ("Add Button component", "Rebuild Home Banner against v2 design").
- Commit source **and** generated build output. The theme must work without Node on a server.
- Push the branch. If the `gh` CLI is authenticated, open a pull request describing what changed, how it was verified and what still needs Jordan's eye. Otherwise tell Jordan the branch is pushed.
- Stop at the end of each phase for Jordan's review before merging.
- Keep `node_modules/`, `.screenshots/`, and any local database or export out of git.

## 3. Architecture: everything is a module

This is the core requirement. **Adding a folder adds a feature; deleting a folder removes it completely.** There are no central lists to edit, anywhere.

### 3.1 Blocks (`blocks/`)

Each block is one self-contained folder. **The folder name, the block's wrapper class and its file names are all the block's short name**, so you can inspect a page, read the class and go straight to the files, the way Made works (Jordan, see `decisions.md` D18):

```
blocks/home-banner/
  block.json               apiVersion 3, name "floe/home-banner", textdomain "floe", example, supports
  home-banner.php          dynamic PHP render template; outer element has class "home-banner"
  home-banner.scss         styles (partials start with _)
  home-banner.js           front-end script, only if the block needs one (viewScript)
  home-banner-editor.js    editor controls
  README.md                what the section is for, its fields, variants, and client editing notes
  assets/                  compiled output referenced by block.json (committed)
```

- **Child blocks live inside their parent's folder**, for example `blocks/cards/card-item/block.json`. Deleting `cards/` removes its children too.
- `floe/media` is used by more than one parent, so it stays at the top level as `blocks/media/`.
- **Registration is discovery-based.** `includes/blocks.php` scans `blocks/` for `block.json` files one or two levels deep at runtime and registers each one. Scanning at runtime (rather than from a generated manifest) is deliberate: a deleted folder is gone on the next page load, with no rebuild needed. A cached manifest can be added later as an optimisation, as long as it rebuilds itself when folders change.
- **The build is discovery-based too.** `npm run build` finds every block by its `block.json` and compiles its sources into that block's own `assets/`. `npm run start` watches, recompiles on save and reloads the browser (BrowserSync, D7). No block names appear in `package.json`, webpack config, SCSS indexes, `theme.json` or PHP.
- Folders starting with `_` are ignored by discovery. Use them for shared build helpers only, not for block markup.
- **Block assets load only on pages that use the block.** Enable on-demand loading of block assets for this classic theme, and use `viewScript` in `block.json` for front-end JS instead of enqueuing it globally.
- **Nothing breaks when a block is removed.** Deleting a block leaves no PHP errors or console errors. Content that used a deleted block simply stops rendering it. Don't hard-code one block's name inside another block, except a parent's own children.

### 3.2 Components (`components/`)

Components are the reusable pieces that blocks and templates are built from. Each is a self-contained folder:

```
components/button/
  button.php          PHP render function, e.g. Floe\Components\button( array $args ): string (escaped HTML); root class "button"
  button.scss         all styling for every button on the site
  button.js           front-end script, only if needed
  button-editor.js    optional React preview used by block edit components
  README.md           arguments, variants, usage
  assets/             compiled CSS/JS (committed)
```

- **One source of truth.** The Button component controls every button on the site, both markup and styling. Blocks must never write their own button markup or button CSS. The same applies to every component below.
- **Discovery.** `includes/components.php` finds each `components/*/` folder, loads its PHP, and registers its compiled CSS on the front end and in the editor. Deleting the folder removes all of that.
- **Graceful dependencies.** Provide a helper, for example `Floe\component( 'button', $args )`, that returns an empty string (and logs when `WP_DEBUG` is on) if the component is missing. That way a deleted component can't cause a fatal error. Each block's README lists the components it uses.
- **Editor previews.** Blocks import component previews through a build alias such as `@floe/components/button`. A missing component should fail the build with a clear message, not fail silently.

Components to build (Figma node IDs are in section 9):

| Component | Notes |
| --- | --- |
| **Button** | Primary, Secondary, Inverse and Link styles; optional arrow; pill shape, 48px tall; link, rel and target handling; visible focus state. |
| **Eyebrow** | Accent dot plus Geist Mono uppercase label; default and inverse tones. |
| **Section header** | Eyebrow, heading, optional intro and optional action. Used by most sections, so the layout stays consistent. The heading level is configurable. |
| **Media** | Image or silent looping MP4 with poster. Correct `sizes` per slot. Let WordPress decide eager or lazy loading; never force `lazy` on banners. MP4s get a visible pause/play control (WCAG 2.2.2) and respect reduced motion. Alt text is read from the media library at render time, with an optional override. |
| **Card** | Post and Feature variants. The title is the link, not a repeated "Read more". |
| **File row** | Whole row is the link. Type and size come from attachment metadata, not `filesize()`. The download arrow is `aria-hidden`. |
| **Play control** | YouTube facade button. Its accessible name includes the video title. |
| **Icon** | Inline SVG set (arrows, download, play, plus, minus, check, dash). |
| **Breadcrumb** | Generated from the page hierarchy. Use a core breadcrumbs block if the installed WordPress has one. |
| **Form field styles** | Styles for native inputs, textareas and checkboxes, so any form plugin's markup inherits them. Label above a 52px field, 1.5px border, `md` radius. |
| **Accordion styles** | Styling for the core Details block. |
| **Header** | Custom logo (falls back to the Floe SVG), primary navigation and one action button. |
| **Navigation** | `wp_nav_menu` with registered locations. Accessible mobile menu using a disclosure button, `aria-expanded` and focus handling. No `wp_page_menu` fallback that lists every page. |
| **Footer** | Logo, sign-off text, action, up to three footer menu locations, and a legal line with the year in the site timezone. |

### 3.3 Future: enabling and disabling modules

Jordan will later want to switch modules on and off. Build the seam for this now, but not the UI:

- `includes/modules.php` returns every discovered block and component with an enabled flag.
- Enabled state comes from a `floe_disabled_modules` option (empty by default), passed through a `floe_enabled_modules` filter.
- A disabled module isn't registered, isn't enqueued and doesn't appear in the inserter.
- Adding an admin screen later should only require writing that option.
- If Made has its own approach to module activation, follow it.

## 4. Design system (from Figma "Floe / Foundations")

Put all of this in `theme.json` so each client site re-brands through tokens, never by editing blocks.

**Colour.** Primitive values, with semantic names in brackets:

| Token | Value |
| --- | --- |
| ink (text/primary, surface/inverse) | `#0B1220` |
| ink-raised | `#162033` |
| muted (text/secondary) | `#5A6474` |
| line (border) | `#E4E7EC` |
| white (surface/base) | `#FFFFFF` |
| canvas (surface/subtle) | `#F4F5F7` |
| ice (surface/tint) | `#E8EEF7` |
| blue (action, text/accent) | `#2B50E8` |
| blue-deep (action hover) | `#2446D8` |
| blue-soft (chips) | `#DCE4FD` |
| on-dark-muted | `#9AA4B5` |
| on-dark-accent | `#A9BCFF` |

All text pairings in the designs pass WCAG AA; keep them that way.

**Type.** Self-host Geist and Geist Mono (SIL OFL; the `geist` npm package ships woff2 files) through `theme.json` `fontFace`. No Google Fonts request.

| Style | Size / line height | Weight | Letter spacing |
| --- | --- | --- | --- |
| Display XL | 84/88 | Medium | −3.5% |
| Display | 64/68 | Medium | −3% |
| H1 | 52/56 | Medium | −2.5% |
| H2 | 40/46 | Medium | −2% |
| H3 | 26/32 | Medium | −1.2% |
| H4 | 19/26 | SemiBold | |
| Quote | 36/44 | Medium | −1.8% |
| Body L | 20/31 | Regular | |
| Body | 17/27 | Regular | |
| Small | 14/21 | Regular | |
| Label | 15/20 | Medium | |
| Eyebrow | 12/16 | Geist Mono Medium | +6%, uppercase |

Scale display and heading sizes fluidly (with `clamp()`) down to mobile.

**Radius.** `sm` 8, `md` 16, `lg` 24 (media), `xl` 32 (panels, CTA), `pill` 999 (buttons, chips).

**Spacing.** 4, 8, 12, 16, 24, 32, 48, 64, 96, 128. Section padding is 120px on large desktop and scales down. The content width is 1248px inside 96px gutters at 1440.

**Breakpoints.** Mobile <550, tablet 550–767, desktop 768–1279, large ≥1280. Use one set of SCSS mixins everywhere.

**Surfaces.** Base, Subtle, Tint, Inverse (ink) and Accent (blue). Text, eyebrow, link and button colours switch automatically per surface.

**Editor lockdown.** The editor should only offer what fits the system:

- Turn off `appearanceTools`, custom colours, custom font sizes, custom spacing and the default palette.
- Offer only the palette and sizes above.
- Floe sections are the top-level inserter items. Core blocks are allowed inside Article and inside other blocks' designated slots.

Mobile and tablet designs don't exist in Figma yet. Make sensible responsive decisions:

- Stack columns.
- Scroll tables inside their own container.
- Scroll in-page navigation horizontally.
- Turn sliders into swipeable rows.

Note each decision in the block's README for Jordan to review.

## 5. Blocks to build

The Figma component descriptions are the spec; this table summarises them. "Core-backed" means the block wraps native core blocks rather than reimplementing them.

| Block | Fields and behaviour |
| --- | --- |
| Home Banner | Eyebrow, H1 (Display XL), body, primary action, optional secondary action, full-width media (image or MP4). Headline-first layout. |
| Page Banner | Breadcrumb (auto, can be toggled off), H1 (Display), intro, optional link, optional media. Tint surface by default, with a working surface control. |
| Article | Core inner blocks in a 760px reading column, plus an optional button. Explicit `allowedBlocks` (headings, paragraph, list, quote/pullquote, image, `floe/media`, table, separator, buttons): no Floe sections nested inside. |
| Image + Copy | Media left or right, eyebrow, H2, body, action. |
| CTA | Contained rounded panel. Surface Ink or Accent. Eyebrow, heading, body, action, optional secondary line. |
| Testimonial | Large quote mark, quote, name, role, optional small portrait. |
| Testimonials | Section header plus quote cards. Grid for up to three, slider with previous/next controls for more. |
| Posts | Section header with "View all" link. Latest, category or hand-picked posts (a searchable post picker, not typed IDs). Count 1–12. Excludes the current post. Per-post media override. `no_found_rows`. |
| Cards | Section header. Feature style (auto number, title, text, link) or Media style (image, title, text). Repeatable child cards. |
| Steps | Section header plus 3–5 connected, numbered steps. Vertical on mobile. |
| Stats | Section header plus 2–4 figures, each with value, accent unit and label. |
| Logo strip | Label plus 4–8 logos from the media library, shown in one muted tone. |
| Team | Section header plus people (portrait, name, role). Repeatable child items for now; a Team content type in a plugin is a later decision for Jordan. |
| Table | Section header plus a core Table with Floe styling, an optional highlighted column and check/dash icons. Core-backed. |
| FAQ | Heading group plus core Details items. Optional FAQPage structured data. Core-backed. |
| Document Download | Heading group plus file rows from the media library (PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX). |
| Images | Optional heading group, then rows of 1, 2 or 3 media slots. The column count follows the number of items (maximum 3). Gap toggle; fit is fill or natural. |
| Video | Dark section. Eyebrow, heading, body, YouTube URL, cover image and the Play control. The iframe loads only on click (youtube-nocookie). |
| Contact | Heading group, contact details (linked email and phone, address, hours), optional map image, and a form slot (inner block for any form plugin's block or shortcode). |
| Newsletter | Tint panel with heading, body, a form slot and a privacy note. |
| In-page navigation | Sticky bar listing the anchors of the sections on the page, generated automatically with manual override. Highlights the active item and has an optional action. |
| Spacing | Keep the existing behaviour: preset or custom gap across the four breakpoints, removing the previous section's default gap. |
| Media | Child helper for Article and Images, using the Media component. |

Every block needs:

- an `example` in `block.json`, for inserter previews
- a README
- correct heading levels: exactly one H1 per page, owned by the banners, with templates printing the title when a page has no banner
- no editor-only CSS on the front end
- working keyboard and focus states
- identical markup in the editor preview and on the front end

## 6. Known problems to fix while rebuilding

These come from the code review of the current repo:

- **Layout lives in shared files.** `render_simple()` / `createSimpleEdit()` hold six blocks' layouts. Move each layout into its own block folder.
- **Hidden media still takes a column.** When "Show media" is off but an image is set, Page Banner and Testimonial still reserve the space.
- **Page Banner surface has no control.** The attribute exists but the editor never shows it.
- **Article accepts any block**, including full-width sections.
- **Images row column count** doesn't match the number of items.
- **Posts editor shows raw HTML entities.** Use `decodeEntities`. Its date should match the front end's site format.
- **Removing an MP4 leaves the wrong placeholder text.**
- **Dead attributes.** Video `buttonLabel`/`buttonUrl` and CTA `eyebrow` are stored but never used.
- **Base CSS path.** Load it with `get_template_directory_uri()`, not `get_stylesheet_uri()` (which breaks child themes).
- **Menu fallback lists every page.**
- **No H1 on singular templates** that have no banner.
- **Comments are only partly disabled.** This is site behaviour, so check how Made handles it. If it belongs in a plugin, flag it to Jordan rather than building the plugin unprompted.
- **Component props.** Add `__next40pxDefaultSize` and `__nextHasNoMarginBottom` to components that need them. Use `LinkControl` (with internal page search) instead of plain URL text fields.
- **Inconsistent breakpoints.** Posts and Cards use 900/600; switch them to the system breakpoints.
- **Full-width sections overflow.** They use a `100vw` breakout with `overflow-x: clip`; use a layout that doesn't overflow.
- **Lint.** 243 `wp-scripts lint-js` errors (mostly formatting).
- **Documentation drift.** The docs mention watch scripts that don't exist. Add a real `npm run start` watch.

## 7. Preview content and imagery

- **Imagery.** Put the eight Floe images (`floe-hero`, `-dawn`, `-drift`, `-seam`, `-blue`, `-giant`, `-pack-teal`, `-dusk`; Jordan has the files) in `assets/PreviewImagery/`, replacing the architectural set, and update its README. They're placeholders, not client content.
- **Brochure pages as patterns.** Build Home, Platform, Pricing, About and Contact as theme patterns in `patterns/`. WordPress registers them from the file header, so this is native. Use the copy from the Figma brochure pages.
- **Seed script.** Add `scripts/seed-preview.php`, run with `wp eval-file`. It imports the preview imagery, creates a "Block Preview" page with every block, creates the five brochure pages from the patterns, sets Home as the front page and builds the menus. It must be safe to run repeatedly. This makes the preview reproducible on any machine, instead of living only in one LocalWP database.

## 8. Phases

Stop for Jordan's review at the end of each phase.

0. **Baseline.** Verify the environment (section 2). Read Made's architecture and write `documentation/made-notes.md`: what Made does for module discovery, registration, the build, components, header/footer and settings, and what Floe will adopt. Create `decisions.md`. Update any existing docs (`agent-handoff.md`, `workflow.md`, `README.md` and others) that still describe `~/Projects/Floe` or a symlink, so they match the single-location setup. No behaviour changes yet.
1. **Foundations.** `theme.json` tokens and fonts, editor lockdown, base styles, breakpoint mixins, surfaces, the `includes/` fixes from section 6.
2. **Module system.** Block and component discovery (runtime and build), the component helper, the `Modules.php` seam, docs. **Acceptance test:** duplicate a block folder under a new name and rename it in `block.json`; after a build it appears in the inserter. Delete it, and it's gone with no errors. Do the same for a component. Show Jordan both.
3. **Components.** Everything in 3.2, including header, navigation and footer, verified at all four breakpoints.
4. **Existing blocks.** Rebuild the original twelve against the Figma designs, one block per commit, fixing section 6 as you go.
5. **New blocks.** Stats, Steps, Logo strip, Testimonials, Team, Table, FAQ, Contact, Newsletter, In-page navigation.
6. **Patterns and preview.** Brochure page patterns, the seed script, imagery.
7. **QA and docs.**
   - Keyboard-only pass, reduced motion, long-content and missing-field checks, screen reader spot checks.
   - `npm run build`, lint, `php -l` on every file (plus PHPCS with WordPress Coding Standards if available).
   - Update `documentation/` so it matches the code.

## 9. Figma reference

The file key is `F43LfH93WZls4WkgIo5e3n`. There are three pages: `01 / Wireframes`, `02 / Block Designs + Components` and `03 / Brochure Site`. If a Figma MCP connection is available, pull design context by node ID. If not, the values in this brief are enough to build from.

| Area | Node IDs |
| --- | --- |
| Foundations | Board `38:181` |
| Components | Button `29:18`, Eyebrow `29:25`, Media `29:38`, Content card `30:88`, File row `30:89`, Play control `30:98`, Accordion item `42:196`, Form field `42:208`, Header `30:2`, Footer `30:25` |
| Blocks | Home Banner `33:11`, Page Banner `33:65`, Article `34:28`, Image + Copy `34:94`, CTA `35:75`, Testimonial `35:76`, Posts `36:58`, Cards `36:173`, Document Download `37:130`, Images `37:174`, Video `37:201`, FAQ `43:183`, Logo strip `43:226`, Stats `43:235`, Steps `43:263`, Testimonials `44:221`, Contact `44:263`, Team `44:317`, Table `45:255`, In-page navigation `45:345`, Newsletter `45:363` |
| Brochure pages | Home `47:4`, Platform `49:389`, Pricing `49:786`, About `49:1071`, Contact `49:1313` |

## 10. Out of scope for now

- **Search.** WordPress has native search (the `?s=` query, `search.php`, `get_search_form()` and the core Search block). Floe will use it, but designing and building the search experience is a later task. Don't build a custom search.
- **Admin screens.** The module enable/disable screen, and any other admin UI beyond native Customizer and Site settings.
- **Plugins.** Extracting blocks or site behaviour into plugins. Flag anything that should move, but don't build it.
- **Figma work.** Tablet and mobile Figma designs, and client-facing documentation beyond block READMEs.
