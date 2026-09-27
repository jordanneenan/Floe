# Carousel

**Figma:** design [107:1560](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=107-1560) · dark [113:3664](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=113-3664)

Floe's own carousel, with no third-party library. Every carousel on the site uses it: Testimonials, Image carousel, the Logo strip's ticker and the lightbox slideshow.

```php
echo Floe\component( 'carousel', array(
	'content'  => $slides_html,        // each top-level element is one slide
	'label'    => __( 'Quotes', 'floe' ),
	'per_view' => array( 1.15, 2, 3 ), // mobile, from 550px, from 768px
) );
```

| Argument | Notes |
| --- | --- |
| `content` | The slides as HTML. Each top-level element is one slide. Returns `''` when empty (unless `empty` is true). |
| `label` | Accessible name, e.g. "Quotes" or "Images". |
| `mode` | `slides` (default) or `ticker`. |
| `per_view` | Slides visible on mobile, from 550px and from 768px. Fractions show part of the next slide, which signals that the row can be swiped (e.g. `1.15`). Slides mode only. |
| `list` | Ticker only: render the track as a `<ul>` (items must be `<li>`), so a list of logos stays a list. |
| `prev`, `next` | Button labels for screen readers (default "Previous" and "Next"). |
| `speed` | Ticker speed in pixels per second (default 40). |
| `empty` | Render the shell with no slides, for scripts that fill it (the lightbox). |
| `class` | Extra classes on the root. |

## Slides

The track scrolls sideways with CSS scroll snapping, so swipe, trackpad and keyboard scrolling work without any script. `carousel.js` adds the rest:

- **Previous/next buttons** in a row below the slides, on the right. They step one slide and disable at either end. When every slide already fits, the buttons are hidden.
- **Keyboard:** with the track focused, ← and → step one slide, and Home and End go to the first and last.
- **Screen readers:** the track is a region with the role description "carousel", and each slide is a group labelled "2 of 6".
- It fires a `floe:carousel` event on the root (`detail.index`, `detail.count`) whenever the position changes.

## Ticker

The items are repeated to fill the width and slide past continuously, fading out at both edges. The speed is steady at any width.

- **Pause:** hovering or focusing the ticker pauses it. A small pause button appears in its top-right corner on hover or focus, and is always visible on touch screens. Pressing it stops the ticker until it's pressed again (WCAG 2.2.2).
- **Reduced motion, or no JavaScript:** nothing moves. The items sit in a centred row that wraps.
- The repeated copies are `aria-hidden` and `inert`, so screen readers and the keyboard meet each item once.

## Script API

The script (`floe-carousel`) is loaded only on pages with a carousel. It starts every `[data-carousel]` in the page. A script that adds a carousel later calls:

```js
window.floe.carousel.init( element );          // start it
window.floe.carousel.goTo( element, 3, false ); // jump to the fourth slide, no animation
```

**Editor:** `Carousel` and `CarouselControls` from `@floe/components/carousel` render the same markup without behaviour. Slides scroll sideways so each one can be edited, and a ticker stands still.

**Components used:** Icon.
