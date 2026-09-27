# Breadcrumb

**Figma:** design [33:33](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=33-33), [33:48](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=33-48)

The core Breadcrumbs block (WordPress 6.9+) with Floe styling: Eyebrow type, muted items, the current page in ink, `/` separators. The trail comes from the page hierarchy. Nothing is shown on the front page, or if the installed WordPress has no core Breadcrumbs block.

```php
echo Floe\component( 'breadcrumb' );
```

Used by the Page Banner. The editor shows "Home / <page title>" as a preview.
