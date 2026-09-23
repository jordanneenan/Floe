# Floe block and component design brief

**Status:** first-pass designs and theme block implementation. Use [the Figma file](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n) for visual exploration and [the block contract](blocks.md) for implemented behaviour. Made 4 is a reference for use cases, not a source to copy or a definition of current best practice.

## Figma design progress

The file now has reusable desktop wireframe and designed components for all ten initial sections, plus the separate Video section. Home Banner is a large split composition; Page Banner is a compact interior-page introduction. The designed library also contains shared Button, inverse Button, Navigation, Header, Footer, Media slot, Content card, File action, and Video play control components. Posts and Cards use instances of the same Content card; the Images rows use instances of the Media slot. Editing those main components updates their Figma instances.

These are first-pass layouts for reviewing the section hierarchy and visual rhythm. The typography and dark green action colour are provisional neutral defaults, not Floe brand rules. The current Figma components show representative content at 1440px. Mobile/tablet/large-desktop layouts, absent optional fields, long-content stress cases, accessibility states, and the final approved variant controls still need design review. Media slots are intentionally placeholders until each site supplies its imagery or MP4. The Brochure Site page remains reserved for the later full-page design phase.

The corresponding Gutenberg blocks now have a first implementation in Floe. Their current fields and responsive rules are documented in [the block contract](blocks.md). WordPress code will still need revision when the remaining Figma variants are approved.

## Design approach

- A Floe block is a complete page section. Its structure, responsive behaviour, and content hierarchy are designed as a unit. Editors choose a section and supply content rather than assemble its layout from individual headings, images, and buttons.
- Base block designs should be visually neutral. A site's typography, colour, imagery, graphics, and selected variants provide its identity. Controls should expose meaningful choices without requiring the editor to design the section.
- A Figma block has a wireframe, a designed component, an eventual WordPress block, and a client-facing usage entry under the same name. A change to a Figma main component updates its Figma instances. Updating the running WordPress site still requires a reviewed code change.
- Shared pieces that are not page sections belong in the component library: header, navigation, footer, buttons, card presentation, media presentation, and video play control. They can be nested in block designs and page designs as Figma instances.
- Complete the block and component library before designing the Floe brochure site. The file has three primary pages: **Block Wireframes**, **Block Designs + Components**, and **Brochure Site**.

## Section catalogue

The following ten sections are the first design set. Home Banner and Page Banner are separate blocks because they often have substantially different layouts. **Video** is an additional section to design; **Spacing** already exists but needs its spacing interaction revised.

| Section | Editor content and behaviour | Design boundary |
| --- | --- | --- |
| **Home Banner** | Homepage opening with title, supporting copy, optional action, and a visual media slot. | Separate composition from Page Banner. Define responsive layouts and media treatment in Figma. |
| **Page Banner** | Interior-page opening with title, optional copy, optional action, and a visual media slot. | Own component and block; do not treat it as only a Home Banner colour or size variant. |
| **Article** | Unrestricted WYSIWYG content with an optional button below the editor content. A short Article can serve as a simple call to action. | Keep the text area straightforward. Background colour becomes an approved site-level choice when background-colour design is defined. |
| **Image + Copy** | Image or looping MP4 alongside title, copy, and optional action. | Layout choices such as media left/right need fixed responsive rules. The number of pairs per section remains a design decision. |
| **CTA** | Focused message and action, with optional supporting content or media. | Distinguish its purpose from a short Article through the layout and editing guidance, rather than brand-specific styling. |
| **Testimonial** | Quote and attribution, with optional portrait or other visual media. | Keep quote, attribution, and optional media relationships fixed. |
| **Posts** | A section fed by WordPress content. Define latest/filtered/selected modes, count, and shared card presentation during design. | Dynamic content source; manual entries belong in Cards. |
| **Cards** | Manually entered card items with title, copy, optional action, and a visual media slot. | Reuse the card presentation component from Posts while keeping the data source separate. |
| **Document Download** | Introductory copy if needed, followed by named downloadable files. | File action and metadata presentation are shared components. |
| **Images** | Repeatable rows. Each row chooses an approved layout and contains image or looping MP4 slots. | Reference Made's one-, two-, and three-column row choices, image aspect-ratio/fill choice, and optional removal of internal grid spacing. Reassess the exact variants in wireframes. |
| **Video** | YouTube video with a cover image and clear play control. The player is opened/loaded after the visitor acts. | This is distinct from a silent looping MP4 used in a visual media slot. |

