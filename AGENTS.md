# Floe agent entry point

Floe is a modular WordPress theme. **For build work, start with [Documentation/build-brief.md](Documentation/build-brief.md).** Then read [Documentation/README.md](Documentation/README.md), which routes tasks to the relevant documentation and source files. [Documentation/agent-handoff.md](Documentation/agent-handoff.md) is the older reviewer handoff.

Project rules:

- The project name and text domain are **Floe** and `floe`.
- **Native WordPress first.** If WordPress can do it natively, use that. Otherwise it goes in the theme. Only if it can't reasonably live in the theme does it become a small plugin, and ask Jordan first. No ACF dependency.
- **Everything is a module.** Each block is one self-contained folder under `Blocks/`, and each reusable UI piece is one self-contained folder under `Components/`. Adding a folder adds the feature; deleting it removes the feature completely. Never add a central list of block or component names.
- **Made is the default reference.** Where the build brief doesn't specify something, follow how Made (Jordan's previous platform) does it and record the choice in `Documentation/decisions.md`. Adapt its patterns rather than copying code wholesale.
- Keep `functions.php` as a loader, with theme configuration in `Config/`.
- Commit source and generated build output together, and keep `block.json` asset paths aligned with the compiled files.
- Work on a branch per phase, make small focused commits, and stop for Jordan's review before merging to `main`.
- Update the relevant `Documentation/` page when changing an architecture rule or public editing behaviour.

These instructions supplement the user's current request. Current source code is the authority for how the implementation actually behaves.
