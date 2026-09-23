# Agent workflow and verification

## Environment

Work at the repository root. Install with `npm ci` using Node.js 20 or newer. PHP and a WordPress site are needed for runtime testing; this repository does not provision them. Production deployments use committed assets and do not run npm.

## Common commands

```sh
npm ci
npm run build
npm run build:js
npm run build:css
git diff --check
git status --short
```

`npm run build` discovers every immediate `Blocks/*/block.json` and compiles a matching `<slug>.js` and `<slug>.scss` into that block's `Assets/`. It also compiles the shared section CSS. `build:css` compiles only styles. `build:js` recompiles styles after JavaScript because `wp-scripts` clears each block's output directory during a JavaScript build.

## Focused verification by change

| Change | Minimum check |
| --- | --- |
| Documentation only | Links and referenced paths resolve; `git diff --check`; compare claims to current source. |
| SCSS or JS | `npm run build`; inspect `Assets/` diff and `block.json` paths. |
| PHP | PHP syntax check when a PHP runtime is available; inspect hooks, escaping, and template output. |
| Block behavior | Build, then insert/edit/render the block in WordPress when a site is available. Check editor and frontend at desktop, tablet, and mobile widths. |
| `theme.json` | Parse JSON, then inspect editor settings in WordPress when available. |

Do not report a live WordPress or browser test unless it actually ran. A JavaScript asset build and static PHP parse cannot prove WordPress registration or UI behavior.

## Completion and docs upkeep

- Keep changes scoped; update the owning documentation page when the contract changes.
- Commit generated `Assets/` files with their sources so the theme works without a build step on another computer.
- Confirm `git status` is clean after committing and that the intended remote branch contains the commit after pushing.
- Record environment limitations in the handoff instead of implying a check passed.
