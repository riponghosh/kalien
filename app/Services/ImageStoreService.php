<?php

namespace App\Services;
use League\Flysystem\Exception;

class ImageStoreService
{

    const IMG_PATH = 'img';
    function __construct()
    {
    }


    /**
     * Apply a given search value to the builder instance.
     *
     * @param
     * @param mixed $value
     * @return array $array
     */

    function store($file, $path, $img_name = ''){
        if(empty($img_name)) throw new Exception('image name cant blank');
        $img_hash_name = sha1($img_name.strtotime('now').rand(100,999));
        $dir = self::IMG_PATH.'/'.$path;
        $format_name = $this->get_media_format($file);
        $path = $dir;
        $file_name = $img_hash_name.'.'.$format_name;
        $full_path = $path.'/'.$file_name;

        if(!$file->storeAs($path, $file_name)) throw new Exception('store image failed');
        return ['img_path' => $full_path, 'img_format' => $format_name];
    }

    private function get_media_format($media){
        $media_mineType = explode('/',$media->getMimeType());
        $media_type = $media_mineType[0];   //例：image,application,video
        $media_format = $media_mineType[1];

        return $media_format;
    }
}