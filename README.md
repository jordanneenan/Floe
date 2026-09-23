# Floe

Floe is an open-source WordPress framework theme inspired by Made 4. It starts with native WordPress features and adds custom blocks only where a specific design system needs them.

AI agents and contributors should start with [Documentation/README.md](Documentation/README.md) for the project map, block contract, migration decisions, and verification workflow.

## Requirements

- WordPress 6.6 or newer
- PHP 8.0 or newer
- Node.js 20 or newer and npm for development

No ACF installation is required.

## Structure

```text
Blocks/
  Spacing/
    block.json       WordPress block metadata
    spacing.php      server render template
    spacing.scss     source styles
    spacing.js       editor source
    Assets/          built JavaScript, asset manifest, and CSS
Components/
  Header/
  Footer/
Config/
  Theme.php         theme supports and navigation
  Assets.php        global styles
  Blocks.php        one-level block discovery and registration
  Comments.php      comments policy
  AdminUI.php       admin menu and toolbar adjustments
functions.php        small loader
theme.json           native editor design settings
```

Each block lives directly under `Blocks/`. The folder name and the `name` in its `block.json` must be unique; variants can use names such as `SpacingV2` and `floe/spacing-v2`.

## Development

Install this repository in `wp-content/themes/floe` and activate **Floe**. Built assets are committed so a production site does not need Node.js.

```sh
npm ci
npm run build
```

For development, run `npm run watch:js` and `npm run watch:css` in separate terminals.

To add a block, create `Blocks/YourBlock/block.json` and its source files. `Config/Blocks.php` discovers folders with metadata on WordPress `init`. Extend the npm build scripts for each new JavaScript and SCSS entry point, keeping generated files in that block's `Assets/` directory.

## Spacing block

The Spacing block carries forward Made's default vertical values: 120px large, 80px medium, 40px small, and 0px for no spacing. Tablet and mobile defaults use approximately the same 1.4 and 1.8 ratios as Made. Editors can override the desktop value and either calculate smaller viewports automatically or set them separately. A zero override is valid.

WordPress's own spacing controls and `theme.json` spacing presets remain available for ordinary layout. Floe's block is for projects that need Made-style spacer behavior. Core Group blocks provide HTML anchors, so Made's anchor-point block is not copied.

## Migration notes

The Made 4 repository is a reference, not a dependency. Floe does not include Made's ACF field definitions, ACF options pages, image rewrite pipeline, third-party front-end libraries, or restrictions on available core blocks. Native Site Icon, block editor, navigation, media sizing, and `theme.json` settings cover the starting point. New project features can live in `Config/`, `Components/`, or focused plugins as needed.

Floe retains the comment and pingback policy from Made, hides the Comments admin menu and toolbar item, and applies light admin branding. It leaves Dashboard, plugin management, and other normal WordPress menus visible.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
