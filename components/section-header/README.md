# Section header

The heading group used by most sections: eyebrow, heading, optional intro and optional action.

| Argument | Notes |
| --- | --- |
| `eyebrow`, `heading`, `intro` | Text (basic inline HTML allowed). Empty parts are left out. |
| `heading_level` | 1–4, default 2. Only banners use 1. |
| `action` | Button args (see Button), e.g. `[ 'label' => 'View all', 'url' => …, 'style' => 'secondary' ]`. |
| `aside` | Extra HTML for the right-hand side, such as slider controls. |
| `layout` | `split` (default): heading group left, intro/action right, bottom-aligned (Posts, Cards, Stats, Team…). `stacked`: one column with a Body intro (FAQ, Document Download). |

On mobile and tablet the split layout stacks, with the intro/action under the heading.

Editor: `import { SectionHeader } from '@floe/components/section-header';` Parts can be render functions receiving `{ tagName, className }`, so a block can pass a RichText and get identical markup. `@floe/editor`'s `EditableSectionHeader` wires this up for blocks.
