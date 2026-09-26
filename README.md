# Floe

Floe is a modular, open-source WordPress theme: a library of designed page sections built on native WordPress. Editors pick a section, fill it in and get a layout that already fits the brand; each site re-brands through `theme.json` tokens, not by editing blocks.

Start with [AGENTS.md](AGENTS.md) and [documentation/build-brief.md](documentation/build-brief.md). [documentation/README.md](documentation/README.md) maps every task to the right page and files.

## Requirements

- WordPress 7.1 or newer, PHP 8.0 or newer
- Node.js 20 or newer and npm, for development only (built files are committed)
- No ACF and no required plugins. A form plugin is needed only for the Contact and Newsletter form slots.

## Everything is a module

```text
blocks/<name>/            one block: block.json, <name>.php, <name>.scss,
                          <name>-editor.js, optional <name>.js and
                          <name>-server.php, README.md, assets/
blocks/<parent>/<child>/  child blocks live inside their parent
components/<name>/        one UI piece: <name>.php, <name>.scss, optional
                          <name>-editor.js (editor twin) and <name>.js, README.md
assets/                   global SCSS, fonts, shared editor helpers, brand files
includes/                 theme setup; includes/admin/ and includes/media/ hold
                          self-contained feature files
build.mjs                 the build (discovers every module by folder)
```

The block's folder name is also its wrapper class and its file names: inspect a page, see `class="page-banner"`, open `blocks/page-banner/page-banner.php`. Adding a folder adds a block or component; deleting it removes it on the next page load with no errors. Nothing lists module names centrally.

Pages and posts are built from blocks in the editor; there are no page templates or patterns. `index.php` is the only template.

## Development

```sh
npm ci
npm run build          # one-off build of every module
npm run start          # watch, rebuild on save, live-reload; also serves the site to
                       # phones and other computers at the printed External URL
npm run lint           # JS and SCSS lint
```

## Library

Sections: Home Banner, Page Banner, In-page navigation, Article, Image + Copy, Cards, Posts (latest of any post type with load more and filters, hand-picked or manual), Stats, Steps, Logo strip, Testimonial, Testimonials, Team, Table, FAQ, Document Download, Images, Video, Contact, Newsletter, CTA (one to three panels) and Spacing. Each block's README describes its fields and responsive behaviour.

Components: Button, Eyebrow, Section header, Media, Card, File row, Play control, Icon, Breadcrumb, Logo, Navigation, Header, Footer, plus Form and Accordion styles.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE). Geist and Geist Mono are under the SIL Open Font License (`assets/fonts/OFL.txt`).
