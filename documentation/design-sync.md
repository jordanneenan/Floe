# Design sync: keeping Figma, wireframes and code in step

Floe exists in three places that must agree: the [Figma file](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n) (block designs with their dark copies, and the wireframes), this repository, and the two sites (floe.local and floewp.com). The code and live sites are kept in step by the [workflow](workflow.md) and [floe-sync](live-site.md). This page covers Figma (D72).

**The rule:** a change to how a block or component looks, or to the options it offers, updates Figma in the same piece of work, before its pull request merges into `dev`. A change made in Figma first is followed in code the same way. Nobody fixes drift later. The fixing happens as part of the change.

**Foundations Figma:** design [38:181](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=38-181), [61:267](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=61-267) (tokens board and imagery; mirrors `theme.json`)

**Last full audit:** 2026-09-28 at commit `129fe27`

## The map

Each block and component README has a line that links it to Figma:

```md
**Figma:** design [36:173](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=36-173) · dark [91:369](…) · wireframe [46:791](…)
```

- **design:** the light design on `02 / Block Designs + Components`, first the main component or block and then any extra example frames.
- **dark:** its dark copy on the same page (the column to the right, in the Dark variable mode).
- **wireframe:** its wireframe on `01 / Wireframes`, plus any extra wireframe examples.
- A module with nothing to draw says so: `**Figma:** none (editor-only: it only adds space between sections)`.

In the other direction, each Figma component's description ends with `Code: blocks/<name>` or `Code: components/<name>`. The map lives only in the module folders, like everything else about a module, so adding or deleting a folder adds or removes its entry. `bin/design-sync check` (part of `npm run lint`) fails if a README has no Figma line.

## When code changes

1. **Make the change and verify it** as the [workflow](workflow.md) describes.
2. **Run `bin/design-sync changed`.** It lists the modules your branch changed and their Figma nodes. README-only and editor-only files don't count. Changes to `theme.json` or `assets/scss/` show as **foundations**.
3. **Update Figma for each one that visitors would see differently**, using the Figma MCP tools and the figma-use skill:
   - **Design:** the light component or block. Change the main component, not an instance, so every use updates.
   - **Dark copy:** check it followed. Add a dark copy for anything new.
   - **Wireframe:** update it when the structure or the options change (a new layout, field or state). Colour and type changes don't touch wireframes.
   - **New module:** add all three, add the `Code:` line to the Figma descriptions, and add the `**Figma:**` line to its README.
   - Take a screenshot of each changed node, light and dark, and compare it with floe.local at 1440.
4. **Blocks page thumbnail:** if the block's look changed, rebuild its thumbnail from the new Figma design (`~/Local Sites/floe/floe-images/blocks/_source`), and push it with floe-sync after Jordan approves the plan.
5. **Fill in the pull request's `## Design sync` section**, one line per module that `changed` listed:
   - `cards: design, dark and wireframe updated (36:173, 91:369, 46:791)`
   - `posts: no visual change: editor-only`

   The **Design sync** check on GitHub fails until every listed module has a line. Editing the description re-runs it.

"No visual change" is right for refactors, performance work, accessibility fixes that don't change the look, and PHP that renders the same markup. If you're not sure, compare floe.local before and after at 375 and 1440.

## When Figma changes first

When Jordan changes a design in Figma and asks for it on the site:

1. Read the changed nodes with the Figma MCP tools (`get_design_context`, `get_screenshot`). The component's description names its code folder.
2. Build it as usual. Check the dark copy and the wireframe still match, and update them if the design change didn't reach them.
3. The pull request's `## Design sync` line says `design from Figma (<node IDs>)`.

## Audits

Pull requests keep things in step one change at a time. A full audit is the safety net for anything missed: a change without a module (for example in `includes/`), something done in the editor, or a Figma edit made without a code change.

- `bin/design-sync audit` lists modules changed since the last full audit, the commit on this page.
- To audit, compare every module and the foundations with Figma (design, dark and wireframe), fix what differs, and then set **Last full audit** above to today's date and the `dev` commit you checked. That setting goes in the pull request with the fixes.
- Do one after a busy stretch of work, or when `bin/floe-status` shows a long list.

## Alignment check

`bin/floe-status` answers "is everything in step?" in one read-only command:

- **Code:** floe.local's checkout is on `dev` and up to date, what `dev` has that live doesn't (unreleased), open pull requests, and branches whose changes aren't on `dev`.
- **Live deploy:** floewp.com runs the latest `main`.
- **Content:** floe.local and floewp.com have the same content (`bin/floe-sync status`).
- **Figma:** every module maps to Figma, and what has changed since the last audit.

It ends with "Everything is in step." or a count of things to look at. Add `--no-live` to skip the parts that need `ssh truenas`. Run it before telling Jordan that everything is in step, and pass each TODO on to him or deal with it.
