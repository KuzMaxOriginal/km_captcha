<?php

require_once "../dist/class.captcha.php";

DEFINE("EXAMPLE_SALT", 'my CuStOm $aLT');

$msg = "";

if (isset($_POST["hex"]) && isset($_POST{"answer"})) {
    $hash = hash("sha256", $_POST["answer"].EXAMPLE_SALT);
    $msg = $hash == $_POST["hex"] ? "<span>Correct Answer!</span>"
        : "<span class='error'>Incorrect answer! Try again!</span>";
}

$captcha = new KMCaptcha(array(
    "width" => 500,
    "height" => 300,
    "font_size" => 66,
    "letters" => KMCaptcha::LETTERS_NUMS . KMCaptcha::LETTERS_EN_UPPER,
    "length" => 8,
));
$captcha->next();

// Use hash with salt to test the answer
$hash = hash("sha256", $captcha->getText().EXAMPLE_SALT);

?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>KMCaptcha Example #1</title>
        <style>
            * {
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }
            
            body {
                font-family: sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                position: fixed;
                height: 100%;
                width: 100%;
                flex-direction: column;
            }

            form {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                border: 3px solid black;
                border-radius: 10px;
            }

            form img {
                border-bottom: 3px solid black;
                border-radius: 10px 10px 0 0;
            }

            form input[type="text"] {
                width: 100%;
                height: 50px;
                border: 0;
                padding: 0 25px;
                font-size: 20px;
                border-radius: 0 0 10px 10px;
            }

            span {
                font-size: 35px;
                color: limegreen;
                margin-bottom: 20px;
            }

            span.error {
                color: red;
            }
        </style>
    </head>
    <body>
        <?= $msg ?>
        <form method="post">
            <img src="<?= $captcha->getImageBase64() ?>"/>
            <input type="hidden" name="hex" value="<?= $hash ?>"/>
            <input type="text" name="answer" value="" placeholder="Enter CAPTHCA, then press 'Enter'"/>
        </form>
    </body>
</html>