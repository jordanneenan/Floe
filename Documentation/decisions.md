# Decisions

This is the log of choices made where the [build brief](build-brief.md) is silent or needed interpreting. Each entry names its source: **Jordan**, **Brief**, **Made** ([notes](made-notes.md)) or **Judgement**. Revisit any entry by editing its status and adding a dated note, and don't delete entries.

Status values: **Adopted** (in effect), **Planned** (agreed, lands in the named phase), **Open** (needs Jordan).

## D1: Build brief lives in `Documentation/`

- **Date / phase:** 2026-09-25, phase 0
- **Source:** Judgement (AGENTS.md and CLAUDE.md link to `Documentation/build-brief.md`)
- **Decision:** The brief arrived at the repository root and was moved to `Documentation/build-brief.md` so the existing links resolve.
- **Status:** Adopted

## D2: Running WP-CLI against the LocalWP site

- **Date / phase:** 2026-09-25, phase 0
- **Source:** Judgement
- **Decision:** No `php` or `wp` is on the system PATH, so run Local's bundled PHP 8.5.3 with its shared libraries, the site's generated `php.ini` (which holds the MySQL socket) and Local's bundled `wp-cli.phar`. The command is documented in [workflow.md](workflow.md#wp-cli). The LocalWP site must be running in Local for database commands to work.
- **Status:** Adopted

## D3: Block folder naming and variants

- **Source:** Brief 3.1, adapting Made
- **Decision:** Keep one PascalCase folder per block, with children nested inside their parent (brief). Made's `<type>/<variant-code>/` layout isn't used. Visual variants are handled with block styles or attributes. If a second, structurally different design of a section is ever needed, add it as a separate block folder, which is Made's approach.
- **Status:** Planned (phase 2)

## D4: Module enable/disable

- **Source:** Brief 3.3 (Made has no equivalent)
- **Decision:** Made registers every discovered block unconditionally, so there's no Made pattern to follow. Build the brief's seam: `Config/Modules.php`, the `floe_disabled_modules` option and the `floe_enabled_modules` filter.
- **Status:** Planned (phase 2)

## D5: Allowed blocks built from discovery

- **Source:** Made, shaped by brief 4
- **Decision:** Like Made's `allowed_block_types_all`, the top-level allowlist is built from discovered blocks, so there's never a hand-written list. Core blocks are only offered inside designated slots (Article and other blocks' inner-block areas). Made's extras (`core/shortcode`, `core/columns`, `gravityforms/form`) are not allowed at the top level; form plugin blocks go in the Contact and Newsletter form slots instead.
- **Status:** Planned (phase 1 lockdown, phase 2 discovery)

## D6: Components are discovered, namespaced, escaped and fail soft

- **Source:** Brief 3.2, adapting Made
- **Decision:** Keep Made's "a component is a function that returns markup" model. Drop Made's hand-written `include_once` lines, global function names, missing escaping and single global stylesheet. Each `Components/<Name>/` folder is discovered, and its CSS is compiled and registered on its own.
- **Status:** Planned (phase 2)

## D7: Watch script with BrowserSync live reload

- **Source:** Jordan (2026-09-25), following Made
- **Decision:** `npm run start` watches block and component sources, rebuilds them, and reloads the browser through BrowserSync, as Made does. Made hard-codes its proxy (`made-4.local`). Floe's defaults to `http://floe.local` and can be overridden with an environment variable (for example `FLOE_PROXY`), so it works on other machines. `npm run build` stays a one-off build with no BrowserSync.
- **Status:** Planned (phase 2)

## D8: Comments stay disabled by the theme

- **Source:** Jordan (2026-09-25), following Made
- **Decision:** Disabling comments stays in the theme permanently; there's no plugin. It moves into the admin folder with the other admin tweaks (see D17). The Made version only runs in the admin, because it lives in `admin.php`. Floe's applies everywhere, which is the intended behaviour. Phase 1 also adds the pieces Made has that Floe doesn't yet: redirecting `edit-comments.php` and removing the dashboard comments widget.
- **Status:** Planned (phase 1)

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
- **Status:** Planned (phase 1)

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
- **Status:** Planned (phase 1)

## D12: Image sizes

- **Source:** Native first over Made
- **Decision:** Made's destructive pipeline (replacing sizes, deleting originals, JPEG quality 70) is not ported. When the Media component is built, consider registering Made's widths (800, 1440, 2400) with `add_image_size()` so `srcset`/`sizes` have sensible candidates. Leave WordPress's default sizes and originals intact.
- **Status:** Planned (phase 3, confirm with Jordan)

## D13: Footer year uses the site timezone

- **Source:** Brief 3.2 over Made
- **Decision:** Made uses `date('Y')`, the server clock, with a hard-coded build year. Floe uses `wp_date( 'Y' )`, with no "Site by" credit unless Jordan wants one.
- **Status:** Planned (phase 3)

## D14: Header layouts

- **Source:** Made (in reserve)
- **Decision:** Floe builds a single header layout from Figma. If more layouts are wanted, follow Made's `layouts/<name>/` subfolders inside the Header component, selected by a Customizer setting rather than an ACF option.
- **Status:** Planned (phase 3), with variants deferred

## D15: `Requires at least` matches the LocalWP site

- **Source:** Brief 2
- **Decision:** The LocalWP site runs WordPress 7.1.2 and PHP 8.5.3 (Local's PHP service for this site). Change `style.css` `Requires at least` from 6.6 to 7.1 in phase 1 and update the docs that state 6.6. Keep `Requires PHP` at 8.0 unless code needs more.
- **Status:** Planned (phase 1)

## D16: Preview imagery comes through the media library

- **Source:** Jordan (2026-09-25)
- **Decision:** The eight Floe images won't be committed. Jordan will upload them into WordPress once the blocks exist. The phase 6 seed script (brief 7) therefore won't import images from the repository. It will use suitable images already in the media library if there are any, and leave media empty otherwise. The five architectural images in `Assets/PreviewImagery/` are no longer the preview set. Remove them and update the README in phase 6, unless Jordan wants to keep them.
- **Status:** Adopted

## D17: Admin tweaks live in `Config/Admin/`, one file per tweak

- **Source:** Jordan asked for a single folder holding comments and admin tidy-up, with the name and location left to the build; Judgement for the details
- **Decision:** Create `Config/Admin/`. Each tweak is one self-contained file: `Comments.php`, `Branding.php` (logo, footer credit, login label, Howdy), `Menu.php` (hidden items, order, Dashboard redirect), `Editor.php` (block directory, sidebar width, tags), `Frontend.php` (emoji, admin bar), `CodeInjection.php` (D10). `Config/Admin/` is loaded by scanning the folder, the same idea as block discovery, so deleting a file removes that tweak and nothing lists them by name. It replaces today's `Config/Comments.php` and `Config/AdminUI.php`. It sits under `Config/` rather than `Components/` because these are site behaviours, not UI pieces.
- **Status:** Planned (phase 1)
