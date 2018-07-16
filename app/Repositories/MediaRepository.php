<?php

namespace App\Repositories;
use App\Media;
use Embed\Embed;

class MediaRepository
{
    public $media;

    /*
     * Directory
     * */
    const IMG = 'img/';
    const FILE_PUT_CONTENTS_IMG = '../storage/app/public/img/';
    const User_icon_orginal_path = 'img/userIconOrginal';
    const User_icon_path = 'img/userIcon';

    function __construct(Media $media)
    {
        $this->media = $media;
    }
    public function insert_url($media_url,$user_id){
        $query = $this->media->create([
            'media_author' => $user_id,
            'media_location_standard' => $media_url,
            'media_format' => 'url'
        ]);
        if(!$query) return false;
        return $query->media_id;
    }
    
    public function upload_media($media, $standard_path, $name, $author_id, $low_qual_media=NULL, $low_qual_path=NULL){
        if(!$media || $media == null) return ['success' => false];
        if($name == '' || $name == null) return ['success' => false];
        if($standard_path == '' || $standard_path == null) return ['success' => false];
        if($author_id == '' || $author_id == null) return ['success' => false];
        if(($low_qual_path == '' || $low_qual_path == null) || ($low_qual_media == null)){
            $low_qual_path = $standard_path;
        }

        /*path 處理*/
        $standard_path = self::IMG.$standard_path;
        $low_qual_path = self::IMG.$low_qual_path;
        if(!$media->storeAs($standard_path, $name.'.'.$this->get_media_format($media)) ) return ['success' => false, 'msg' => 'fail to upload orginal size media'];
        if($standard_path != $low_qual_path){
            if(!$low_qual_media->storeAs($low_qual_path, $name.'.'.$this->get_media_format($media)) ) return ['success' => false, 'msg' => 'fail to upload low quality size media'];
        }
        /*get format*/
        $userIconOrgin_format = $this->get_media_format($media);
        /*寫入mysql*/
        $query = Media::create([
            'media_author' => $author_id,
            'media_location_standard' => $standard_path.'/'.$name.'.'.$userIconOrgin_format,
            'media_location_low' => $low_qual_path.'/'.$name.'.'.$userIconOrgin_format,
            'media_format' => $userIconOrgin_format
        ]);
        if(!$query) return ['success' => false];
        return [
            'success' => true,
            'standard_path' => $standard_path.'/'.$name.'.'.$this->get_media_format($media),
            'low_qual_path' => $low_qual_path.'/'.$name.'.'.$this->get_media_format($media),
            'format' => $this->get_media_format($media),
            'media_id' => $query->media_id
        ];

    }

    public function upload_media_by_url($media_url, $standard_path, $name, $author_id, $low_qual_media_url=NULL, $low_qual_path=NULL){
        $media_format = 'jpeg';
        if(!$media_url || $media_url == null) return ['success' => false];
        if($name == '' || $name == null) return ['success' => false];
        if($standard_path == '' || $standard_path == null) return ['success' => false];
        if($author_id == '' || $author_id == null) return ['success' => false];
        if($low_qual_path == '' || $low_qual_path == null){
            $low_qual_path = $standard_path;
        }
        $info_standard = Embed::create($media_url);

        if($low_qual_media_url == null ||  $low_qual_media_url == '' ||$media_url ==  $low_qual_media_url){
            $info_low_qual = $info_standard;
        }else{
            $info_low_qual = Embed::create($low_qual_media_url);
        }
        /*path 處理*/
        $standard_path_for_save = self::FILE_PUT_CONTENTS_IMG.$standard_path;
        $low_qual_path_for_save = self::FILE_PUT_CONTENTS_IMG.$low_qual_path;
        /*儲存圖片*/
        try{
            $save_standard_img = $this->dl_file_and_save($info_standard->url, $standard_path_for_save.'/'.$name.'.'.$media_format);
            $save_low_img = $this->dl_file_and_save($info_low_qual->url, $low_qual_path_for_save.'/'.$name.'.'.$media_format);

        }catch (\Exception $e){
            return false;
        }
        /*寫入mysql*/
        $query = Media::create([
            'media_author' => $author_id,
            'media_location_standard' => self::IMG.$standard_path.'/'.$name.'.'.$media_format,
            'media_location_low' => self::IMG.$low_qual_path.'/'.$name.'.'.$media_format,
            'media_format' => $media_format
        ]);
        if(!$query) return ['success' => false];
        return [
            'success' => true,
            'standard_path' => self::IMG.$standard_path.'/'.$name.'.'.$media_format,
            'low_qual_path' => self::IMG.$low_qual_path.'/'.$name.'.'.$media_format,
            'format' => $media_format,
            'media_id' => $query->media_id
        ];
    }

    public function update_media_info($type, $media_id, $data, $user_id){
        $types = ['media_description','media_title'];
        if(!in_array($type,$types)) return false;
        $update = $this->media->where('media_author',$user_id)->where('media_id',$media_id)->update($data);
        return $update;
    }
    private function get_media_format($media){
        $media_mineType = explode('/',$media->getMimeType());
        $media_type = $media_mineType[0];   //例：image,application,video
        $media_format = $media_mineType[1];

        return $media_format;
    }
    public function dl_file_and_save($file,$newfile){
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
// 初始化一個 cURL 對象
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5 FirePHP/0.2.1");

        //curl_setopt($curl, CURLOPT_REFERER, "http://www.xxx.com.tw/"); //有時候需要設定該網站網址才能抓取圖片
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
// 設置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, $file);

// 設置header
        curl_setopt($curl, CURLOPT_HEADER, 0);

// 設置cURL 參數，要求結果保存到字符串中還是輸出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

// 運行cURL，請求網頁
        $data = curl_exec($curl);

// 關閉URL請求
        curl_close($curl);

//寫入獲得的數據
        $write = @fopen($newfile,"w");
        fwrite($write,$data);
        fclose($write);
//判斷是否為圖片
        if (!getimagesize($newfile)) return false;
        else return TRUE;
    }
}

?>