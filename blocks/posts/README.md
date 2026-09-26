# Posts (`floe/posts`)

A heading group and a grid of post cards (Figma `36:58`), with everything Made's Posts block and the Ajax Load More plugin did, built in.

| Field | Notes |
| --- | --- |
| Eyebrow, Heading, Intro | Section header. |
| "View all" link | Optional Secondary button on the right. |
| Show | **Latest** of a post type, **Hand-picked** (search by title, reorder, remove; up to 24), or **Manual entries** (add **Post card** blocks and type everything in). |
| Post type | Any public post type (Posts, Pages, or a custom type). |
| Show all / Number of posts | Every published post (up to 100), or 1–24 at a time. |
| More posts | **None**, a **Load more button**, or **Load automatically on scroll**. Each load adds the same number again. |
| Taxonomy | Categories or any public custom taxonomy of the post type. Used for the label on each card, the filters, and "Only show". |
| Show filters | Buttons for "All" and each **top-level** term with posts. Clicking one swaps the cards in place: the page doesn't reload and the URL doesn't change. |
| Only show | Limit to one term (when filters are off). |
| Card images | The featured image by default; override per post for this section only. |

**How it works:** the first cards are rendered on the server. Filters and "Load more" fetch more cards from the block's own REST route (`/wp-json/floe/v1/posts`, in `posts-server.php`), which only returns published, public content. Results are announced to screen readers, focus moves to the first new card after "Load more", and the automatic mode keeps the button as a fallback for keyboard users. The current post is always excluded and no total count is queried.

The section renders nothing when there's nothing to show.

**Responsive:** three-up from 768px, two-up on tablet, one-up on mobile; filter buttons wrap.

**Components used:** Section header, Button, Card, Media.
