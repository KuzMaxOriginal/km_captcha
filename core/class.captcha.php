<?php

class Captcha {
	const LETTERS_NUMS = "1234567890";
	const LETTERS_EN_UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const LETTERS_EN_LOWER = "abcdefghijklmnopqrstuvwxyz";

	protected $_params;

	public function __construct($params=array()) {
		$_params = $params;

		$default = array(
			"width" => 600,
			"height" => 200,
			"letters" => Captcha::LETTERS_NUMS + Captcha::LETTERS_EN_LOWER + Captcha::LETTERS_EN_UPPER,
			"length" => 6
		);

		if (!isset($_params["width"]))
			$_params["width"] = $default["width"];

		if (!isset($_params["height"]))
			$_params["height"] = $default["height"];

		if (!isset($_params["letters"]))
			$_params["letters"] = $default["letters"];

		if (!isset($_params["length"]))
			$_params["length"] = $default["length"];
	}

	public function getImage() {
		return "hello";
	}
}