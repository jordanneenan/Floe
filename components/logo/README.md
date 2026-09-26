# Logo

The site's custom logo (Appearance → Customize → Site Identity → Logo) if one is set. Otherwise the Floe logo (Figma header), inline SVG in `currentColor`, so it is ink on light surfaces and white in the footer. Linked to the home page with the accessible name "<Site name> home".

```php
echo Floe\component( 'logo' );
```
