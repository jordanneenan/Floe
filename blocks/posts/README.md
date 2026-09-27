# Posts (`floe/posts`)

A grid of post cards (Figma `36:58`), with listing, filters and load more built in (no plugin needed). For an eyebrow, heading and "View all" link (a Secondary button on the right), add a **Block intro** above the Posts.

| Field | Notes |
| --- | --- |
| Show | **Latest** of a post type, **Hand-picked** (search by title, reorder, remove; up to 24), or **Manual entries** (add **Post card** blocks and type everything in). |
| Post type | Any public post type (Posts, Pages, or a custom type). |
| Show all / Number of posts | Every published post (up to 100), or 1–24 at a time. |
| More posts | **None**, a **Load more button**, or **Load automatically on scroll**. Each load adds the same number again. |
| Taxonomy | Categories or any public custom taxonomy of the post type. Used for the label on each card, the filters, and "Only show". |
| Show filters | Buttons for "All" and each **top-level** term with posts. Clicking one swaps the cards in place without reloading the page, and puts the filter in the address (`?filter=<term-slug>`): the link can be shared or bookmarked and opens already filtered, and Back/Forward step through filters. An unknown slug shows everything. |
| Only show | Limit to one term (when filters are off). |
| Card images | The featured image by default; override per post for this section only. |

Card titles are H3. For a coloured band, put the Posts and their Block intro in a **Background** block.

**How it works:** the first cards are rendered on the server. Filters and "Load more" fetch more cards from the block's own REST route (`/wp-json/floe/v1/posts`, in `posts-server.php`), which only returns published, public content. Results are announced to screen readers, focus moves to the first new card after "Load more", and the automatic mode keeps the button as a fallback for keyboard users. The current post is always excluded and no total count is queried.

The block renders nothing when there's nothing to show.

**Responsive:** three-up from 768px, two-up on tablet, one-up on mobile; filter buttons wrap.

**Components used:** Button, Card, Media.
