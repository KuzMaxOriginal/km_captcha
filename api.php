<?php

require_once "core/class.captcha.php";

$captcha = new KMCaptcha();
$captcha->next();

echo "<img src='".$captcha->getImageBase64()."'/>";