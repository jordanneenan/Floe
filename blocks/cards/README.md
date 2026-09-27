# Cards (`floe/cards`)

A grid of manually entered cards (Figma `36:173`). For an eyebrow, heading, intro or button, add a **Block intro** above the Cards.

| Field | Notes |
| --- | --- |
| Card style | **Feature**: numbered cards (01, 02… added automatically in order), title, text and a "Learn more" link, on the raised surface colour. **Icon**: the same panel with an icon in place of the number. **Media**: an image or MP4 on each card, title and text; the card can link. |
| Columns | 2, 3 (default) or 4 on large screens. Four sit two by two between 768 and 1279px. |
| Cards | Add as many **Card** blocks as needed. Card titles are H3. |

**Icon grid:** the inserter also offers Cards as **Icon grid** (a block variation with the Icon style already chosen), so it can be found by that name. Each card's icon is any image from the media library, SVG included, shown whole at 56px. Icons are decorative (the title says what the card is about), so they have no alt text. A card can link; its title is the link and "Learn more" (or the link text you set) shows at the bottom.

For a coloured band, put the Cards and their Block intro in a **Background** block.

**Responsive:** the chosen columns from 768px (four only from 1280px), two-up on tablet, one-up on mobile. The block renders nothing with no cards.

**Components used:** Card (and Media, Button through Card).
