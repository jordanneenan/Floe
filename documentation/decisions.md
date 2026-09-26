# Decisions

This is the log of choices made where the [build brief](build-brief.md) is silent or needed interpreting. Each entry names its source: **Jordan**, **Brief**, **Made** ([notes](made-notes.md)) or **Judgement**. Revisit any entry by editing its status and adding a dated note, and don't delete entries.

Status values: **Adopted** (in effect), **Planned** (agreed, lands in the named phase), **Open** (needs Jordan).

## D1: Build brief lives in `documentation/`

- **Date / phase:** 2026-09-25, phase 0
- **Source:** Judgement (AGENTS.md and CLAUDE.md link to `documentation/build-brief.md`)
- **Decision:** The brief arrived at the repository root and was moved to `documentation/build-brief.md` so the existing links resolve.
- **Status:** Adopted

## D2: Running WP-CLI against the LocalWP site

- **Date / phase:** 2026-09-25, phase 0
- **Source:** Judgement
- **Decision:** No `php` or `wp` is on the system PATH, so run Local's bundled PHP 8.5.3 with its shared libraries, the site's generated `php.ini` (which holds the MySQL socket) and Local's bundled `wp-cli.phar`. The command is documented in [workflow.md](workflow.md#wp-cli). The LocalWP site must be running in Local for database commands to work.
- **Status:** Adopted

## D3: Block folder naming and variants

- **Source:** Brief 3.1, adapting Made
- **Decision:** Keep one PascalCase folder per block, with children nested inside their parent (brief). Made's `<type>/<variant-code>/` layout isn't used. Visual variants are handled with block styles or attributes. If a second, structurally different design of a section is ever needed, add it as a separate block folder, which is Made's approach.
- **Status:** Superseded by D18 (2026-09-25). Folders are now lowercase, matching the block name. The variant approach above still stands.

## D4: Module enable/disable

- **Source:** Brief 3.3 (Made has no equivalent)
- **Decision:** Made registers every discovered block unconditionally, so there's no Made pattern to follow. Build the brief's seam: `includes/modules.php`, the `floe_disabled_modules` option and the `floe_enabled_modules` filter.
- **Status:** Adopted (phase 2, 2026-09-26)

## D5: Allowed blocks built from discovery

