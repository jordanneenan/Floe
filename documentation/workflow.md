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
npm run build         # every module, global CSS and fonts
npm run build:css     # styles only
npm run build:js      # scripts only
npm run start         # build, watch, recompile on save, live-reload through BrowserSync
npm run lint          # lint:js (WordPress ESLint rules) and lint:css (stylelint)
git diff --check
git status --short
```

`npm run start` proxies `http://floe.local` by default; set `FLOE_PROXY` for another URL. It prints an **External** address (e.g. `http://192.168.86.41:3000`): open that on a phone or another computer on the same network to browse the site, with links rewritten to that address and live reload on save. Nothing needs setting up on the other device; if it can't connect, allow port 3000 through this machine's firewall. Restart it after adding or removing a module folder.

## Preview content

The preview site's pages, posts and menus live in the LocalWP database and are built from blocks in the editor, like any client site. There are no theme patterns or seed scripts. Pages worth keeping as starting points can be saved as synced or unsynced patterns in WordPress itself.

## Focused verification by change

| Change | Minimum check |
| --- | --- |
| Documentation only | Links and referenced paths resolve; `git diff --check`; compare claims to current source. |
| SCSS or JS | `npm run build`; inspect the `assets/` diff and `block.json` paths. |
| PHP | `php -l` on changed files (see WP-CLI above for Local's PHP); inspect hooks, escaping and template output. |
| Block behavior | Build, then insert/edit/render the block in WordPress. Check the editor and the front end at 375, 600, 1024 and 1440. |
| `theme.json` | Parse JSON, then inspect editor settings in WordPress when available. |

Do not report a live WordPress or browser test unless it actually ran. A JavaScript asset build and static PHP parse cannot prove WordPress registration or UI behavior.

## Completion and docs upkeep

- Keep changes scoped; update the owning documentation page when the contract changes.
- Commit generated `assets/` files with their sources so the theme works without a build step on another computer.
- Confirm `git status` is clean after committing and that the intended remote branch contains the commit after pushing.
- Record environment limitations in the handoff instead of implying a check passed.
