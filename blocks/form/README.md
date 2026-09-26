# Form (`floe/form`)

Floe's built-in form, so a site doesn't need a form plugin. It goes in the form slot of **Contact** or **Newsletter** and takes the type that suits where it's added.

| Type | Fields |
| --- | --- |
| Enquiry form | First name (required) and last name side by side, email (required), organisation, "How can we help?" (required), optional consent checkbox, "Send message" (Figma `44:263`). |
| Newsletter signup | Email address and "Subscribe" on one line (Figma `45:363`). |

| Setting | Notes |
| --- | --- |
| Type | Enquiry form or Newsletter signup. |
| Button label | Edited in place. Defaults to "Send message" or "Subscribe". |
| Ask for consent | Enquiry form only. The label is edited in place and may contain a link, e.g. to a privacy page. |
| Thank-you message | Shown in place of the form once it's sent. |

## Where entries go

- **Enquiries** in the admin menu (editors and administrators): name, email, organisation, message, the page it came from, and whether the email alert went out. Filter by Enquiry or Newsletter. Entries have no public URL and aren't searchable.
- **An email alert** to the site's administration email address (Settings → General). Replying to it replies to the sender. Change the address with the `floe_form_recipient` filter.

The entry is saved before the email is sent, so nothing is lost if the site's email isn't working. For reliable email, set up SMTP in `wp-config.php` (see `includes/mail/smtp.php`).

## Behaviour

- Posts to `wp-admin/admin-post.php` (action `floe_form`). Without JavaScript the page reloads with the result; with it (`form.js`) the form sends in the background, shows errors beside it and swaps in the thank-you message.
- Spam: a hidden field that people never fill in, a signed timestamp (a send within two seconds of the page loading is refused) and five sends per visitor every ten minutes. No nonce, so cached pages keep working.
- Server code is in `form-server.php`. Deleting this folder removes the block, its handler and the Enquiries screen; saved entries stay in the database.

**Components used:** Button (`type` submit), Icon, Form (styles).
