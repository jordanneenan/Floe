# Header

Site header (Figma `30:2`): the Logo component, the `primary` menu and one action button, on a white bar with a line underneath. 88px tall from 768px, 72px below.

**Appearance → Customize → Header:**

| Setting | Default | Notes |
| --- | --- | --- |
| Sticky header | On | The bar stays at the top of the window while the page scrolls, and its padding shrinks as the page scrolls: over the first 300px it goes from 88px to 64px tall (72px to 56px below 768px), following the scroll position rather than playing an animation. A bottom margin makes up the difference, so nothing below it moves. `header.js` keeps `--floe-sticky-top` (`base.scss`) at the bar's real height, so the In-page navigation sticks right below it; anchors land clear of the shrunk bar (`--floe-sticky-top-scrolled`). Without JavaScript it stays full height. |
| Show the button | On | Off leaves the header without its button. The footer keeps its button either way. |
| Menu position | Centred | From 1280px: **Centred** on the bar (with or without the button), or **Right**, beside the button. Below 1280px the menu is behind the menu button either way. |

- **Menu:** Appearance → Menus → "Header menu" location. The current page is shown in ink, others muted. One level of dropdown is supported (on hover and keyboard focus).
- **Action:** the first item of the "Header and footer button" menu location, as a Primary button without an arrow.
- **Below 1280px:** a menu button (disclosure pattern: `aria-expanded`, `aria-controls`) opens a panel under the bar with the menu and the action. Escape closes it and returns focus to the button; so does tabbing out of the header. Without JavaScript the menu shows under the logo. From 1280px the menu sits in the bar; labels never wrap, and a menu too long for the bar wraps onto a second row between items.

With the header not sticky, the In-page navigation block sticks to the top of the window instead.

```php
echo Floe\component( 'header' );
```
