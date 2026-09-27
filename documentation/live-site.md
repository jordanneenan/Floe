# Live site: deploying code and syncing content

Floe runs in two places:

| | Local | Live |
| --- | --- | --- |
| Address | `http://floe.local` | `https://floewp.com` |
| Runs in | Local (LocalWP) on Jordan's machine | Docker on the TrueNAS box (`ssh truenas`), TrueNAS app `floe` |
| Files | `~/Local Sites/floe/app/public` | `/mnt/ssd_pool/apps/floe/wordpress` |

Code and content take different routes to live.

- **Theme code** (this repository) goes live in releases. Feature pull requests merge into `dev`, which floe.local runs. Merging `dev` into `main` is a release, and `main` changes only then. Within a minute of `main` moving, `/mnt/ssd_pool/scripts/floe-deploy.sh` (TrueNAS cron) copies `main` into the live theme folder, checks the site responds and rolls back if it doesn't. Its log is `/mnt/ssd_pool/apps/floe/deploy/deploy.log`. `bin/` is left out of the live theme.
- **Content** (pages, posts, menus, media, settings: everything in the database and `wp-content/uploads`) moves with **`bin/floe-sync`**, described below.

**Live is the source of truth for content.** Work on local, then push to live. Before starting new content work, pull live down so local is current.

## Agent rules

Follow these without asking Jordan how to sync. The script enforces most of them itself.

