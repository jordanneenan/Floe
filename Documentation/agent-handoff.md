# Floe review handoff

This is the starting point for an AI or human reviewing Floe without the original conversation. Read [AGENTS.md](../AGENTS.md) for contribution rules, then use the source and `block.json` files to verify implementation claims. The current user request takes precedence over this document.

## Project and intent

Floe is an open-source, GPL-2.0-or-later WordPress framework theme (`floe` text domain and block namespace). It draws on the earlier Made 4 project for useful behavior, but Made is a reference, not a dependency or a source to copy. The goal is a reusable library of complete page sections. An editor chooses a section, enters content, and gets a controlled layout without assembling a design from individual core blocks. Article is the intentional exception: it offers unrestricted native inner blocks within a designed section.

The user wants a visually neutral foundation. Site-specific typography, colors, imagery, graphics, and selected block changes should provide the brand. Each section should eventually have a matching Figma wireframe, Figma design component, WordPress block, and client-facing usage entry. The user wants the block and component library refined before designing the Floe brochure site.

The first set is Home Banner, Page Banner, Article, Image + Copy, CTA, Testimonial, Posts, Cards, Document Download, and Images. Home Banner and Page Banner are separate sections. Video is an additional YouTube section, and Spacing controls the gap after a section. See the [design brief](design-system-brief.md) for the full product rationale and [block contract](blocks.md) for current editor behavior.

## Where things are

| Item | Location / status |
| --- | --- |
| Git repository | `/home/jordan/Projects/Floe`; remote `https://github.com/jordanneenan/Floe.git`, branch `main` |
| LocalWP install | `/home/jordan/Local Sites/floe/app/public` |
| Installed theme | `wp-content/themes/floe` in that LocalWP site is a symlink to this repository |
| Local preview | `http://floe.local/floe-block-qa/` (`Floe Block Preview`, local page ID 10) |
| Current local homepage | `http://floe.local/` still shows the starter WordPress post; it is not the proposed brochure site |
| Made 4 reference | `/home/jordan/Projects/made-4` on the original machine; optional and not available in every environment |
| Figma first pass | [Floe design file](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n); access is separate from Git access |
| Logo and mark | `Assets/Brand/floe-logo.svg`, `floe-mark.svg`, `floe-site-icon.svg` and `.png` |
| Preview photos | Five architectural WebP files in `Assets/PreviewImagery/`; these are placeholder assets, not a media-library migration mechanism |

The preview page and its media attachments are stored in the **local WordPress database and uploads**, not in Git. Attachment IDs and the page ID are specific to this LocalWP site. Cloning the repository alone does not recreate the preview content. A reviewer on another machine must install the theme in WordPress and make test content, or obtain a separate site export. Never assume the LocalWP URL resolves elsewhere.

Before this documentation update on 24 September 2026, `main` was clean and **five commits ahead of `origin/main`**, with `4b9452d` as HEAD. Check `git status -sb` for the current count. A GitHub-only reviewer will not see local commits until they are pushed.

## Runtime architecture

This is a **classic PHP theme with Gutenberg custom blocks and `theme.json`**, not a Full Site Editing block theme. `header.php`, `footer.php`, and `index.php` supply templates; header, navigation, and footer markup live in `Components/`. The `theme.json` file defines editor settings and provisional neutral color/spacing presets. It does not provide Site Editor templates or editable template parts. WordPress's Site Icon and custom logo are supported; the header falls back to the theme SVG logo.

`functions.php` loads the focused `Config/` files. `Config/Theme.php` registers theme support and the primary menu. `Config/Blocks.php` registers each immediate `Blocks/*` directory containing `block.json` on `init`. `Config/Assets.php` loads root `style.css`, the shared section CSS, and the shared front-end media script. Each block's metadata declares its compiled editor JavaScript, CSS, and PHP render template. Blocks are rendered by PHP; dynamic parents with children save `InnerBlocks.Content`. Shared editor controls and PHP render helpers live in `Blocks/_shared/`.

There is **no ACF dependency or required plugin**. Native media, RichText, InnerBlocks, WordPress queries, menus, and `theme.json` are preferred. Put reusable functionality that must survive a theme change into a plugin when that need arises. See [architecture](architecture.md), [configuration](configuration.md), and [migration decisions](migration.md).

## Block inventory

There are 16 registered block types: 12 top-level sections and 4 nested helpers.

