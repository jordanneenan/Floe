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

## D7: Watch script without BrowserSync

- **Source:** Judgement (brief section 6 asks for `npm run start`; Made uses chokidar with BrowserSync)
- **Decision:** `npm run start` watches and rebuilds block and component sources, and Jordan reloads `floe.local` manually. BrowserSync can be added later if Jordan wants live reload. If it is, take the proxy URL from an environment variable instead of hard-coding it as Made does.
- **Status:** Planned (phase 2), Open on live reload

## D8: Comments stay disabled by the theme

- **Source:** Made, with a brief section 6 check
- **Decision:** Made disables comments in the theme, so Floe keeps `Config/Comments.php`. The Made version only runs in the admin, because it lives in `admin.php`. Floe's version already applies everywhere, which is the intended behaviour. Remaining gaps, such as redirecting `edit-comments.php`, removing the dashboard widget and handling comment feeds, get fixed in phase 1. Disabling comments is site behaviour, so it may belong in a small plugin later.
- **Status:** Adopted; **Open** for Jordan: keep it in the theme, or plan a plugin?

## D9: Brand settings use native WordPress, not options pages

- **Source:** Brief 2 and 4 (native first) over Made
- **Decision:** Made's ACF options for logo, favicon, brand colours and fonts are replaced by the custom logo, Site Icon, the `theme.json` palette and self-hosted Geist. Floe won't have a brand-colours repeater.
- **Status:** Adopted

## D10: Code injection fields are not ported

- **Source:** Judgement (native first; this is site behaviour)
- **Decision:** Made has header, body and footer code-injection options. Floe doesn't build them, because `wp_head`, `wp_body_open` and `wp_footer` are native hooks and a snippet plugin can cover the need. The theme must call `wp_body_open()`.
- **Status:** **Open**: does Jordan want this as a Floe plugin later?

## D11: Admin tidy-up beyond the current set

- **Source:** Made versus the existing Floe docs
- **Decision:** Floe already copies Made's WP logo removal, comments menu removal and footer credit. Made also hides Dashboard, trims "Howdy", relabels the login field, reorders the menu, removes tags, disables the block directory, removes emoji scripts and moves the admin bar to the bottom. The brief says to follow Made, but the earlier Floe docs deliberately kept Dashboard and other menus. Nothing changes in phase 0.
- **Proposal:** Adopt the low-risk items (disable the block directory, remove emoji scripts). Leave menus, Dashboard and tags alone unless Jordan wants Made parity.
- **Status:** **Open**

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

## D16: Preview imagery not yet supplied

- **Source:** Brief 7
- **Decision:** The eight Floe images (`floe-hero`, `-dawn`, `-drift`, `-seam`, `-blue`, `-giant`, `-pack-teal`, `-dusk`) are not in the repository or anywhere under the home directory. `Assets/PreviewImagery/` still holds the five architectural images committed in `5f5a610`, and they stay until Jordan supplies the new files.
- **Status:** **Open**: waiting on the files (needed by phase 6)
