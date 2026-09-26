# Breadcrumb

The core Breadcrumbs block (WordPress 6.9+) with Floe styling: Eyebrow type, muted items, the current page in ink, `/` separators. The trail comes from the page hierarchy. Nothing is shown on the front page, or if the installed WordPress has no core Breadcrumbs block.

```php
echo Floe\component( 'breadcrumb' );
```

Used by the Page Banner. The editor shows "Home / <page title>" as a preview.
