# Eyebrow

**Figma:** design [29:25](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=29-25) · dark [91:283](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-283)

Accent dot plus Geist Mono uppercase label (Figma `29:25`). The tone follows the surface: blue dot and muted label on light surfaces, the Inverse tone on Inverse, all white on Accent. No `tone` argument is needed.

```php
echo Floe\component( 'eyebrow', [ 'text' => 'Latest posts' ] );
```

Editor: `import { Eyebrow } from '@floe/components/eyebrow'; <Eyebrow>{ richText }</Eyebrow>`
