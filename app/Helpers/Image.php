<?php


namespace App\Helpers;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class Image
{
    const SIZE = 1000;

    /**
     * The method of uploading an image using the specified path
     *
     * @param object $image
     * @param string $name
     * @param string $dir
     * @return string
     */
    public static function upload(object $image, string $name, string $dir)
    {
        $path = storage_path('app').'/'.$dir;
        $manager = new ImageManager(Driver::class);
        $resizedImage = $manager->read($image);
        $resizedImage->scaleDown(width: self::SIZE);

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $resizedImage->save($path .'/'. $name);
        return $path . $name;
    }

    /**
     * The method uses the image format in $image param
     *
     * @param string $image
     * @return string
     */
    public static function resizeEncodeBase64(string $image): string
    {
        $manager = new ImageManager(new Driver());
        $resizedImage = $manager->read($image);
        $resizedImage->scaleDown(width: self::SIZE);
        $base64_resized = base64_encode($resizedImage->encode());
        return  'data:image/jpeg;base64,' . $base64_resized;
    }

    /**
     * @param string $base64String
     * @return false|string
     */
    public static function base64Decode(string $base64String): string
    {
        $data = substr($base64String, strpos($base64String, ',') + 1);
        return base64_decode($data);
    }

}
