<?php

require_once "core/class.captcha.php";

$captcha = new Captcha;
$captcha->nextImage();

echo "<img src='".$captcha->getImageBase64()."'/>";