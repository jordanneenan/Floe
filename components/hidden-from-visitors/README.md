# Hidden from visitors

Keeps a block off the public site until it's ready, while the people working on the page still see it. Every Floe block, sections and the blocks inside them, has the switch.

- **In the editor:** select the block, open **Block settings → Advanced** and turn on **Hide from visitors** ("Only people who can edit this page will see this block, tagged “Hidden from visitors”. Use it for anything that isn't ready to go live."). The block stays in the editor with the same marker it has on the site.
- **Who sees it:** anyone who can edit the page being viewed (administrators, editors, and the author of their own post). For everyone else the block isn't rendered at all: no markup, and its CSS and JS don't load. A hidden block inside another block is left out before its parent renders, so a CTA with one of three panels hidden lays out as two, and Stats counts only the figures that show.
- **The marker:** a dashed outline around the block and a **Hidden from visitors** tag in its top corner (class `hidden-from-visitors` on the block's outer element; the label is its `data-hidden-label` attribute, so it can be translated). The tag takes the surface's strong colour, so it reads on Base, Subtle, Tint, Inverse and Accent and in dark mode, and it fades while the pointer is over the block so it never covers what's underneath.
- **In-page navigation** leaves out links to hidden sections for visitors. It asks through the `floe_block_visible` filter, which this component answers, so neither module depends on the other.

It has no markup of its own:

| File | Does |
| --- | --- |
| `hidden-from-visitors.php` | Adds the `hiddenFromVisitors` attribute to every `floe/*` block (`register_block_type_args`), skips hidden blocks for visitors (`pre_render_block`, and `render_block_data` for hidden children), marks them for editors (`render_block`), and answers `floe_block_visible`. |
| `hidden-from-visitors-editor-script.js` | The Advanced setting (`editor.BlockEdit`) and the marker on hidden blocks in the editor (`editor.BlockListBlock`). Loaded on every block editor screen. |
| `hidden-from-visitors.scss` | The outline and tag, on the site and in the editor. |

**Hiding isn't a way to keep secrets.** The block is still saved in the page's content, so WordPress search can match its text, and anyone with access to the editor or the database can read it.

**Page caching.** Visitors get the page without the block and editors get it with the marker. Page caches normally skip logged-in users, so that holds with caching on; if a cache ever serves logged-in users, exclude them. Most caches clear a page when it's saved; if yours doesn't, clear it after hiding a block.

**WordPress's own Hide** (the block's ⋮ options menu → Hide, WordPress 6.9 and later) hides a block from everyone, editors included: it's gone from the site and from the editor canvas, and only List View still shows it. Use it to take a block out without deleting it. Use Hide from visitors when the team should still see it ([decision D47](../../documentation/decisions.md#d47-hide-from-visitors-is-a-floe-setting-beside-wordpresss-hide)).

Delete this folder to remove the feature. Nothing errors: hidden blocks simply show to everyone again, without the marker, and the setting disappears from the editor (the saved setting is dropped the next time each page is saved).
