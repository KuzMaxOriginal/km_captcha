<?php

class Captcha {
	const LETTERS_NUMS = "1234567890";
	const LETTERS_EN_UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const LETTERS_EN_LOWER = "abcdefghijklmnopqrstuvwxyz";

	protected $_params; // Captcha configurations
	protected $_image; // Last generated image
	protected $_text; // Last generated text

	public function __construct($params=array()) {
		$default = array(
			"width" => 300,
			"height" => 100,
			"letters" => Captcha::LETTERS_NUMS . Captcha::LETTERS_EN_LOWER . Captcha::LETTERS_EN_UPPER,
			"length" => 8,
			"font_size" => 25,
			"font_angle_range" => array(-10, 10),
			"font_color" => array(
				array(255, 0, 0),
				array(0, 128, 0),
				array(0, 0, 255),
				array(0, 0, 0),
				array(255, 128, 0),
				array(0, 128, 255),
				array(255, 0, 255)
			)
		);

		$this->_params = array_merge($default, $params);
	}

	public function getText() {
		return $this->_text;
	}

	public function getImage() {
		return $this->_image;
	}

	public function getImageBase64() {		
		ob_start();
		imagepng($this->_image);
		$data = base64_encode(ob_get_clean());

		return "data:image/png;base64,$data";
	}

	public function nextImage() {

		// Create image object

		$image = @imagecreatetruecolor($this->_params["width"], $this->_params["height"]);

		// Generate captcha text

		$text = "";
		for ($i = 0; $i < $this->_params["length"]; $i++) {
			$text .= $this->_params["letters"][rand(0, strlen($this->_params["letters"])-1)];
		}

		// Set background color
		
		imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));

		// Set background noise

		for ($i = 0; $i < 20; $i++) {
			if (rand(0, 1) == 1) {
				$x1 = 0;
				$y1 = rand(0, $this->_params["height"]);
			} else {
				$x1 = rand(0, $this->_params["width"]);
				$y1 = 0;
			}

			if (rand(0, 1) == 1) {
				$x2 = $this->_params["width"];
				$y2 = rand(0, $this->_params["height"]);
			} else {
				$x2 = rand(0, $this->_params["width"]);
				$y2 = $this->_params["height"];
			}

			$color_gray = rand(150, 230);

			imageline($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, $color_gray, $color_gray, $color_gray));
		}

		// Draw text

		$text_size = $this->_params["font_size"];
		$text_agle = rand($this->_params["font_angle_range"][0], $this->_params["font_angle_range"][1]);
		$text_fontfile = __DIR__."/assets/fonts/OpenSans-Bold.ttf";

		// -> Detecting drawn text box's coordinates for centering the text
		
		$text_box = imageftbbox($text_size, $text_agle, $text_fontfile, $text);

		$min_x = $this->_params["width"];
		$min_y = $this->_params["height"];
		$max_x = 0;
		$max_y = 0;

		for ($i = 0; $i < 4; $i++) {
			$coord_x = $text_box[$i * 2];
			$coord_y = $text_box[$i * 2 + 1];

			if ($min_x > $coord_x)
				$min_x = $coord_x;

			if ($max_x < $coord_x)
				$max_x = $coord_x;

			if ($min_y > $coord_y)
				$min_y = $coord_y;

			if ($max_y < $coord_y)
				$max_y = $coord_y;
		}

		$center_x = ($min_x + $max_x) / 2;
		$center_y = ($min_y + $max_y) / 2;

		$color_args_array = $this->_params["font_color"][rand(0, count($this->_params["font_color"])-1)];
		array_unshift($color_args_array, $image);
		$text_color = call_user_func_array("imagecolorallocate", $color_args_array);

		$text_x = $this->_params["width"] / 2 - $center_x;
		$text_y = $this->_params["height"] / 2 - $center_y;

		imagefttext($image, $text_size, $text_agle, $text_x, $text_y, $text_color, $text_fontfile, $text);

		$this->_image = $image;
		$this->_text = $text;
	}
}