| Top-level section | Main behavior |
| --- | --- |
| `floe/home-banner` | Large split opening with copy, action, and image or looping MP4 |
| `floe/page-banner` | Separate compact page opening with optional breadcrumb, action, media layout, and surface |
| `floe/article` | Native inner blocks, optional button underneath, surface choice |
| `floe/image-copy` | Fixed media/text pair, optional action, media left or right |
| `floe/cta` | Focused title, copy, optional action, surface choice |
| `floe/testimonial` | Quote, attribution, optional image or looping MP4 |
| `floe/posts` | Published post query: latest, category, or selected IDs; count 1–12; per-post media overrides |
| `floe/cards` | Repeatable manually entered cards using shared presentation |
| `floe/document-download` | Repeatable media-library file entries |
| `floe/images` | Repeatable rows of one, two, or three visual slots |
| `floe/video` | YouTube URL with cover and visitor-triggered, privacy-enhanced player |
| `floe/spacing` | Large, Medium, Small, None, or custom gap at four breakpoints |

Nested helpers: `floe/card-item`, `floe/download-item`, `floe/image-row`, and `floe/media`. Images rows offer a gap toggle and cover/natural fit. The Media helper can also be used inside Article. The [block contract](blocks.md) details fields, responsive spacing values, and media behavior; individual `block.json` files are the exact attribute schemas.

Every Floe section has a default bottom gap. A directly following Spacing block removes that gap through the shared CSS sibling selector and supplies its own height. Preset/custom values cover large desktop (≥1280px), desktop (768–1279px), tablet (550–767px), and mobile (<550px). This depends on the rendered blocks remaining direct siblings.

Most visual media slots accept a WordPress image or a media-library MP4. MP4 requires a poster, loops muted without controls, plays only near the viewport, and pauses for reduced-motion preferences. `floe/video` is a separate YouTube cover and play interaction: its iframe is created only after a click. Media and card rendering helpers are in `Blocks/_shared/render.php`; the front-end behavior is in `Blocks/_shared/Assets/media.js`.

## Build and verification

Requirements: WordPress 6.6+, PHP 8+, Node 20+ and npm for development. Built assets are committed, so a deployed theme does not need Node.

```sh
cd /home/jordan/Projects/Floe
npm ci
npm run build
git diff --check
git status -sb
```

`npm run build:js` and `npm run build:css` are available for focused work. There is no watch script. `scripts/build.mjs` discovers immediate block folders from `block.json`. WordPress Scripts clears a block's `Assets/` directory during its JS build, so the script runs all JS builds, then all Sass builds; even `build:js` rebuilds CSS. Commit changed source **and** generated `Assets/` files. Validate PHP syntax when changing PHP and inspect the actual editor and frontend when changing block behavior. See [workflow](workflow.md).

The local preview URL returned HTTP 200 on 24 September 2026 and rendered all 11 visual sections; Spacing also renders as a gap element. Previous work reported a successful build, PHP syntax checks, 16-block registration, and desktop/mobile frontend review. Those are historical checks, not proof that a new change passed. **The Gutenberg editor UI has not been visually verified in the current review record**; test insertion, controls, save/reopen, nested blocks, and frontend rendering before claiming it is complete. There is no automated test suite in this repository.

## Design status and next decisions

The Figma file has first-pass 1440px wireframes and designed components for the block library, plus shared Button, Navigation, Header, Footer, Media slot, Content card, File action, and Video play control components. Figma main-component edits update Figma instances; they do **not** update WordPress code automatically. The brochure page in Figma is reserved for a later phase, and the local WordPress homepage is not that brochure site.

The current theme implements an initial interpretation of the designs. Tablet, mobile, large-desktop, absent-field, long-content, accessibility-state, and approved variant designs still need review. The neutral palette and background surfaces are provisional. Posts source controls, Images row controls, looping video behavior, and Article's Media helper have implementations, but the design and client editing model are not finally approved. Client-facing block documentation has not been produced.

## Suggested independent review

Please inspect the code directly and report findings with file paths, impact, and a reproduction path. Prioritize:

1. Gutenberg editor behavior: insertion, nested block restrictions, media selection/removal, saving/reopening, and editor/front-end parity.
2. PHP render safety and WordPress API correctness, especially query behavior, media IDs, escaping, semantic heading structure, and block attributes.
3. Responsive behavior, keyboard/focus handling, reduced motion, missing content, and long content across all four intended ranges.
4. Build reproducibility and whether every metadata asset path points to committed output.
5. Gaps between the user's controlled-section editing model, Figma components, and the current blocks. Distinguish defects from choices awaiting design approval.

Do not treat a Made 4 pattern or this handoff as proof of current behavior. State which checks actually ran and which require a WordPress login, a local database, or Figma access.
