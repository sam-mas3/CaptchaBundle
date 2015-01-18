<?php
namespace stz184\CaptchaBundle\Generator;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Config\FileLocator;

class Captcha
{
    protected $config = array();
    protected $text;

    public function __construct(FileLocator $fileLocator, array $config)
    {
        $this->config = $config;
        $this->config['font_path'] = $fileLocator->locate('@' . $config['font_path']);
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param string|array $color
     * @return array
     */
    protected function getColor($color) {
        if (is_array($color) && isset($color['r']) && isset($color['g']) && isset($color['b'])) {
            return $color;
        }

        if(!preg_match('/^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/', $color)) {
            return false;
        }

        $hex = str_replace("#", "", $color);

        $color = array();
        if(mb_strlen($hex) == 3) {
            $r = mb_substr($hex, 0, 1);
            $g = mb_substr($hex, 1, 1);
            $b = mb_substr($hex, 2, 1);
            $color['r'] = hexdec($r.$r);
            $color['g'] = hexdec($g.$g);
            $color['b'] = hexdec($b.$b);
        }
        else if(mb_strlen($hex) == 6) {
            $color['r'] = hexdec(mb_substr($hex, 0, 2));
            $color['g'] = hexdec(mb_substr($hex, 2, 2));
            $color['b'] = hexdec(mb_substr($hex, 4, 2));
        }

        return $color;
    }

    protected function pickUpRandom(array $items) {
        shuffle($items);
        return reset($items);
    }

    public function generate()
    {
        $config 			= $this->config;
        $imageWidth 		= $config['width'];
        $imageHeight 		= $config['height'];
        $hasBackgroundNoise = $config['background_noise'];

        /** @var resource $imageResource */
        if (false == ($imageResource = @imagecreate($imageWidth, $imageHeight))) {
            throw new Exception("Cannot Initialize new GD image stream");
        }


        $backgroundColorCode = $this->getColor(
            $this->pickUpRandom($this->config['background_color'])
        );

        imagecolorallocate(
            $imageResource,
            $backgroundColorCode['r'],
            $backgroundColorCode['g'],
            $backgroundColorCode['b']
        );

        if ($hasBackgroundNoise && isset($this->config['noise_color'])) {
            $noiseColorCode = $this->getColor(
                $this->pickUpRandom($this->config['noise_color'])
            );

            $noiseColor = imagecolorallocate(
                $imageResource,
                $noiseColorCode['r'],
                $noiseColorCode['g'],
                $noiseColorCode['b']
            );

            // Generate random lines in background
            for ($i=0; $i < $imageHeight * 0.25; $i++) {
                imageline(
                    $imageResource,
                    0, //mt_rand(0,$imageWidth),
                    mt_rand(0,$imageHeight),
                    mt_rand($imageWidth / 2, $imageWidth),
                    mt_rand(0,$imageHeight),
                    $noiseColor
                );
            }

            for ($i=0; $i < $imageHeight * $imageWidth * 0.33; $i++) {
                imagefilledellipse($imageResource, mt_rand(0, $imageWidth), mt_rand(0, $imageHeight), 1, 1, $noiseColor);
            }
        }

        $textColor = array();
        foreach ($this->config['font_color'] as $colorCode) {
            $colorCode = $this->getColor($colorCode);
            $textColor[] = imagecolorallocate(
                $imageResource,
                $colorCode['r'],
                $colorCode['g'],
                $colorCode['b']);
        }

        shuffle($textColor);
        $colorVariants = count($textColor);

        for($j = 0; $j < mb_strlen($this->text); $j++) {
            imagettftext(
                $imageResource,
                20,
                0,
                5 + ($j * 23),
                24,
                $textColor[$j % $colorVariants],
                $this->config['font_path'],
                $this->text[$j]
            );
        }

        ob_start();
        imagepng($imageResource);
        $imageString = ob_get_clean();
        imagedestroy($imageResource);

        return $imageString;
    }
} 