1. **Start with `bin/floe-sync status`.** It lists what changed on each side since the last sync and how local differs from live. It changes nothing.
2. **Pull before content work.** When live has changes local doesn't, run `bin/floe-sync pull`. If local has unpushed changes, pull refuses and lists them. Don't add `--force` unless Jordan agrees to lose those changes.
3. **A push needs Jordan's yes.** Run `bin/floe-sync push` first. Without a terminal it only prints the plan: every page, menu item, attachment and setting that live will gain (`+`), change (`~`) or lose (`-`), plus how many media files it will upload. Show Jordan that plan and wait for a clear yes. Then run `bin/floe-sync push --yes`.
4. **Never add `--force` on your own.** Push refuses when live has changed since the last sync, because the push would overwrite those changes. It lists them. Only Jordan can decide they can be lost. Also stop at the first push after the sync state is lost (the `No sync recorded yet` message). There, show Jordan the plan: any `~` item might be a live edit.
5. **Release code before content that needs it.** floe.local runs `dev`, so local content can use blocks or behaviour that live doesn't have yet. The push plan (and `status`) ends with a `Theme:` line listing commits floe.local has that live hasn't released. If the content depends on any of them, don't push. Tell Jordan it needs a release first (`dev` merged into `main`, then the deploy log showing it). Push once live has the release. Otherwise live shows the new blocks as invalid.
6. **Content migrations come with releases.** When a release changes how existing content is stored, it ships a one-off script in `scripts/` (see [workflow](workflow.md#preview-content)). floe.local gets it when the change lands on `dev`. Live needs it once, right after the release deploys. While Jordan is the only editor, the simplest way is to push floe.local's already-migrated content straight after the release. When a push isn't wanted, run the script on live instead, dry run first, after Jordan agrees:

   ```sh
   ssh truenas 'sudo docker run --rm --volumes-from ix-floe-wordpress-1 --network ix-floe_default --env-file /mnt/ssd_pool/apps/floe/secrets.env --user 33:33 wordpress:cli-2.12 wp eval-file wp-content/themes/floe/scripts/<script>.php'
   ```

   Add `apply` after the script path to make the changes. A script run doesn't back up live the way a push does, so first run the nightly backup by hand: `ssh truenas sudo /mnt/ssd_pool/scripts/floe-backup.sh`.
7. **Check the "matches" line.** Every pull and push ends with `Local now matches live.` / `Live now matches local.`, or a list of what still differs. Pass that on to Jordan with the plan you pushed. Also mention anything to check on floewp.com.
8. **The script is on `dev`.** Parallel sessions switch branches in the shared theme folder, so the checked-out branch may predate `bin/floe-sync`. If it's missing, run it from a worktree of `dev`, for example `git worktree add /tmp/floe-dev origin/dev` and then `/tmp/floe-dev/bin/floe-sync status`. It works from any folder because the site paths are fixed.

`bin/floe-sync help` prints the command summary.

## Commands

```sh
bin/floe-sync status                 # what differs; changes nothing
bin/floe-sync pull                   # live → local
bin/floe-sync push                   # local → live: prints the plan (asks y/N in a terminal)
bin/floe-sync push --yes             # local → live, after Jordan approved the plan
bin/floe-sync push --dry-run         # the plan only, even in a terminal
bin/floe-sync backups                # database backups on both sides
bin/floe-sync restore local <file>   # put a local-… backup back on floe.local
bin/floe-sync restore live <file> --yes   # put a live backup back on floewp.com
```

`--force` exists on `pull` and `push` for the cases in the rules above.

Local must be running the Floe site, and `ssh truenas` must work. The script uses Local's own PHP, MySQL client and WP-CLI (found from `~/.config/Local/sites.json`), `jq`, and `rsync`.

## What a push does

1. Fingerprints both sites and prints the plan. Stops if live has changed since the last sync, unless `--force`.
2. Uploads media: copies new and changed files from local `wp-content/uploads` to live. Nothing is deleted on live. A live file that gets replaced is kept in `backups/uploads-replaced-<time>/`.
3. Exports the local database without the user tables. Keeps a copy in `~/Local Sites/floe/sync/backups/local-pushed-<time>.sql.gz` and uploads it.
4. On live: backs up the database (`backups/pre-push-<time>.sql.gz`) and saves the enquiries (`backups/enquiries-pre-push-<time>.json`). Then it imports, rewrites `http://floe.local` to `https://floewp.com` (including JSON-escaped and serialized copies), puts the enquiries back and flushes caches.
5. Checks the live home page returns 200. If it doesn't, restores the pre-push backup automatically and reports the failure. Media uploaded in step 2 stays, since uploading is additive.
6. Fingerprints live again, confirms it matches local and records this as the last sync.

**What stays on live:** user accounts and their passwords, sessions and settings (`wp_users`, `wp_usermeta`), and form enquiries. If an imported page already uses an enquiry's ID, the enquiry gets a new ID.

**What a push replaces:** everything else in the database, including settings and the list of active plugins. While there's a single editor that's what we want. See [limits](#limits-and-next-steps).

## What a pull does

1. Fingerprints both sites. Stops if local has changes that aren't on live, unless `--force`.
2. Backs up the local database (`sync/backups/local-pre-pull-<time>.sql.gz`) and sets aside the local user tables.
3. Downloads the whole live database. It's kept as `sync/backups/live-<time>.sql.gz`, which also gives an off-server copy of live. Then it downloads new and changed uploads, keeping any local file it replaces in `sync/backups/uploads-replaced-<time>/`.
4. Imports into floe.local, restores the local user tables, rewrites `https://floewp.com` to `http://floe.local` and deletes the enquiries (personal data stays on the server) and flushes caches.
5. Confirms local matches live and records the sync.

## How it knows what changed

`bin/sync/fingerprint.php` hashes every post (with its meta), term and setting. It leaves out users, enquiries, revisions, transients, cron and other values that change by themselves. The site address becomes `{home}` before hashing, so identical content hashes the same on both sites. After each sync the fingerprint is saved as `~/Local Sites/floe/sync/state/last-sync.json`, the baseline. Comparing each side with the baseline shows who changed what. Comparing the two sides shows what a push or pull would do. `bin/sync/compare.php` prints the differences.

The baseline lives only on Jordan's machine. If it's deleted, the script treats the next push or pull as unknown and wants `--force` whenever the sites differ.

## Backups and undo

| Where | What | Kept |
| --- | --- | --- |
| Live `/mnt/ssd_pool/apps/floe/backups/nightly-*.sql.gz` | Whole live database, 02:00 every night (`/mnt/ssd_pool/scripts/floe-backup.sh`, TrueNAS cron) | Every day for 7 days, weekly for 3 months, monthly for 12 months, one per year forever |
| Live `backups/pre-push-*`, `pre-restore-*`, `enquiries-pre-push-*`, `uploads-replaced-*` | Made by floe-sync before it overwrites anything | 90 days (pruned by the nightly job) |
| Local `~/Local Sites/floe/sync/backups/` | `local-pre-pull-*`, `local-pre-restore-*`, `local-pushed-*`, `live-*` (whole live DB from each pull), `uploads-replaced-*` | Newest 60 database files and 10 upload folders |

Hand-made files in the live backups folder (such as `before-forms-….sql`) are never pruned. The nightly job logs to `backups/backup.log`.

To undo a push, run `bin/floe-sync backups` and then `bin/floe-sync restore live pre-push-<time>.sql.gz --yes`, with Jordan's agreement. Restoring takes a `pre-restore-` backup first. A restored live database won't match the last sync, so the next push will report live as changed. That's expected. To undo a pull, run `bin/floe-sync restore local local-pre-pull-<time>.sql.gz`.

`bin/server/floe-backup.sh` is the source of the nightly job. After changing it, reinstall it:

```sh
scp bin/server/floe-backup.sh truenas:/tmp/ && ssh truenas 'sudo install -m 755 /tmp/floe-backup.sh /mnt/ssd_pool/scripts/ && rm /tmp/floe-backup.sh'
```

## Files

| File | Runs | Does |
| --- | --- | --- |
| `bin/floe-sync` | Jordan's machine | The command. Drives Local's WP-CLI, ssh and rsync. |
| `bin/sync/remote.sh` | TrueNAS, as root | Live side: fingerprint, export, backup, import, restore. Copied to `/mnt/ssd_pool/apps/floe/sync/lib/` on every run, so edit it here, never on the server. Runs WP-CLI in a throwaway `wordpress:cli` container that shares the live site's files and database. |
| `bin/sync/fingerprint.php` | Both sites (`wp eval-file`) | Content fingerprint. |
| `bin/sync/compare.php` | Jordan's machine | Differences between two fingerprints. |
| `bin/sync/enquiries.php` | Both sites (`wp eval-file`) | Saves, removes and restores enquiries. |
| `bin/server/floe-backup.sh` | TrueNAS cron | Nightly backup and retention. |

## Troubleshooting

- **"floe.local isn't running"**: start the Floe site in Local.
- **"can't reach the live server"**: check `ssh truenas` works (see Jordan's ssh config) and the TrueNAS box is up.
- **"another floe-sync is already running"**: another session is syncing. Wait for it; never run two at once.
- **Push or pull ends with "still differs here"**: the listed items didn't come across as expected. Usually a setting WordPress rewrote on its own after the import. Compare the two sites for those items before telling Jordan it's done.
- **A setting keeps showing as changed with no one editing it**: WordPress or a plugin is updating it by itself. Add its name to the skip list in `fingerprint.php` (`$floe_skip_opts`).

## Testing changes to the sync

Don't test sync changes against the real sites. The `FLOE_*` variables point both ends at copies. This is how the script was tested:

- `FLOE_LOCAL_WP`: a copy of `app/public` whose `wp-config.php` uses another database in Local's MySQL.
- `FLOE_SYNC_DIR`: a scratch state folder, so the real baseline isn't touched.
- `FLOE_REMOTE_ENV`: variables for `remote.sh`, e.g. `FLOE_WP_CONTAINER=floe-sync-test FLOE_DB_NAME=floe_synctest FLOE_HEALTH_URL=http://localhost:8091/ FLOE_BACKUPS=/mnt/ssd_pool/apps/floe/sync-test/backups`. The container is a `wordpress` container on port 8091 serving a copy of the live files, pointed at a copy of the live database.

Remove the test container and database afterwards.

## Limits and next steps

- **Single editor.** A push replaces live's whole database apart from users and enquiries. That's fine while Jordan is the only editor, and the live-changes check stops accidental overwrites. Once other people edit live, whole-database pushes should stop. The next step is `push --page <slug>`, which sends one page and its media, matched by slug because IDs will no longer line up.
- **Plugins and settings.** A plugin activated only on live would be switched off by a push, and its settings overwritten. Install plugins on both sites, or pull after installing on live.
- **Uploads are only added.** Deleting media in WordPress deletes the database record everywhere on the next sync, but the files stay on the other site's disk.
- **Upload backups.** The nightly job backs up the database only. Uploads exist on live and, after each pull, on Jordan's machine.
