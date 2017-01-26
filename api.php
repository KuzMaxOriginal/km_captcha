<?php

require_once "core/class.captcha.php";

$captcha = new Captcha;

echo $captcha->getImage();