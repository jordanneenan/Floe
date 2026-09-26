# Posts (`floe/posts`)

A heading group and a grid of post cards (Figma `36:58`).

| Field | Notes |
| --- | --- |
| Eyebrow, Heading | Section header. |
| "View all" link | Optional Secondary button on the right (e.g. to the blog page). |
| Show | **Latest posts**, **Posts from a category**, or **Hand-picked posts** (search by title, reorder, remove; up to 12). |
| Number of posts | 1–12 (latest and category). |
| Card images | Each card uses the post's featured image. The "Card images" panel can set a different image for this section only. |

Queries skip the current post, ignore sticky posts and don't count totals (`no_found_rows`). Titles and excerpts are shown decoded in the editor, and dates use the site's date format in both places. Each card is one link (the title). The section renders nothing when no posts match.

**Responsive:** three-up from 768px, two-up on tablet, one-up on mobile.

**Components used:** Section header, Button, Card, Media.
