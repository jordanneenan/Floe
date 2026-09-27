# Play control

**Figma:** design [30:98](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=30-98) · dark [91:397](https://www.figma.com/design/F43LfH93WZls4WkgIo5e3n?node-id=91-397)

The YouTube facade button (Figma `30:98`): a frosted pill with a blue play circle, "Play video" and an optional duration. The Video block creates the privacy-enhanced iframe only after it is pressed.

```php
echo Floe\component( 'play-control', [ 'title' => 'How Floe works', 'duration' => '2:14' ] );
```

The accessible name is "Play video: <title>".
