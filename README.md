# Floe

Floe is a modular, open-source WordPress theme: a library of designed page sections built on native WordPress. Editors pick a section, fill it in and get a layout that already fits the brand; each site re-brands through `theme.json` tokens, not by editing blocks.

Start with [AGENTS.md](AGENTS.md) and [Documentation/build-brief.md](Documentation/build-brief.md). [Documentation/README.md](Documentation/README.md) maps every task to the right page and files.

## Requirements

- WordPress 7.1 or newer, PHP 8.0 or newer
- Node.js 20 or newer and npm, for development only (built files are committed)
- No ACF and no required plugins. A form plugin is needed only for the Contact and Newsletter form slots.

## Everything is a module

```text
Blocks/<name>/            one block: block.json, <name>.php, <name>.scss,
                          <name>-editor.js, optional <name>.js, README.md, assets/
Blocks/<parent>/<child>/  child blocks live inside their parent
Components/<name>/        one UI piece: <name>.php, <name>.scss, optional
                          <name>-editor.js (editor twin) and <name>.js, README.md
Config/                   theme setup; Config/Admin/ and Config/Media/ hold
                          self-contained feature files
patterns/                 brochure pages and the Block Preview, as block patterns
Assets/                   global SCSS, fonts, the shared editor helpers, brand files
scripts/                  build, screenshots and the preview seed script
```

The block's folder name is also its wrapper class and its file names: inspect a page, see `class="page-banner"`, open `Blocks/page-banner/page-banner.php`. Adding a folder adds a block or component; deleting it removes it on the next page load with no errors. Nothing lists module names centrally.

## Development

```sh
npm ci
npm run build          # one-off build of every module
npm run start          # watch, rebuild on save, live-reload floe.local (BrowserSync)
npm run lint           # JS and SCSS lint
npm run screenshots    # full-page captures at 375, 600, 1024 and 1440 into .screenshots/
```

Build the preview site (brochure pages, journal posts, menus, Block Preview) on any site running Floe:

```sh
wp eval-file wp-content/themes/floe/scripts/seed-preview.php /path/to/preview/images
```

## Library

Sections: Home Banner, Page Banner, In-page navigation, Article, Image + Copy, Cards, Posts, Stats, Steps, Logo strip, Testimonial, Testimonials, Team, Table, FAQ, Document Download, Images, Video, Contact, Newsletter, CTA and Spacing. Each block's README describes its fields and responsive behaviour.

Components: Button, Eyebrow, Section header, Media, Card, File row, Play control, Icon, Breadcrumb, Logo, Navigation, Header, Footer, plus Form and Accordion styles.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE). Geist and Geist Mono are under the SIL Open Font License (`Assets/fonts/OFL.txt`).
