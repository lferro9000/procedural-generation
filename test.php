<?php

class genNext
{
    static $index = 0;
    static $seed;
    static $original;

    public static function generate($min, $max) {
        self::nextSeed();
        $interval = $max - $min;
        return (crc32(self::$seed) % $interval) + $min;
    }

    private static function seedRandom() {
        self::$seed = self::$original = md5(random_bytes(50));
        echo 'Seed: ' . self::$seed . "\n";
    }

    private static function nextSeed() {
        if (null === self::$seed) self::seedRandom();
        self::$index++;
        self::$seed = md5(self::$index . self::$seed); // new seed
    }
}

function processImage(Imagick $image, callable $callable)
{
    $iterator = $image->getPixelIterator();
    foreach ($iterator as $row => $pixels) {
        /** @var array $pixels */
        foreach ($pixels as $col => $pixel) {
            $r = $g = $b = $callable();
            $a = 1;
            $pixel->setColor("rgba($r,$g,$b,$a)");
        }
        $iterator->syncIterator();
    }
    return $image;
}

$image = new Imagick();
$image->newImage(256, 256, new ImagickPixel('black'));
$image->setImageFormat('png');


$image = processImage($image, function () {
    return rand(0, 255);
});

file_put_contents('rand-image.png', $image);

$image = processImage($image, function () {
    return genNext::generate(0,255);
});

file_put_contents('pgen-image.png', $image);