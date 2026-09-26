# Claude Code instructions for Floe

Read `AGENTS.md`, then `documentation/build-brief.md`, before doing anything else.

- This repository is the Floe theme inside the LocalWP site: `~/Local Sites/floe/app/public/wp-content/themes/floe` (`http://floe.local`). It is the only copy of the project, with no separate `~/Projects/Floe` and no symlink. Changes are live after `npm run build`.
- `Local Sites` contains a space, so quote paths in every shell command and script.
- Work phase by phase as the build brief describes: one branch per phase, pushed to GitHub, then stop for Jordan's review.
- Where the brief is silent, follow Made and record the choice in `documentation/decisions.md`. Made is a reference-only theme in the same site, in `../` beside this theme folder. Read it; never edit, activate, commit or depend on it.
- Before reporting a change as done, build it, lint it, check PHP syntax and look at it on `floe.local`. Say what you verified and what Jordan still needs to check in the browser or editor.
