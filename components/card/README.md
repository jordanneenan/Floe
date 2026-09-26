# Card

Figma "Content card" (`30:88`). Variants:

| Variant | Contents | Used by |
| --- | --- | --- |
| `post` | Media (4:3), category chip, date, title (H3), excerpt | Posts |
| `feature` | Number, title, text, "Learn more" cue, on the raised surface colour | Cards (Feature style) |
| `media` | Media (4:3, neutral placeholder if empty), title, text | Cards (Media style) |

The title is the link, and the link covers the whole card, so each card is one link with the title as its name (no repeated "Read more"). The Feature card's "Learn more" is a visual cue only (`aria-hidden`).

```php
echo Floe\component( 'card', [
	'variant'  => 'post',
	'title'    => get_the_title(),
	'text'     => get_the_excerpt(),
	'url'      => get_permalink(),
	'media'    => [ 'id' => get_post_thumbnail_id() ],
	'category' => 'Insights',
	'date'     => get_the_date(),
	'datetime' => get_the_date( 'c' ),
] );
```

Hover: the title underlines and the image zooms slightly. Focus: the ring surrounds the whole card.
