# Form

Styles only (Figma "Form field", `42:208`). Floe's own Form block and any form plugin's markup pick up these styles inside a form slot (the `.form-slot` wrapper in Contact and Newsletter), as does the native search form.

- Label: Small, ink, above the field.
- Field: 52px tall (the Figma description's value; the drawing measures 60), white, 1.5px line border, 16px radius, Body text, muted placeholder at 80%.
- Textarea: 140px tall.
- Checkbox/radio: 20px, 1.5px border, 6px radius, muted label.
- Focus: blue border plus a soft blue ring. `aria-invalid="true"` shows a red border.
- Submit buttons are styled by the Button component.
