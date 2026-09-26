# Floe review handoff

This is the starting point for reviewing Floe without the original conversation. Read [AGENTS.md](../AGENTS.md) for contribution rules and the [build brief](build-brief.md) for the plan, then use the source, `block.json` files and module READMEs to verify claims. The current user request takes precedence over this document.

## Project and intent

Floe is an open-source, GPL-2.0-or-later WordPress theme (`floe` text domain and block namespace), the successor to Jordan's Made platform. It is a library of complete, designed page sections: an editor chooses a section, enters content and gets a controlled layout. Article is the exception, offering native blocks within a designed section. Each client site re-brands through `theme.json` tokens.

## Where things are

| Item | Location / status |
| --- | --- |
| Git repository and installed theme | `~/Local Sites/floe/app/public/wp-content/themes/floe` (the only copy); remote `https://github.com/jordanneenan/Floe.git` |
| LocalWP site | `~/Local Sites/floe/app/public`, `http://floe.local` |
| Brochure pages | `/`, `/platform/`, `/pricing/`, `/about/`, `/contact/`, `/journal/`, built from blocks (Journal is a normal page with the Posts block) |
| Block Preview | `http://floe.local/block-preview/`: every section with sample content |
| Made reference | Read-only theme beside Floe at `wp-content/themes/made`. See [Made notes](made-notes.md) |
| Figma | [Floe design file](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n); node IDs in the build brief |
| Preview imagery | Photos are uploaded to the media library (not in git); placeholder logos in `assets/PreviewImagery/logos/` |

Preview content lives in the local database; it isn't in git.

## Build status (September 2026)

Phases 0–7 of the brief are built, each on its own branch (`phase-0-baseline` … `phase-7-qa-docs`), stacked in order, plus `phase-8-simplify` (Jordan's first review: lowercase layout, `includes/`, no patterns or scripts, 860px left-aligned column, Journal as a normal page, the new Posts block and the multi-panel CTA). Then `phase-9-dark-motion`: dark mode with a footer toggle, the 0.4s motion timing, scroll reveals and small animations (D40, D41). All await review before merging to `main`. See the [architecture](architecture.md) and [block library](blocks.md).

Verified on floe.local:
- `npm run build`, `npm run lint` (JS and SCSS) and `php -l` on every PHP file pass.
- Every page renders at 375 and 1440 with no console errors, no horizontal scrolling, one H1 and an ordered heading outline.
- Keyboard pass: every tab stop on Home and Platform has a visible focus style; the mobile menu disclosure opens, reports `aria-expanded` and closes on Escape with focus returned.
- Long-content check (long words, URLs, button labels) at 375 and 1440.
- Editor: WordPress's real editor scripts rendered the Block Preview content in a standalone test page: all 30 blocks registered, every block (including generated core markup) valid, no JavaScript errors. This was not the logged-in wp-admin editor.

Not yet checked by a person:
- The logged-in block editor in wp-admin: inserting blocks, the link picker, media selection, saving and reopening.
- Screen reader walkthroughs (only structure was checked).
- Forms: Contact and Newsletter need the site's form plugin in their form slots.

## Review priorities

1. Editor behaviour in wp-admin: insertion (Floe sections only at the top level), nested restrictions, media and link editing, save/reopen, editor/front-end parity.
2. Decisions in [decisions.md](decisions.md) marked Open or made by judgement.
3. Responsive behaviour at 550–767 (tablet), which Figma doesn't cover.
4. Copy written to fill gaps in Figma (FAQ answers, sample posts, Privacy and Accessibility placeholder pages).
