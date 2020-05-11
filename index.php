<?php

require './SimpleImage.php';

function convertImageToWebP($source, $destination, $quality = 80)
{
    $extension = pathinfo($source, PATHINFO_EXTENSION);
    if ($extension == 'jpeg' || $extension == 'jpg')
        $image = imagecreatefromjpeg($source);
    elseif ($extension == 'gif')
        $image = imagecreatefromgif($source);
    elseif ($extension == 'png')
        $image = imagecreatefrompng($source);
    return imagewebp($image, $destination, $quality);
}

function createThumb($src, $with, $height)
{
    $key = md5($src . '_' . $with . '_' . $height);
    $tmp = "./.thumbtmp/$key";
    $content = "";
    if (is_readable($tmp)) {
        $content = file_get_contents($tmp);
    } else {
        try {
            $image = new SimpleImage();
            $image->fromFile($src);
            if ('image/webp' != $image->getMimeType()) {
                $newSrc = $tmp . '.webp';
                convertImageToWebP($src, $newSrc, 100);
                $image = new SimpleImage();
                $image->fromFile($newSrc);
            }
            unlink($newSrc);
            $image
                ->thumbnail($with, $height, 440);
            $content = $image->toString();
            file_put_contents($tmp, $content);
        } catch (Exception $err) {
            $content = "";
        }
    }
    header("Content-Type: image/webp");
    echo $content;
}

createThumb('banner-k.jpg', 990, 440);
