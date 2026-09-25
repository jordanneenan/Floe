# Play control

The YouTube facade button (Figma `30:98`): a frosted pill with a blue play circle, "Play video" and an optional duration. The Video block creates the privacy-enhanced iframe only after it is pressed.

```php
echo Floe\component( 'play-control', [ 'title' => 'How Floe works', 'duration' => '2:14' ] );
```

The accessible name is "Play video: <title>".
