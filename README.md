# Floe

Floe is an open-source WordPress framework theme inspired by Made 4. It starts with native WordPress features and adds custom blocks only where a specific design system needs them.

AI agents and contributors should start with the [review handoff](Documentation/agent-handoff.md), then use [Documentation/README.md](Documentation/README.md) for the project map, block contract, migration decisions, and verification workflow.

## Requirements

- WordPress 6.6 or newer
- PHP 8.0 or newer
- Node.js 20 or newer and npm for development

No ACF installation is required.

## Structure

```text
Blocks/
  HomeBanner/, PageBanner/, Article/, ...
  Spacing/
    block.json       WordPress block metadata
    spacing.php      server render template
    spacing.scss     source styles
    spacing.js       editor source
    Assets/          built JavaScript, asset manifest, and CSS
  _shared/           shared editor controls, PHP render helpers, section CSS and media script
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

The build script discovers every block folder with `block.json` and compiles matching JavaScript and SCSS files. For focused work, use `npm run build:js` or `npm run build:css`. No watch script is configured.

To add a block, create `Blocks/YourBlock/block.json` and source files named for the block slug. `Config/Blocks.php` discovers folders with metadata on WordPress `init`. Keep generated files in that block's `Assets/` directory.

## Block library

Floe includes Home Banner, Page Banner, Article, Image + Copy, CTA, Testimonial, Posts, Cards, Document Download, Images, Video, and Spacing. Repeatable card, file, and image-row items use nested Gutenberg blocks. Most non-icon media slots accept an image or a silent looping self-hosted MP4 with a poster. Video is a separate YouTube cover-and-play block.

Each page section has a default bottom gap. Placing Spacing directly after one replaces that gap with Large, Medium, Small, None, or custom values for large desktop, desktop, tablet, and mobile. [The block contract](Documentation/blocks.md) describes each editor field and current behaviour.

## Migration notes

The Made 4 repository is a reference, not a dependency. Floe does not include Made's ACF field definitions, ACF options pages, image rewrite pipeline, third-party front-end libraries, or restrictions on available core blocks. Native Site Icon, block editor, navigation, media sizing, and `theme.json` settings cover the starting point. New project features can live in `Config/`, `Components/`, or focused plugins as needed.

Floe retains the comment and pingback policy from Made, hides the Comments admin menu and toolbar item, and applies light admin branding. It leaves Dashboard, plugin management, and other normal WordPress menus visible.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