### Article contract

Article is intentionally the flexible exception in the section library. The content area is a WYSIWYG editor, and an optional button follows it. An approved background-colour option can later make it a more prominent section. The editor decides the content structure inside Article; the theme supplies the section width, spacing, responsive treatment, and button presentation. Images inserted within the WYSIWYG area need the same image-or-looping-video option as other visual content; the editor mechanism for this remains to be designed.

### Images contract

Use the name **Images**, not Image Grid. An Images section can contain multiple rows, and each row can use an approved layout. Made's Images block is the behavioural reference for one-, two-, and three-column rows, a choice between filling the container and retaining a source aspect ratio, and optional internal grid spacing. Each media slot can hold an image or a self-hosted looping MP4. The exact set of layouts and editor controls must be confirmed from neutral wireframes before implementation.

## Shared visual media contract

Every image slot outside iconography should offer an **image** or **self-hosted looping video** choice. This applies to banners, Image + Copy, Cards, Images, Article content, and other visual slots where the layout supports media. Icons stay as icons.

| Media choice | Source | Visitor behaviour | Required design states |
| --- | --- | --- | --- |
| Image | WordPress Media Library | Static visual | Loaded image, missing image, crop/fill, responsive sizes, alternative text where meaningful |
| Looping video | MP4 in WordPress Media Library | Autoplay, muted, looped, inline, without controls | Poster/fallback image, loading state, unavailable video, reduced-motion treatment |
| Video section | YouTube URL or ID plus cover image | Cover with play control; visitor starts playback | Cover, hover/focus, loading, player open, unavailable video |

The looping video is visual media, not the Video section. It should never require sound to convey essential information. The fallback image is part of the authoring contract. Reduced-motion behaviour and performance limits need agreement during design; a poster in place of autoplay is the proposed reduced-motion default.

## Spacing interaction to design

Every top-level Floe section has a default gap after it. A Spacing block placed immediately after a section **replaces** that gap, including a **No spacing** choice that makes the gap zero. Presets are Large, Medium, Small, and No spacing. Custom mode needs values for large desktop, desktop, tablet, and mobile; an unset large-desktop value can inherit desktop. Proposed boundaries are large desktop `>=1280px`, desktop `768–1279px`, tablet `550–767px`, and mobile `<550px`.

This behaviour is implemented in the theme. See [the block contract](blocks.md) for the current values and editor fields.

## Figma and implementation sequence

1. Define the shared components and neutral tokens. In particular, design a media slot, button, card, file action, and play control in addition to header/navigation/footer.
2. Wireframe each section, including absent optional content, long content, and the four responsive ranges. Create separate Home Banner and Page Banner wireframes.
3. Create designed Figma components from the approved wireframes. Use instances wherever a section or shared component appears in another design.
4. Translate approved sections to Floe blocks. Keep field names, variants, media rules, and client documentation aligned with the Figma component. Review and test code before updating the live site.
5. Design the Floe brochure site using the approved Figma instances. Its proposed content is a clear introduction, block examples, editing model, documentation, and repository link.

## Decisions still open

- Exact visual layouts and allowed variants for each section, especially both banners and Images.
- Whether Image + Copy contains one pair per block or repeatable pairs within one block.
- Posts query/filter controls and the shared card presentation.
- Approved background colours and which roles may change them.
- Video fallback, reduced-motion, and loading details.
