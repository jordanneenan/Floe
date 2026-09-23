# Floe block contract

## Registration and build

`Config/Blocks.php` registers every immediate `Blocks/*/block.json` folder. Each block has its own metadata, editor source, SCSS, server render template, and committed `Assets/` output. Shared editor controls and PHP render helpers live in `Blocks/_shared/`. `npm run build` discovers source files and rebuilds all block assets and the shared section CSS.

The theme is a classic WordPress theme with Gutenberg blocks. Blocks use native `RichText`, `InnerBlocks`, the Media Library, and WordPress queries. No ACF plugin is needed. Dynamic parents that contain nested blocks save `InnerBlocks.Content`, so WordPress passes rendered child content to their PHP templates.

## Page sections

| Block | Main editor fields | Frontend behaviour |
| --- | --- | --- |
| `floe/home-banner` | Eyebrow, title, intro, optional link, image or MP4 | Large split homepage hero |
| `floe/page-banner` | Breadcrumb, title, intro, optional link and media | Compact interior-page banner; optional media layout |
| `floe/article` | Unrestricted native inner blocks, optional link, approved background surface | Readable WYSIWYG section |
| `floe/image-copy` | Media, eyebrow, title, copy, optional link, media side | Fixed paired layout, stacked on narrow screens |
| `floe/cta` | Title, copy, optional link, background surface | Centred call to action |
| `floe/testimonial` | Quote, attribution, optional portrait media | Quote-led section with optional visual |
| `floe/posts` | Eyebrow, title, latest/category/selected source, count 1–12, per-post media overrides | Queries published WordPress posts and renders shared card layout |
| `floe/cards` | Eyebrow, title, repeatable manual Card children | Manual cards using the same presentation as Posts |
| `floe/document-download` | Eyebrow, title, repeatable Download file children | File list with title, type, size, and download action |
| `floe/images` | Eyebrow, title, repeatable Images row children | Rows of one, two, or three media items |
| `floe/video` | Eyebrow, title, YouTube URL, cover image, caption | Cover and play control; privacy-enhanced player loads on click |
| `floe/spacing` | Preset or custom values for four breakpoints | Replaces the previous Floe section's default bottom gap |

Helper blocks are `floe/card-item`, `floe/download-item`, `floe/image-row`, and `floe/media`. WordPress restricts Card and Download children to their parents. The Media helper is available within Article and Images rows.

## Media rules

An image slot in Floe offers an image or a self-hosted MP4. The editor selects media through the WordPress Media Library. An MP4 requires a poster image. On the frontend it has no controls or sound, loops inline, and starts only when visible and motion is allowed. Reduced-motion visitors see the poster because playback is paused. Images use WordPress responsive image markup and author-supplied alternative text.

Posts use each post's featured image by default. Editors may override individual cards with an image or MP4 inside a Posts section. Article remains unrestricted; use the **Image or Looping Video** helper for a visual that can switch between those media types. Native Core Image blocks remain available as ordinary WordPress content.

The Video section is separate: it takes a YouTube URL and cover image, then creates a `youtube-nocookie.com` iframe only after the visitor presses Play. It accepts standard watch, short, Shorts, and embed URLs containing an 11-character video ID.

## Spacing

Every Floe section has a default bottom gap. A Spacing block placed immediately after it removes that default and supplies its own height. This includes **No spacing**, which makes the gap zero. The selector requires direct sibling blocks, as WordPress normally renders them in post content.

| Preset | Large desktop ≥1280 | Desktop 768–1279 | Tablet 550–767 | Mobile <550 |
| --- | ---: | ---: | ---: | ---: |
| Large | 120px | 120px | 86px | 67px |
| Medium | 80px | 80px | 57px | 44px |
| Small | 40px | 40px | 29px | 22px |
| No spacing | 0 | 0 | 0 | 0 |

Custom mode exposes a pixel value for each breakpoint, capped at 500px. Unset values use the selected preset; an unset large-desktop value inherits desktop. Legacy three-breakpoint overrides continue to render. The block is decorative and has `aria-hidden="true"`.

## Design boundary

The current CSS follows the first desktop Figma pass and stacks or reduces columns at narrower widths. The Figma library still needs approved mobile/tablet variants and long-content/empty-state review. Colour roles in `theme.json` are provisional neutral defaults; site branding may change those token values without changing section structure.
