# Footer

Site footer on the Inverse surface (Figma `30:25`).

- **Sign-off:** the Logo component (white), the **site tagline** (Settings → General → Tagline) in H3, and the "Header and footer button" menu's first item as a button (Inverse look on the dark surface, with arrow).
- **Link columns:** the `footer-1`, `footer-2` and `footer-3` menu locations. Each menu's **name** is its column heading (e.g. name the menu "Explore"). Empty locations are left out.
- **Legal row:** "© <year> <site name>. All rights reserved." (year in the site's timezone) and "Built with Floe".

Mobile: the sign-off stacks above the columns, which sit two-up.

```php
echo Floe\component( 'footer' );
```
