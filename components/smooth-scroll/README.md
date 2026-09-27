# Smooth scroll

In-page links glide to their section instead of jumping, and a page opened with an anchor in its URL (`/about/#team`) loads at the top and then glides down to it. It has no markup: `smooth-scroll.php` loads it on every front-end page.

- **Clicks:** native CSS, `scroll-behavior: smooth` on the page. Any same-page link works: In-page navigation, buttons linking to `#section`, the skip link. Sections stop below the sticky In-page navigation (`scroll-padding-top` in `base.scss`).
- **Landing on an anchor:** a tiny inline script at the top of `<head>` takes the anchor off the URL before the browser can jump there, and `smooth-scroll.js` puts it back once the page has loaded, which scrolls to it smoothly (and scroll reveals play on the way down). Only on a fresh visit: a reload or Back keeps the browser's own scroll position, and if the visitor starts scrolling before the page finishes loading, they stay where they are.
- **Reduced motion:** `prefers-reduced-motion: reduce` keeps the browser's instant jump for both.
- **Editor:** the block editor and admin screens are left alone.

Delete this folder to switch it off.
