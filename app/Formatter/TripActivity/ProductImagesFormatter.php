<?php
namespace  App\Formatter\TripActivity;

use App\Formatter\Interfaces\IFormatter;

class ProductImagesFormatter implements IFormatter
{
    function __construct()
    {
    }

    function dataFormat($data, callable $closure = null)
    {
        if(empty($data))
        {
            return [];
        }
        return $data->map(function($media){
            return [
              'url' => storageUrl($media->media['media_location_standard']),
              'is_gallery_image' => $media->is_gallery_image,
              'description_zh_tw' => $media->description_zh_tw
            ];
        });
    }
}