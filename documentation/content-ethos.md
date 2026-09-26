# Content ethos

**Purpose:** how Floe talks about itself. Use this for the Floe site's own pages, posts, block examples and any copy an editor or visitor reads. It doesn't rename anything in the code (see [Words](#words)).

## What Floe is, in one line

Floe gives every page a designed starting point and every editor a simple way to build on it. Native WordPress, no complex page builder plugin.

## The four ideas

**1. Built from blocks.** Floe is a page builder, but the pieces are designed blocks, not granular elements. Editors don't assemble pages from columns, spacers, buttons and styling settings. They choose a complete block, a banner or FAQ or set of cards, add their copy and images, and the layout is already done. The copy should make that difference clear: less to learn and less to get wrong, because the building blocks are bigger and already designed.

**2. Freedom, not constraint.** Floe unlocks a team's ability to build and grow its own website without waiting on a developer. Write about what editors can do, not what they're kept from doing. "The freedom to build your own pages", not "the editor only offers approved choices". Avoid *constraints*, *restrictions*, *locked down*, *only offers*, *can't break* and *never drift*, even when the point is brand consistency. Say the positive version: every block already carries the brand, so new pages look right from the first draft.

**3. A firm foundation.** Native WordPress is the ground everything stands on: the block editor, `theme.json` and core features, with no page builder plugin and no lock-in. Floe's blocks sit on that foundation, and each client's branding sits on the blocks. Building language fits naturally here (foundations, building blocks, building on it, built to last), but use it where it's true and don't stack metaphors.

**4. We design it, they build with it.** The Floe studio customises the system for each client. Clients never touch the design system and aren't asked to understand it. Don't describe clients or their designers "setting tokens" or "configuring the system". A site can start from Floe's design and be made the client's own, or be fully custom; either way every site feels its own.

## Words

| Say | Don't say | Why |
| --- | --- | --- |
| block, blocks, block library | section, sections | "Blocks" is WordPress's own word and what editors see in the editor. |
| custom branding, made yours, fully custom | brand tokens, tokens, design tokens | Tokens are how we build it, not what the client gets. |
| no complex page builder plugin | no page builder | Floe *is* a page builder; the difference is it's native blocks, not a plugin with its own learning curve. |
| copy and images | content (on its own) | Say what the editor actually adds. |
| freedom, confidence, build your own pages | constraints, restrictions, guardrails | Floe is sold on what it unlocks. |

In the code, a top-level Floe block is still called a *section* (the `floe-sections` category, `floe-section` wrapper class, `has_sections()`, `--floe-section-space`). That's an internal term for the architecture and stays as it is. [Architecture](architecture.md) and [the block library](blocks.md) use it that way.

## Voice

- Plain, confident and short. One idea per sentence; cut words that don't add meaning.
- Australian spelling: colour, organisation, customise.
- Speak to the reader's team ("your team", "editors"), not to designers or developers, unless the page is for them.
- Specific over clever: "choose a block, add your copy and images" beats "unleash your creativity".
- Technical terms (`theme.json`, WCAG 2.2 AA) are fine where they reassure a technical reader, briefly and without explanation-heavy detail.

## Placeholder proof

The preview site's logo strip (Northwind, Halcyon, Meridian and so on), stats ("40+ sites", "98 Lighthouse"), testimonials and team members are illustrative. They show how the blocks look with real-world content. Before the site goes live, each must be replaced with real clients, real figures and real quotes (with permission), or removed.

## Checklist for new copy

1. Does it say *block*, not *section*?
2. Does it describe what the editor can do, not what they can't?
3. Would a client read it and understand it without knowing how the theme works?
4. Is any proof (logos, numbers, quotes) real, or clearly placeholder?
