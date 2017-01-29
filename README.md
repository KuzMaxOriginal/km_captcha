# KMCaptcha - a simple PHP class for generation nice and secure captcha!

Welcome to KMCaptcha GitHub page! My small library allows you to create CAPTCHA image to prevent bots attack or checking user for some brains existance ;) OK, here you are.

## Basic usage
The simplest way to use it:
```php
<?php

// Include KMCaptcha php file
require_once "./class.captcha.php";

// ... some code above

$captcha = new KMCaptcha(); // Create KMCaptcha instance with default configuration
$captcha->next(); // Generate new captcha

$answer = $captcha->getText(); // Retrives the correct answer

echo '<img src="'.$captcha->getImageBase64().'"/>'; // This function returns base64 image representation

// Some code below...
```
The code shows recent generated captcha image and stores right answer to `$answer`.

## Best way to check

I highly recommend you to see examples in `/examples/` folder to study the best way to use it and check the answer. Howewer, the easiest way to post data:
```php
<?php

...

$hash = hash("sha256", $captcha->getText()."Some Salt Here");

?>

...

<form>
    <input type="hidden" name="hex" value="<?= $hash ?>"/>
    <input type="text" name="answer" value=""/>
</form>

...
```
And check it:
```php
...

$hash = hash("sha256", $_GET["answer"]."Some Salt Here");

if ($hash == $_GET["hex"]) {
    // The answer is correct!
}

...
```

## Settings
In order to customize CAPTCHA's properties, pass array of desired options to KMCaptcha's constructor:
```php
...

$captcha = new KMCaptcha(array(
    "width" => 300,
    "height" => 100,
    "letters" => KMCaptcha::LETTERS_NUMS . KMCaptcha::LETTERS_EN_LOWER . KMCaptcha::LETTERS_EN_UPPER,
    "length" => 6,
    "font_size" => 30,
    "font_color" => array(
      array(255, 0, 0),
      array(0, 128, 0),
      array(0, 0, 255),
      array(0, 0, 0),
      array(255, 128, 0),
      array(0, 128, 255),
      array(255, 0, 255)
    )
));

...
```
All the values above are equaled to default.

Here is a complete list of arguments and it's description:
* **width** - CAPTCHA's image width
* **height** - CAPTCHA's image height
* **letters** - List of characters that may be used by captcha text generator.
* **length** - Length of CAPTCHA's text.
* **font_size** - Letters font size.
* **font_color** - List of colors used to draw captcha text. Each color passes as array(R, G, B).

## Fonts
In the KMCaptcha file directory, there's assets/fonts. Here, each file must be a valid font, which used to generate image text (choosing randomly). You can put your own fonts also.

## Additional information
Live example: http://own.kuzmax.top/captcha/examples/simple_form.php

All general php files (1 now) are documented detaily and has much more information about functions and features.
