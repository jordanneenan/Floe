# Theme toggle

A round button that switches the site between light and dark. It sits in the footer's bottom row.

- **Default:** follows the visitor's device (`prefers-color-scheme`), and keeps following it live, until they press the button. Their choice is then remembered in `localStorage` (`floe-theme`).
- **No flash:** `theme_toggle_head()` prints a tiny inline script at the very top of `<head>` that sets `<html data-theme="light|dark">` before the page paints. The dark colours live in `theme.json` (`settings.custom.dark`) and are applied to the surfaces in `assets/scss/base.scss`.
- **Animation:** the sun's rays spin away as a shadow slides over it to make a moon. The page switches with a circular wipe from the button where the browser supports View Transitions; elsewhere every colour cross-fades over 0.4s. With reduced motion it switches instantly.
- **Accessibility:** a real `<button>` named "Dark mode" with `aria-pressed` showing whether dark mode is on.

```php
echo Floe\component( 'theme-toggle' );
```

Delete this folder to remove dark mode: without the head script nothing sets `data-theme`, so the site stays light.
