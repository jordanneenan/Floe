# Header

Site header (Figma `30:2`): the Logo component, the `primary` menu and one action button, on a white bar with a line underneath. 88px tall on desktop, 72px below 768px.

- **Menu:** Appearance → Menus → "Header menu" location. The current page is shown in ink, others muted. One level of dropdown is supported (on hover and keyboard focus).
- **Action:** the first item of the "Header and footer button" menu location, as a Primary button without an arrow.
- **Mobile and tablet (<768px):** a menu button (disclosure pattern: `aria-expanded`, `aria-controls`) opens a panel under the bar with the menu and the action. Escape closes it and returns focus to the button; so does tabbing out of the header. Without JavaScript the menu shows under the logo.

The header isn't sticky; the In-page navigation block sticks to the top of the window instead.

```php
echo Floe\component( 'header' );
```
