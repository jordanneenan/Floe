# Floe documentation for AI agents

**Purpose:** a fast, explicit map for agents implementing changes in Floe. This is working documentation, not marketing copy.

## Read in this order

1. [Build brief](build-brief.md): the working brief and phase plan for building Floe from the Figma designs.
2. [Decisions](decisions.md) for choices made where the brief is silent, and [Made notes](made-notes.md) for how Made (the default reference) works.
3. This page for project identity and task routing.
4. [Architecture](architecture.md) for the module system, assets, styling and editor model.
5. The task-specific page below, the module's own README, and the source files before editing. If documentation and code differ, treat code as the current behaviour and correct the documentation in the same change.

| Task | Read | Primary files |
| --- | --- | --- |
| Add or change a block | [Block library](blocks.md), the block's README | `blocks/<name>/` |
| Add or change a component | [Architecture](architecture.md#modules), the component's README | `components/<name>/` |
| Change tokens, type, surfaces, breakpoints | [Architecture](architecture.md#styling-system) | `theme.json`, `assets/scss/` |
| Change editor lockdown or allowed blocks | [Configuration map](configuration.md) | `includes/editor.php`, block `allowedBlocks` |
| Change admin behaviour or site-wide tweaks | [Configuration map](configuration.md) | `includes/admin/`, `includes/media/` |
| Change the template or page titles | [Architecture](architecture.md#templates) | `index.php`, `includes/templates.php` |
| Follow or port a Made pattern | [Made notes](made-notes.md), [Decisions](decisions.md), [Migration](migration.md) | `../made/` (read-only reference theme) |
| Record a choice the brief doesn't cover | [Decisions](decisions.md) | |
| Build, verify, or release | [Workflow](workflow.md) | `package.json`, `build.mjs` |
| Older review context | [Review handoff](agent-handoff.md) | |

## Project facts

- Name: **Floe**. Theme text domain and block namespace: `floe`.
- Repository: `https://github.com/jordanneenan/Floe`; default branch `main`; one branch per phase.
- Type: classic WordPress theme with block editor support and `theme.json`, not a full-site-editing block theme.
- Minimum: WordPress 7.1, PHP 8.0, Node.js 20 for development. Production uses committed built assets and doesn't need Node.
- License: GPL-2.0-or-later.
- 22 sections plus 10 child blocks and 15 components. No required plugins and no ACF.

## Source-of-truth order

For implementation facts, use: **current code and `block.json` → package scripts / WordPress metadata → module READMEs → these docs → root README**. For what to build, use: **Jordan's current instruction → the build brief → what Made does → judgement**, with native WordPress first, and record the last two in [decisions](decisions.md).

## Change checklist

1. Locate the owning module or `includes/` file.
2. Keep the one-folder-per-module layout and the name-matching convention.
3. `npm run build`, `npm run lint`, `php -l` on changed PHP.
4. Check the front end at the four breakpoints and the editor.
5. Update the module README and any affected documentation.
