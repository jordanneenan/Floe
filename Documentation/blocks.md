# Custom block contract

## Registration and layout

`Config/Blocks.php` registers every **immediate** subdirectory of `Blocks/` that contains `block.json`. There is no parent category directory, no nested version directory, and no ACF registration. Block names must be unique WordPress names under the `floe/` namespace.

Use this shape for a new block (example name `Example`):

```text
Blocks/Example/
  block.json             metadata and asset paths
  example.php             PHP render template, when dynamic output is needed
  example.scss            source CSS
  example.js              editor source
  Assets/
    example.css           compiled CSS; commit it
    example.js            compiled JS; commit it
    example.asset.php     wp-scripts dependency manifest; commit it
```

`block.json` may use a static `save` implementation instead of a PHP render template when that is the right WordPress pattern. The current `Spacing` block is dynamic: `spacing.js` registers its editor UI and returns `null` from `save`; `spacing.php` supplies frontend markup. Its metadata declares `editorScript`, shared `style`, and `render` with `file:./...` paths.

## Add a block

1. Check whether a core block, block variation, block style, pattern, or `theme.json` setting meets the need. Add a custom block only when the editing or rendering behavior is distinct.
2. Create one directory directly under `Blocks/`, choose a unique `floe/<slug>` name, and create `block.json`. For a second variant, choose a new folder and ID such as `ExampleV2` and `floe/example-v2`.
3. Put editor controls in the source JS, block styles in SCSS, and server output in PHP when using dynamic rendering. Use `useBlockProps()` in the editor and `get_block_wrapper_attributes()` on the server.
4. Add the block's JS and SCSS entries to the npm scripts (or improve the build script to discover blocks). Keep each compiled output in that block's `Assets/` folder.
5. Build, confirm every `file:` path in `block.json` exists, and commit source and compiled assets together. Check editor and frontend behavior in a running WordPress site when one is available.

## `floe/spacing` behavior and attribute contract

| Attribute | Default | Meaning |
| --- | --- | --- |
| `size` | `large` | `large`, `medium`, `small`, or `none`. |
| `desktop` | `-1` | Pixel override. `-1` means unset; `0` is a valid zero override. |
| `tablet` | `-1` | Optional tablet pixel override when automatic calculation is off. |
| `mobile` | `-1` | Optional mobile pixel override when automatic calculation is off. |
| `automatic` | `true` | With a desktop override, derive tablet/mobile values by dividing by 1.4/1.8. |

Default CSS values are large **120/86/67px**, medium **80/57/44px**, small **40/29/22px**, and none **0/0/0px** for desktop/tablet/mobile. Breakpoints are `max-width: 768px` and `max-width: 550px`. Editor and PHP rendering both cap override inputs at 500px. Server rendering validates the size against the allowed list and uses CSS custom properties for overrides. The block is decorative and marks its wrapper `aria-hidden="true"`.

When changing these semantics, update **all of** `block.json` defaults, JS preview math, PHP render math, SCSS defaults, and this page. A change only in the editor can diverge from the published page.

## Asset gotcha

The current `wp-scripts build Blocks/Spacing/spacing.js --output-path=Blocks/Spacing/Assets` command emits `spacing.js` and `spacing.asset.php`, **not** `index.js`. Keep the metadata's `editorScript` path set to `file:./Assets/spacing.js` unless the build command changes.
