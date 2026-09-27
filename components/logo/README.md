# Logo

**Figma:** design [113:1781](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-1781) · dark [113:3663](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-3663)

The site's custom logo (Appearance → Customize → Site Identity → Logo) if one is set. Otherwise the Floe logo (Figma header), inline SVG in `currentColor`, so it is ink on light surfaces and white in the footer. Linked to the home page with the accessible name "<Site name> home".

```php
echo Floe\component( 'logo' );
```
