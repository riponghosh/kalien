<?php
namespace App\Http\Controllers;
use App\Services\TripService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TripIntroductionController
{
    protected $TripService;

    public function __construct(TripService $TripService)
    {
        $this->TripService = $TripService;
    }

    public function create_trip(Request $request){
        $publish = $request->publish;
        $trip =  $this->TripService->create_trip(Auth::user()->id, $publish);
        if(!$trip) return ['success' => false];
        return ['success' => true, 'trip_id' => $trip->trip_id, 'trip_status' => $trip->trip_status ];
    }
    public function delete_trip(Request $request){
        $result = $this->TripService->del_trip($request->trip_id, Auth::user()->id);
        if(!$result) return ['success' => false];
        return ['success' => true];
    }
    public function published_trip(Request $request){
        $result = $this->TripService->update_trip_status($request->trip_id, $request->status, Auth::user()->id);
    }
    public function update_trip(Request $request){
        $result = $this->TripService->update_trip($request->trip_id, $request, Auth::user()->id);
        if(!$result) return ['success' => false];
        return ['success' => true];
    }
    public function upload_main_trip_media(Request $request){
        if(!$request->hasFile('media')) return ['success' => false];
        $validator = Validator::make($request->all(),[
            'media' => 'mimes:jpeg,bmp,png,gif|max:20480',
        ]);
        if($validator->fails()){
            return ['success' => false];
        }
        return $this->TripService->upload_trip_media($request->trip_id, $request->File('media'), Auth::user()->id, $request->order);

    }
    public function upload_main_trip_media_url(Request $request){
        //youtube
        $validator = Validator::make($request->all(),[
            'media_link' => 'youtube_url'
        ]);
        if($validator->fails())return ['success' => false ,'msg' => $validator->errors()->all()];
        $result = $this->TripService->upload_trip_main_media_link($request->trip_id, $request->media_link, Auth::user()->id, $request->order);
        if($result['success'] = false) return ['success' => false];
        return ['success' => true];

    }
    public function remove_main_trip_media(Request $request){
        return $this->TripService->remove_main_trip_media($request->trip_id, $request->media_id, Auth::user()->id, $request->order, 'img');
    }
    /*移除youtube連結*/
    public function remove_main_trip_media_url(Request $request){
        return $this->TripService->remove_main_trip_media($request->trip_id, $request->media_id, Auth::user()->id, $request->order, 'video_url');
    }
}
?>

