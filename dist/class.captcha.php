<?php
/**
 * KMCaptcha - captcha generator class.
 * PHP Version 5
 * @package KMCaptcha
 * @link https://github.com/KuzMaxOriginal/km_captcha The KMCaptcha GitHub project
 * @author KuzMax <maxim@kuzmax.top>
 * @copyright 2017 KuzMax
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

/**
 * KMCaptcha - captcha generator class.
 * @package KMCaptcha
 * @author KuzMax <maxim@kuzmax.top>
 */
class KMCaptcha {
	const LETTERS_NUMS = "1234567890";
	const LETTERS_EN_UPPER = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const LETTERS_EN_LOWER = "abcdefghijklmnopqrstuvwxyz";

    /**
     * Captcha generator properties.
     * @var array
     */
	public $config;

    /**
     * Last generated image.
     * @var resource
     */
	protected $_image;

    /**
     * Last generated text.
     * @var string
     */
	protected $_text;

    /**
     * KMCaptcha constructor.
     * @param array $params {
     *      Optional. Captcha generation configurations.
     *
     *      @type int    $width      Image's width. Default 300.
     *      @type int    $height     Image's height. Default 100.
     *      @type string $letters    List of characters that may be used by
     *                               captcha text generator. Default numbers
     *                               0..9 and english letters (both letter case).
     *      @type int    $length     Length of captcha text. Default 6.
     *      @type int    $font_size  Letters font size. Default 25.
     *      @type array  $font_color List of colors used to draw captcha text.
     *                               Each color passes as array(R, G, B).
     *                               Default red, green, blue, black, golden, ocean,
     *                               violette.
     * }
     */
	public function __construct($params=array()) {
		$default = array(
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
		);

		$this->config = array_merge($default, $params);
	}

    /**
     * @return string Last generated captcha text.
     */
	public function getText() {
		return $this->_text;
	}

    /**
     * @return resource Last generated captcha image.
     */
	public function getImage() {
		return $this->_image;
	}

    /**
     * @return string Last generated captcha image in base64 representation.
     */
	public function getImageBase64() {		
		ob_start();
		imagepng($this->_image);
		$data = base64_encode(ob_get_clean());

		return "data:image/png;base64,$data";
	}

    /**
     * Generates new captcha with specified configuration.
     *
     * @see _img_draw_text()
     * @see _img_distortion()
     * @see _img_noise()
     */
	public function next() {
		$image = @imagecreatetruecolor($this->config["width"], $this->config["height"]);

		imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));

		$text = "";
		for ($i = 0; $i < $this->config["length"]; $i++) {
			$text .= $this->config["letters"][rand(0, strlen($this->config["letters"])-1)];
		}

		$text_box = $this->_img_draw_text($image, $text);
		$this->_img_distortion($image, ($text_box[2] - $text_box[0])*1.4 / 2);
		$this->_img_noise($image, $this->config["width"]*$this->config["height"]*0.0006);

		$this->_image = $image;
		$this->_text = $text;
	}

    /**
     * Draws random lines on the specified image.
     * @param $image resource Instance of image used to draw on.
     * @param $difficulty int Count of lines to be drawn.
     */
    protected function _img_noise(&$image, $difficulty) {
		for ($i = 0; $i < $difficulty; $i++) {
			if (rand(0, 1) == 1) {
				$x1 = 0;
				$y1 = rand(0, $this->config["height"]);
			} else {
				$x1 = rand(0, $this->config["width"]);
				$y1 = 0;
			}

			if (rand(0, 1) == 1) {
				$x2 = $this->config["width"];
				$y2 = rand(0, $this->config["height"]);
			} else {
				$x2 = rand(0, $this->config["width"]);
				$y2 = $this->config["height"];
			}

			$color_gray = rand(150, 230);

			imageline($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, $color_gray, $color_gray, $color_gray));
		}
	}

    /**
     * Draws a text label on the specified image.
     * Uses random color specified in $config.
     * Uses random font file from ./assets/fonts/.
     * @param $image resource Instance of image used to draw on.
     * @param $text string Text that will be drawn on the image.
     * @return array Text box's coordinates array.
     */
    protected function _img_draw_text(&$image, $text) {
		$text_size = $this->config["font_size"];

		$font_list = glob(__DIR__."/assets/fonts/*.*");
		$text_fontfile = $font_list[array_rand($font_list)];

		// -> Detecting drawn text box's coordinates for centering the text

		$text_box = imageftbbox($text_size, 0, $text_fontfile, $text);

		$center_x = ($text_box[0] + $text_box[2]) / 2;
		$center_y = ($text_box[1] + $text_box[5]) / 2;

		$color_args_array = $this->config["font_color"][array_rand($this->config["font_color"])];
		array_unshift($color_args_array, $image);
		$text_color = call_user_func_array("imagecolorallocate", $color_args_array);

		$text_x = $this->config["width"] / 2 - $center_x;
		$text_y = $this->config["height"] / 2 - $center_y;

		return imagefttext($image, $text_size, 0, $text_x, $text_y, $text_color, $text_fontfile, $text);
	}

    /**
     * Distort the image using "swirl" function.
     * @link http://geekofficedog.blogspot.com/2013/04/hello-swirl-swirl-effect-tutorial-in.html
     * @param $image resource Instance of image used to draw on.
     * @param $radius int Radius of distortion.
     */
    protected function _img_distortion(&$image, $radius) {

		// Clone image for the pixels map using

		$old_image = @imagecreatetruecolor($this->config["width"], $this->config["height"]);
		imagecopy($old_image, $image, 0, 0, 0, 0, $this->config["width"], $this->config["height"]);

		// Loop through the pixels and distort the image

		for ($px = 0; $px < $this->config["width"]; $px++) {
			for ($py = 0; $py < $this->config["height"]; $py++) {
				$image_width = imagesx($old_image);
				$image_height = imagesy($old_image);

				$x = $px - $this->config["width"] / 2;
			    $y = $py - $this->config["height"] / 2;
			    $r = sqrt($x*$x+$y*$y);

			    $maxr = $radius;

			    if ($r <= $maxr) {
				    $a = atan2($y, $x);
				    $a += 1 - ($r / $maxr)*2;
				    $dx = cos($a) * $r + $this->config["width"] / 2;
				    $dy = sin($a) * $r + $this->config["height"] / 2;

				    if (($this->config["width"] > $dx && $dx >= 0
				    && $this->config["height"] > $dy && $dy >= 0))
				    	imagesetpixel($image, $px, $py, imagecolorat($old_image, $dx, $dy));
				    else imagesetpixel($image, $px, $py, imagecolorallocate($image, 255, 255, 255));
				}
			}
		}

		// Rotate image after distortion to normalize text angle

		$rotate = imagerotate($image, 0, 0xffffff);
		$image = imagecrop($rotate, array(
			"x" => imagesx($rotate) / 2 - $this->config["width"] / 2,
			"y" => imagesy($rotate) / 2 - $this->config["height"] / 2,
			"width" => $this->config["width"],
			"height" => $this->config["height"]
		));
	}
}