# File row

One downloadable file (Figma `30:89`). The whole row is a single link.

```php
echo Floe\component( 'file-row', [ 'id' => $attachment_id, 'title' => '' ] );
```

- **Title:** the override if given, otherwise the attachment title from the media library.
- **Type tile:** a short code from the extension (DOCX → DOC, XLSX → XLS, PPTX → PPT, otherwise the extension).
- **Meta:** the real extension and the size, e.g. `PDF · 2.4 MB`, read from the attachment metadata (`filesize`), not from the file system.
- The download arrow is decorative (`aria-hidden`); the link text is the title and meta.

Hover: the arrow button fills blue and the title underlines.