- **Source:** Made, shaped by brief 4
- **Decision:** Like Made's `allowed_block_types_all`, the top-level allowlist is built from discovered blocks, so there's never a hand-written list. Core blocks are only offered inside designated slots (Article and other blocks' inner-block areas). Made's extras (`core/shortcode`, `core/columns`, `gravityforms/form`) are not allowed at the top level; form plugin blocks go in the Contact and Newsletter form slots instead.
- **Status:** Adopted (phases 1–2, 2026-09-26)

## D6: Components are discovered, namespaced, escaped and fail soft

- **Source:** Brief 3.2, adapting Made
- **Decision:** Keep Made's "a component is a function that returns markup" model. Drop Made's hand-written `include_once` lines, global function names, missing escaping and single global stylesheet. Each `components/<name>/` folder is discovered, and its CSS is compiled and registered on its own. Folder and file naming follows D18.
- **Status:** Adopted (phases 2–3, 2026-09-26)

## D7: Watch script with BrowserSync live reload

- **Source:** Jordan (2026-09-25), following Made
- **Decision:** `npm run start` watches block and component sources, rebuilds them, and reloads the browser through BrowserSync, as Made does. Made hard-codes its proxy (`made-4.local`). Floe's defaults to `http://floe.local` and can be overridden with an environment variable (for example `FLOE_PROXY`), so it works on other machines. `npm run build` stays a one-off build with no BrowserSync.
- **Status:** Adopted (phase 1, 2026-09-26)

## D8: Comments stay disabled by the theme

- **Source:** Jordan (2026-09-25), following Made
- **Decision:** Disabling comments stays in the theme permanently; there's no plugin. It moves into the admin folder with the other admin tweaks (see D17). The Made version only runs in the admin, because it lives in `admin.php`. Floe's applies everywhere, which is the intended behaviour. Phase 1 also adds the pieces Made has that Floe doesn't yet: redirecting `edit-comments.php` and removing the dashboard comments widget.
- **Status:** Adopted (phase 1, 2026-09-26)

## D9: Brand settings use native WordPress, not options pages

- **Source:** Brief 2 and 4 (native first) over Made
- **Decision:** Made's ACF options for logo, favicon, brand colours and fonts are replaced by the custom logo, Site Icon, the `theme.json` palette and self-hosted Geist. Floe won't have a brand-colours repeater.
- **Status:** Adopted

## D10: Code injection in the Customizer

- **Source:** Jordan (2026-09-25): no plugin, so the theme decides; following Made
- **Decision:** Jordan doesn't want a plugin, so Floe keeps Made's feature inside the theme, built natively instead of with ACF. There will be a **Code injection** section in the Customizer (Appearance → Customize) with three fields: *Head*, *Start of body* and *Footer*. They print through the native `wp_head`, `wp_body_open` (already called in `header.php`) and `wp_footer` hooks.
  - The settings are stored as site options, not theme mods, so the code isn't lost if the theme is ever switched.
  - Only users with the `unfiltered_html` capability (administrators) can see or edit the fields, because they accept raw scripts.
  - The feature lives in its own file in the admin folder (D17), so it can be removed by deleting that file.
- **Status:** Adopted (phase 1, 2026-09-26)

## D11: Admin tidy-up: full Made parity

- **Source:** Jordan (2026-09-25), following Made
- **Decision:** Adopt all of Made's admin tweaks, grouped in the admin folder (D17):
  - Remove the WordPress logo from the admin bar and the login page, and set Floe's own admin footer credit.
  - Replace "Howdy, name" with just the name, and relabel the login field "Username or Email Address" as "Email".
  - Hide the Dashboard and Comments menu items.
  - Reorder the admin menu to Pages, Posts, Media, Plugins, Users, Settings. Appearance and the rest follow in core order.
  - Remove tags from posts (unconditionally; Floe uses native search, so Made's Relevanssi exception isn't needed).
  - Disable the block directory in the editor.
  - Remove the emoji detection script and styles.
  - Widen the editor settings sidebar.
  - Turn off the periodic admin email verification screen.
  - Stop the admin bar from pushing the page down on the front end. Made's comment says "bottom", but its CSS actually makes the bar sit in the normal page flow at the top, so Floe copies the actual behaviour.
- **Rule:** Appearance and Appearance → Customize are **never hidden**. Made doesn't hide them either (its line is commented out), and the Customizer holds code injection (D10) and the logo.
- **Judgement:** Hiding the Dashboard menu doesn't stop WordPress sending users there after login, so Floe also redirects the login landing page and `index.php` to the Pages list.
- **Status:** Adopted (phase 1, 2026-09-26)

## D12: Image sizes and processing: Made's pipeline

- **Source:** Jordan (2026-09-25), following Made
- **Decision:** Keep Made's image sizes and its upload processing in the theme:
  - Register `mobile` 800, `laptop` 1440 and `desktop` 2400 (width only, no crop). Keep `thumbnail` and stop WordPress generating its other default sizes.
  - Set JPEG quality to 70 and keep the large-image threshold at 2560px.
  - Convert opaque PNGs to JPEG, delete the PNG files and switch the attachment to the JPEG. Leave transparent PNGs alone.
- **Differences from Made:**
  - Check transparency with Imagick's alpha channel check where it's available, falling back to GD. Made reads every pixel, which is slow on big PNGs.
  - Leave out Made's 2800px resize, which never has an effect (see [Made notes](made-notes.md)).
  - Put the code in one self-contained file, `includes/media/images.php`. The Media component's `sizes` attribute uses these three widths.
- **Note:** The PNG conversion deletes the uploaded PNG, so it can't be undone for that image. That's Made's behaviour and Jordan's choice. The theme applies it to new uploads only; existing media is untouched unless it's regenerated.
- **Status:** Adopted (phase 3, 2026-09-26)

## D13: Footer year uses the site timezone

- **Source:** Brief 3.2 over Made
- **Decision:** Made uses `date('Y')`, the server clock, with a hard-coded build year. Floe uses `wp_date( 'Y' )`, with no "Site by" credit unless Jordan wants one.
- **Status:** Adopted (phase 3, 2026-09-26)

## D14: Header layouts

- **Source:** Made (in reserve)
- **Decision:** Floe builds a single header layout from Figma. If more layouts are wanted, follow Made's `layouts/<name>/` subfolders inside the Header component, selected by a Customizer setting rather than an ACF option.
- **Status:** Adopted (phase 3, one layout, 2026-09-26)

## D15: `Requires at least` matches the LocalWP site

- **Source:** Brief 2
- **Decision:** The LocalWP site runs WordPress 7.1.2 and PHP 8.5.3 (Local's PHP service for this site). Change `style.css` `Requires at least` from 6.6 to 7.1 in phase 1 and update the docs that state 6.6. Keep `Requires PHP` at 8.0 unless code needs more.
- **Status:** Adopted (phase 1, 2026-09-26)

## D16: Preview imagery comes through the media library

- **Source:** Jordan (2026-09-25)
- **Decision:** The eight Floe images won't be committed. Jordan will upload them into WordPress once the blocks exist. The phase 6 seed script (brief 7) therefore won't import images from the repository. It will use suitable images already in the media library if there are any, and leave media empty otherwise. The five architectural images in `assets/PreviewImagery/` are no longer the preview set. Remove them and update the README in phase 6, unless Jordan wants to keep them.
- **Status:** Adopted; the preview imagery folder was later removed (D34)

## D17: Admin tweaks live in `includes/admin/`, one file per tweak

- **Source:** Jordan asked for a single folder holding comments and admin tidy-up, with the name and location left to the build; Judgement for the details
- **Decision:** Create `includes/admin/`. Each tweak is one self-contained file: `Comments.php`, `Branding.php` (logo, footer credit, login label, Howdy), `Menu.php` (hidden items, order, Dashboard redirect), `Editor.php` (block directory, sidebar width, tags), `Frontend.php` (emoji, admin bar), `CodeInjection.php` (D10). `includes/admin/` is loaded by scanning the folder, the same idea as block discovery, so deleting a file removes that tweak and nothing lists them by name. It replaces today's `includes/Comments.php` and `includes/AdminUI.php`. It sits under `includes/` rather than `components/` because these are site behaviours, not UI pieces.
- **Status:** Adopted (phase 1, 2026-09-26)

## D18: Every module is findable from its class name

- **Source:** Jordan (2026-09-25). This overrides the `src/` + `build/` and PascalCase layout in brief 3.1 and 3.2, and it's how Made works.
- **Why:** What Jordan values most in Made is that he can inspect a page, read a block's class name, find the folder with that name and edit its files straight away, with the watcher compiling on save (D7).
- **Decision:** Folder name, wrapper class and file names are all the block's short name (the part after `floe/` in `block.json`):

  ```
  blocks/page-banner/
    block.json            name "floe/page-banner"
    page-banner.php       render template
    page-banner.scss      styles
    page-banner.js        front-end script, only if the block needs one
    page-banner-editor.js editor controls
    README.md
    assets/               compiled CSS/JS referenced by block.json (committed)
  ```

  - The block's outer element has the class `page-banner` (exactly the folder name) alongside WordPress's own `wp-block-floe-page-banner`.
  - Child blocks nest inside their parent in the same style, for example `blocks/cards/card-item/card-item.php`.
  - Shared helper folders keep the `_` prefix (`blocks/_shared/`) so discovery ignores them.
  - Components follow the same convention: `components/button/button.php`, `button.scss`, `button.js` and `button-editor.js` if they need them, and the component's root class is `button`. The PHP function stays namespaced (`Floe\Components\button()`, called through `Floe\component( 'button', … )`).
- **Consequence:** Phase 2 renames the existing `blocks/PageBanner/`-style folders, renames `render.php` to `<name>.php` and `assets/` to `assets/`, and splits the editor JS into `<name>-editor.js`. Brief sections 3.1 and 3.2 are updated to show this layout.
- **Status:** Adopted (phases 2–5, 2026-09-26)

## D19: Sections own their padding; Spacing replaces it

- **Source:** Figma (every block frame includes its own padding), adapting Made's section gaps
- **Decision:** Each section pads itself top and bottom (`--floe-section-space`: 120 large desktop, 96 desktop, 80 tablet, 64 mobile; banners, CTA, Newsletter and Logo strip use the fractions drawn in Figma). Two adjacent sections therefore sit 240px apart at 1440, as the brochure frames show. A Spacing block between two sections removes the padding on the facing edges, so the gap is exactly its value. Spacing's presets and ÷1.4/÷1.8 ratios are unchanged.
- **Status:** Adopted (phase 2)

## D20: Editor lockdown through `ancestor`

- **Source:** Brief 4, judgement on the mechanism
- **Decision:** Every non-Floe block (core and third-party) gets an `ancestor` of all Floe blocks at `init`, so it can only be inserted inside a Floe block's slot; unused core blocks are removed via `allowed_block_types_all`; third-party blocks stay allowed so form plugins work in form slots. Nothing names individual Floe blocks.
- **Status:** Adopted (phase 1)

## D21: Heading font-size presets are `heading-1`…`heading-4`

- **Source:** Judgement (WordPress bug-for-bug behaviour)
- **Decision:** WordPress turns a preset slug `h2` into the CSS variable `--wp--preset--font-size--h-2`, which made every heading reference miss. The presets are named `heading-1`…`heading-4`.
- **Status:** Adopted (phase 6)

## D22: Surfaces drive colour through CSS variables

- **Source:** Figma Foundations and component descriptions (no explicit surface table, derived by the components spec)
- **Decision:** Five surface classes set variables every component reads. Primary buttons take the Inverse look on Inverse and Accent ("Inverse on dark or accent surfaces"); on Accent the eyebrow and body text are white, as in the Accent CTA. Blocks never pick a tone themselves.
- **Status:** Adopted (phase 1)

## D23: Form fields are 52px; buttons are 48px including their border

- **Source:** Figma descriptions over the drawn frames
- **Decision:** The Form field description says 52px (the drawing measures 60), and the Button description says 48px (the Secondary drawing measures 51 because Figma draws the border outside). Floe follows the descriptions: fields are 52px, every button is 48px with its 1.5px border inside, and the Newsletter submit button matches the 52px field.
- **Status:** Adopted (phase 3)

## D24: Small Figma inconsistencies resolved

- **Source:** Judgement, each noted in the block's README
- **Decision:**
  - Steps: markers line up with their text columns (Figma drifts them left by 8–24px); step 01 is always the filled marker.
  - Testimonials: a 32px minimum gap between quote and attribution (they touch in Figma); previous/next controls only appear when there are more than three quotes (as the description says).
  - Testimonial: the quote keeps Figma's drawn 40/50 size rather than the 36/44 Quote style.
  - Table: the highlighted column tint fills whole cells (Figma leaves strips in icon rows).
  - Header: not sticky; In-page navigation sticks to the top of the window instead (Figma gives no offset).
  - Media tag ("Image or looping MP4") is a Figma annotation and isn't rendered.
- **Status:** Adopted (phases 3–5)

## D25: Table content conventions

- **Source:** Judgement (Figma gives no authoring rule)
- **Decision:** In the core Table inside Floe's Table block, a cell containing only `✓` renders the check icon ("Included" for screen readers) and only `—`, `–` or `-` renders the dash ("Not included"). The highlighted column is chosen by number, its badge is a text field, and "Emphasise the last row" sets the last row in H4 for prices. Tables keep a 640px minimum width and scroll inside their frame on small screens.
- **Status:** Adopted (phase 5)

## D26: Forms come from the site's form plugin

- **Source:** Brief 3.2 and 5 (native first; no required plugins)
- **Decision:** Floe ships form styling, not a form. Contact and Newsletter have form slots that accept any non-Floe block (a form plugin's block or a Shortcode block). Without a form, Contact shows only its details and Newsletter can show a button instead, which is how Figma's "also usable as a slimmer CTA" is provided. The preview site uses a `mailto:` Subscribe button until a form plugin is added.
- **Status:** Adopted (phase 5); superseded by D42, which adds Floe's own Form block. The slots still accept a form plugin's block.

## D27: Logo strip tone

- **Source:** Judgement on "shown in one muted tone"
- **Decision:** Logos with transparency (PNG, WebP, GIF, SVG) are recoloured to muted ink at 75% with a CSS mask, whatever their colours; opaque files are shown in greyscale. The preview uses six generated Geist wordmarks matching the Figma placeholders.
- **Status:** Adopted (phase 5)

## D28: Links, cards and buttons

- **Source:** Brief 3.2 ("the title is the link, not a repeated Read more")
- **Decision:** Card titles are the link and cover the whole card. The Feature card's "Learn more" is a visual cue (`aria-hidden`). Every link field uses WordPress's link picker with page search, stored as `{ label, url, newTab }`; a button with no URL isn't rendered.
- **Status:** Adopted (phase 3)

## D29: Header button, footer sign-off and legal line use native settings

- **Source:** Native first
- **Decision:** The header and footer button is the first item of the "Header and footer button" menu location; the footer sign-off is the site tagline; footer column headings are the menu names; the legal line after "© year site name." is a Customizer setting (Footer). Component CSS loads on every page; block CSS only where the block is used.
- **Status:** Adopted (phase 3)

## D30: Preview content is reproducible, not stored in git

- **Source:** Brief 7, D16
- **Decision:** Brochure pages are block patterns in `patterns/`; the seed script builds pages, posts, menus and settings from them. Patterns find images by file name (`floe-hero` etc.), so any site with the photos uploaded gets the same result. The seed script also sets the site date format to `d M Y` to match the Figma dates, and moves WordPress's "Hello world!" post and "Sample Page" to the trash.
- **Status:** Superseded by D34 (2026-09-26)

## D31: Copy written where Figma has none

- **Source:** Judgement; flagged for Jordan
- **Decision:** Figma has answers for only the first FAQ item on each page, so the other twelve answers were written in the same voice. The three journal posts have short bodies, and Privacy and Accessibility are clearly marked placeholder pages (they're linked from the footer). The Video block has no YouTube link, so its play button is hidden until one is added.
- **Status:** **Open**: Jordan to review or replace the written copy and add a video link

## D32: Adding pattern files needs a cache refresh

- **Source:** WordPress behaviour
- **Decision:** WordPress caches a theme's pattern list per theme version outside development mode. The theme version is now 0.2.0; adding a pattern later needs a version bump, `WP_DEVELOPMENT_MODE` set to `theme`, or a run of the seed script.
- **Status:** Superseded by D34 (2026-09-26): there are no theme patterns

## D33: Lowercase folders and `includes/`

- **Source:** Jordan (2026-09-26)
- **Decision:** Every folder is lowercase. The top level is `assets/`, `blocks/`, `components/`, `includes/` (was `Config/`; feature files in `includes/admin/` and `includes/media/`, loaded automatically) and `documentation/`, plus WordPress's required files and a root `build.mjs` (like Made's `build.js`). PHP namespaces follow: `Floe\Includes\…`. A block that needs server code keeps it in its own folder as `<name>-server.php`, loaded only while the block is enabled.
- **Status:** Adopted (phase 8)

## D34: No theme patterns and no scripts folder

- **Source:** Jordan (2026-09-26), overriding brief section 7
- **Decision:** Pages are built from blocks in the editor; patterns can be made in WordPress if a site needs them. `patterns/`, the seed script, the screenshot script and the preview imagery folder are removed. The existing pages were already stored as blocks, so nothing on the site changed.
- **Status:** Adopted (phase 8)

## D35: One template; posts are built from blocks

- **Source:** Jordan (2026-09-26)
- **Decision:** `index.php` is the only template (`singular.php` and `404.php` are gone). Posts are laid out with blocks like pages: new posts start with Page Banner, Article and CTA. The existing journal posts were rebuilt that way (banner with the excerpt and featured image). The H1 fallback stays for anything without a banner.
- **Status:** Adopted (phase 8)

## D36: 860px left-aligned reading column

- **Source:** Jordan (2026-09-26), overriding the Figma offset column
- **Decision:** The narrow content width (the `content.narrow` token, not WordPress's `contentSize`, which would also squeeze sections in the editor) is 860px and left-aligned to the content edge everywhere it's used (Article, the reading-column fallback, Image + Copy without media).
- **Status:** Adopted (phase 8)

## D37: No posts page; Journal is a normal page

- **Source:** Jordan (2026-09-26)
- **Decision:** WordPress's "posts page" setting is off. Journal is a page built from blocks (Page Banner, Posts, Newsletter), so any listing page can use the full library.
- **Status:** Adopted (phase 8)

## D38: Posts block does listing, load more and filters itself

- **Source:** Jordan (2026-09-26), following Made's Posts block without the Ajax Load More plugin
- **Decision:** Sources are Latest (any public post type), Hand-picked and Manual entries (child **Post card** blocks). Latest shows 1–24 or all (capped at 100), with "More posts" set to none, a Load more button, or automatic loading on scroll. Filters are the top-level terms of a chosen taxonomy (categories or a custom taxonomy). Filtering and loading more fetch server-rendered cards from the block's REST route (`floe/v1/posts`) and swap them in place without reloading the page. The chosen filter goes in the address as `?filter=<term-slug>` (with `pushState`, so Back and Forward step through filters); the server reads it, so a shared link opens already filtered. The route only returns published posts of public post types, filtered by public taxonomies.
- **Status:** Adopted (phase 8). Filter in the URL confirmed by Jordan (2026-09-26), as long as the page doesn't refresh.

## D39: CTA holds one to three panels

- **Source:** Jordan (2026-09-26), following Made's CTA (a repeater of panels in a row)
- **Decision:** The CTA block is a container of 1–3 **CTA panel** child blocks, each with eyebrow, heading, body, action, note, optional image and its own surface (Ink, Accent, Tint, Subtle). One panel keeps the wide Figma layout; two or three sit in equal columns with headings stepping down in size. Existing CTAs were converted to one-panel CTAs.
- **Status:** Adopted (phase 8)

## D40: Dark mode follows the device until the visitor chooses

- **Source:** Jordan (2026-09-26)
- **Decision:** Dark mode is a set of dark tokens (`settings.custom.dark`) applied to the existing surfaces under `html[data-theme="dark"]`, so every block gets a dark version without its own dark styles. The site follows `prefers-color-scheme` (live) until the visitor presses the sun/moon toggle in the footer; the choice is then kept in `localStorage`. An inline script at the top of `<head>` sets the theme before first paint, so there's no flash. Switching wipes the new theme across the page in a circle from the button where View Transitions are supported, and cross-fades colours over 0.4s elsewhere. The light theme declares `color-scheme: only light`, so Chrome's forced dark mode (`chrome://flags/#enable-force-dark`) can't invert it after a visitor switches to light. The editor stays light. Figma gets a dark copy of each block and component beside the light one.
- **Status:** Adopted (phase 9)

## D41: One motion timing, and reveals that never hold content back

- **Source:** Jordan (2026-09-26): "any color changes, on buttons, need to fade over 0.4 seconds", with a smooth, subtle fade-in as content scrolls into view
- **Decision:** Every colour change and hover uses `--floe-duration` (0.4s) and `--floe-ease` (`cubic-bezier(0.22, 1, 0.36, 1)`), from `settings.custom.motion`. Scroll reveals are a component (`components/reveal/`) that picks its own targets: the parts of each section, with grid and list items staggered 90ms apart. Only JavaScript hides anything, and only below the fold, so nothing is hidden without it and the first screen never waits; banners have their own CSS load-in instead. Reduced motion turns every animation off. Deleting the Reveal folder switches reveals off.
- **Status:** Adopted (phase 9)

## D42: Floe has its own form, not a form plugin

- **Source:** Jordan (2026-09-26): no email address shown on the site, and no form plugin
- **Decision:** A **Form** block (`blocks/form/`) goes in the Contact and Newsletter form slots. It has two types: the Figma enquiry form (first and last name, email, organisation, "How can we help?", consent) and a one-field newsletter signup. It posts to `admin-post.php`, so it works without JavaScript; with JavaScript it sends in the background and shows the thank-you message in place. Each entry is saved as a private **Enquiry** (admin menu, no public URL) before an alert is emailed to the site's administration email address with Reply-To set to the sender, so nothing is lost if email fails. Spam protection is a hidden field, a signed timestamp (sends within two seconds of loading are refused) and five sends per visitor per ten minutes; there is no nonce, so cached pages keep working. `includes/mail/smtp.php` sends all WordPress email through SMTP when `wp-config.php` defines `FLOE_SMTP_HOST`. Newsletter signups are stored the same way until a mailing provider is chosen.
- **Status:** Adopted

## D43: The header menu goes inline from 1280px; menu labels never break inside a word

- **Source:** Jordan (2026-09-26): header and footer links broke mid-word at tablet widths ("Platfor / m" at 800px, "Documentat / ion" at 1024px)
- **Decision:** The header keeps the menu button below the `large` breakpoint (1280px) and shows the menu in the bar from there. The default five items and button need about 820px of content width, so 768px was too narrow, and 1280px leaves room for longer client menus while staying on the system breakpoints; Made uses a separate `$navBreak` instead. Menu labels wrap only between words (`overflow-wrap: normal` in the Navigation component, overriding base.scss's `anywhere` for list items); header links and the button don't wrap at all, and a menu too long for the bar wraps onto a second row between items. In the footer the link columns sit beside the sign-off only when both fit (about 1090px with three columns) and otherwise wrap underneath, and the columns themselves wrap rather than squeeze.
- **Status:** Adopted
