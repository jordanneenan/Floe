# Icon

**Figma:** design [128:2088](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=128-2088) · dark [128:2091](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=128-2091)

Inline SVG icons in `currentColor`, from the Figma component set: `arrow`, `arrow-left`, `download`, `play`, `plus`, `minus`, `check`, `dash`, plus `menu`, `close`, `chevron`, `pause` and `play-small` for header and media controls.

```php
echo Floe\icon( 'arrow' );                                           // decorative
echo Floe\component( 'icon', [ 'name' => 'check', 'label' => 'Included' ] ); // meaningful
```

Editor: `import { Icon } from '@floe/components/icon'; <Icon name="arrow" />`

Icons are `aria-hidden` unless a `label` is passed. Colour follows the surrounding text.
