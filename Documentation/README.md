# Floe documentation for AI agents

**Purpose:** a fast, explicit map for agents implementing changes in Floe. This is working documentation, not marketing copy.

## Read in this order

1. [Build brief](build-brief.md), the working brief and phase plan for building Floe from the Figma designs.
2. [Decisions](decisions.md) for choices made where the brief is silent, and [Made notes](made-notes.md) for how Made (the default reference) works.
3. This page for project identity and task routing.
4. [Architecture](architecture.md) for the runtime path and file ownership.
5. The task-specific page below and the actual source files before editing. If documentation and code differ, treat code as the current behavior and correct the documentation in the same change.

| Task | Read | Primary files |
| --- | --- | --- |
| Add or change a block | [Block contract](blocks.md) | `Blocks/<Name>/`, `Config/Blocks.php`, `package.json` |
| Change admin UI | [Configuration map](configuration.md) | `Config/AdminUI.php` |
| Change theme setup, menus, styles, comments | [Configuration map](configuration.md) | `Config/Theme.php`, `Config/Assets.php`, `Config/Comments.php`, `theme.json` |
| Change page structure | [Architecture](architecture.md) | `index.php`, `header.php`, `footer.php`, `Components/` |
| Follow or port a Made pattern | [Made notes](made-notes.md), [Decisions](decisions.md), [Migration](migration.md) | `../made/` (read-only reference theme) |
| Record a choice the brief doesn't cover | [Decisions](decisions.md) | |
| Older review context | [Review handoff](agent-handoff.md) | |
| Design the block and component library | [Design-system brief](design-system-brief.md) | Figma block wireframes, designs, and shared components |
| Build, verify, or release | [Workflow](workflow.md) | `package.json`, `README.md`, Git history |

## Project facts

- Name: **Floe**. Theme text domain and custom block namespace: `floe`.
- Repository: `https://github.com/jordanneenan/Floe`; default branch: `main`.
- Type: classic WordPress theme with block editor support and `theme.json`, not a full-site-editing block theme.
- Minimum: WordPress 6.6, PHP 8.0, Node.js 20 for development. Production uses committed built assets and does not need Node.
- License: GPL-2.0-or-later.
- Custom blocks: Home Banner, Page Banner, Article, Image + Copy, CTA, Testimonial, Posts, Cards, Document Download, Images, Video, and Spacing. Cards, downloads, images, and Article use nested helper blocks. There are no required plugins and no ACF dependency.

## Source-of-truth order

For implementation facts, use: **current code and `block.json` → package scripts / WordPress metadata → these docs → root README**. For what to build, use: **Jordan's current instruction → the build brief → what Made does → judgement**, with native WordPress first, and record the last two in [decisions](decisions.md). Do not infer that a Made feature exists in Floe because it is mentioned as a reference.

## Change checklist

1. Locate the owning file using the task table.
2. Preserve WordPress APIs and the one-folder-per-block layout.
3. Rebuild changed block assets and validate paths.
4. Run the focused checks in [Workflow](workflow.md).
5. Update only documentation affected by the change.
