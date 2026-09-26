# Navigation

Renders a registered menu location with `wp_nav_menu()`. Nothing is printed when a location has no menu, so there's never an automatic list of every page.

| Location | Used for |
| --- | --- |
| `primary` | Header menu |
| `action` | Header and footer button: the first item becomes the button (e.g. "Get started" → Contact) |
| `footer-1`, `footer-2`, `footer-3` | Footer link columns. The menu's name is the column heading (e.g. "Explore") |

```php
echo Floe\component( 'navigation', [ 'location' => 'primary', 'label' => 'Main' ] );
echo Floe\component( 'navigation', [ 'location' => 'footer-1', 'heading' => true ] );
echo Floe\component( 'navigation', [ 'location' => 'action', 'as' => 'button' ] );
```

Menus are edited natively in Appearance → Menus.
