<?php
namespace App\Presenters;
use Illuminate\Support\Facades\Storage;

class MediaPresenter
{
    function get_media_type($media_format){
        $image_format = ['gif','jpeg','jpg','png'];
        $video_format = ['avi','mp4','mov'];
        if(in_array($media_format,$image_format)) return 'image';
        if($media_format == 'url') return 'url';
        if(in_array($media_format,$video_format)) return 'video';
        return 'undefined';
    }
    function img_path($path){
        if($path == '') return '';
        if( file_exists(public_path(Storage::url($path))) ){
            return Storage::url($path);
        }else{
            return '';
        };
    }
}
?>

