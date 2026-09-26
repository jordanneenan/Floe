# Button

Every button on the site: pill shape, 48px tall, Label type. From Figma component `29:18`.

| Style | Use |
| --- | --- |
| `primary` | The main action. On Inverse and Accent surfaces it automatically takes the white Inverse look. |
| `secondary` | Alongside a primary (outlined). |
| `inverse` | Force the white look. |
| `link` | Tertiary text action (blue text and arrow). |

```php
echo Floe\component( 'button', [
	'label'   => 'Start a project',
	'url'     => home_url( '/contact/' ),
	'style'   => 'primary',
	'arrow'   => true,        // default true
	'new_tab' => false,       // adds target, rel and a screen-reader note
] );
```

For a form, pass `'type' => 'submit'` and no `url`: it renders a `<button type="submit">` with the same look.

Blocks store links as `{ label, url, newTab }` objects; `Floe\Components\button_args_from_link( $link, [ 'style' => 'secondary' ] )` converts one.

The stylesheet also styles core Button blocks (inside Article) and native submit buttons inside form slots, so there is only one button design.

Editor: `import { Button } from '@floe/components/button';` renders the same markup, with the label passed in as a node.

Hover: Primary darkens to blue-deep (Figma notes blue-deep as the action hover); the arrow nudges right. Secondary's border darkens. Focus uses the global focus ring.
