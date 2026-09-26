# Reveal

Content fades gently up into place (0.9s, a 28px rise) as it scrolls into view. It has no markup: `reveal.php` loads `reveal.js` on every front-end page and the script picks its own targets.

- **What moves:** in each section of the page, the direct parts of the section's inner container. Grids and lists (`__grid`, `__list`, `__items`, `__logos`, `__files`, `__rows` and the CTA panels) reveal their items one after another, 90ms apart. The Steps connectors draw themselves in.
- **What doesn't:** the Home and Page banners (they have their own load animation in their block CSS), the sticky In-page navigation, Spacing, and anything already on screen when the page loads.
- **Safe by default:** only JavaScript hides anything, and only below the fold. Once an element is in place its `data-reveal` attribute is removed so its own hover transitions apply again. `prefers-reduced-motion: reduce` turns it off.

Delete this folder to switch scroll reveals off.
