# Floe agent entry point

Floe is a WordPress theme. Read [Documentation/README.md](Documentation/README.md) before changing code. That file routes tasks to the relevant documentation and source files.

Project rules:

- The project name and text domain are **Floe** and `floe`.
- Prefer native WordPress features. Add custom theme code only for behavior WordPress does not already cover; use a plugin for reusable site functionality that should survive a theme change.
- Do not add an ACF dependency or copy Made 4 wholesale. Made 4 is historical reference material, not build input.
- Keep `functions.php` as a loader. Put theme configuration in `Config/`, reusable template parts in `Components/`, and each custom block directly in one folder under `Blocks/`.
- When changing a block's source JS or SCSS, regenerate and commit its `Assets/` output. Keep `block.json` paths and compiled filenames aligned.
- Update the relevant `Documentation/` page when changing an architecture rule or public editing behavior.

These instructions supplement the user's current request. Current source code is the authority for how the implementation actually behaves.
