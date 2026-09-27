# Claude Code instructions for Floe

Read `AGENTS.md`, then `documentation/build-brief.md`, before doing anything else.

- This repository is the Floe theme inside the LocalWP site: `~/Local Sites/floe/app/public/wp-content/themes/floe` (`http://floe.local`). It is the only copy of the project, with no separate `~/Projects/Floe` and no symlink. Changes are live after `npm run build`.
- `Local Sites` contains a space, so quote paths in every shell command and script.
- Work phase by phase as the build brief describes: one branch per phase or feature, started from `origin/dev`, pushed to GitHub with a pull request into `dev`, then stop for Jordan's review. floe.local runs `dev`; merging `dev` into `main` is the release to the live site.
- Where the brief is silent, choose the native WordPress approach and record the choice in `documentation/decisions.md`.
- Before reporting a change as done, build it, lint it, check PHP syntax and look at it on `floe.local`. Say what you verified and what Jordan still needs to check in the browser or editor.
- **Going live:** feature PRs merge into `dev`, which floe.local runs. Merging `dev` into `main` is a release, and live (https://floewp.com) deploys `main` automatically. Content (database and uploads) moves only with `bin/floe-sync`: `status`, `pull`, `push`. Live is the source of truth. Read `documentation/live-site.md` and follow its agent rules. Never push without showing Jordan the plan and getting a yes, and never use `--force` without Jordan's agreement.
