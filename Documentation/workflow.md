# Agent workflow and verification

## Environment

Work at the repository root. The repository is the theme folder inside the LocalWP site, `~/Local Sites/floe/app/public/wp-content/themes/floe`, served at `http://floe.local`. It is the only copy, with no separate checkout and no symlink. `Local Sites` contains a space, so quote paths. Install with `npm ci` using Node.js 20 or newer. PHP and a WordPress site are needed for runtime testing; this repository does not provision them. Production deployments use committed assets and do not run npm.

Work on one branch per phase of the [build brief](build-brief.md) (`phase-1-foundations`, …), push it, and stop for Jordan's review before merging to `main`.

### WP-CLI

Local's "Open site shell" provides `wp` ready to use. From a normal terminal on Jordan's Linux machine, PHP and WP-CLI are not on the PATH, so use Local's bundled copies. The site must be running in Local for any command that reaches the database. The site ID (`XrngiJY5d`) and PHP version come from `~/.config/Local/sites.json`.

```sh
LOCAL_PHP="$HOME/.config/Local/lightning-services/php-8.5.3+1/bin/linux"
LD_LIBRARY_PATH="$LOCAL_PHP/shared-libs" "$LOCAL_PHP/bin/php" \
  -c "$HOME/.config/Local/run/XrngiJY5d/conf/php/php.ini" \
  /opt/Local/resources/extraResources/bin/wp-cli/wp-cli.phar \
  --path="$HOME/Local Sites/floe/app/public" theme list
```

The same `LD_LIBRARY_PATH=… php -l file.php` gives a PHP syntax check without a system PHP.

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
