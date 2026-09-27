# Agent workflow and verification

## Environment

Work at the repository root. The repository is the theme folder inside the LocalWP site, `~/Local Sites/floe/app/public/wp-content/themes/floe`, served at `http://floe.local`. It is the only copy, with no separate checkout and no symlink. `Local Sites` contains a space, so quote paths. Install with `npm ci` using Node.js 20 or newer. PHP and a WordPress site are needed for runtime testing; this repository does not provision them. Production deployments use committed assets and do not run npm.

floe.local runs the `dev` branch, and that's where Jordan reviews work. Start each branch (one per phase of the [build brief](build-brief.md) or per feature) from `origin/dev`. Once the work is built and verified, land it on `dev` yourself, without waiting to be asked (D71):

1. Fetch `origin/dev` and merge it into your branch if it has moved. Rebuild compiled files rather than hand-merging their conflicts, renumber your `D<n>` in `decisions.md` if another branch took it, and verify again.
2. Push the branch and open a pull request into `dev` saying what changed, how it was verified and what Jordan should check.
3. Merge the pull request (a merge commit) once GitHub reports it mergeable.
4. Update floe.local: in the shared checkout, run `git pull --ff-only origin dev` if it's on `dev` with no uncommitted changes. If another session has it on a different branch or has work in progress there, leave it alone and tell Jordan the change is on `dev` but not showing on floe.local yet.
5. Tell Jordan what to look at on floe.local.

Jordan's review happens on floe.local, and anything he wants changed comes as a new pull request. Hold a pull request open only when Jordan asks you to. Merging `dev` into `main` is different: it's the release, it syncs to the live site, and it happens only when Jordan asks. Nothing goes to `main` except from `dev` (D51).

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

When a theme change alters how existing content is stored, a one-off migration goes in `scripts/` and is run once on each site after deploying, e.g. `wp eval-file scripts/migrate-to-block-intro.php` (dry run) then `… apply`. Each script says what it changes and is safe to run twice.

Content moves between floe.local and the live site (floewp.com) with `bin/floe-sync`, never by hand. See [Live site](live-site.md).

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
