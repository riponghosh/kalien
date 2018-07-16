<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ErrorLogRepository;
use App\User;
use App\SocialFbInfo;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;

class SocialAccountController extends Controller
{
    protected $err_log;
    protected $userService;
    const CLASS_NAME = 'SocialAccountController';
    const CALLBACK_URL = '/fbLoginFinish';
    protected $response;
    public function __construct(ErrorLogRepository $errorLogRepository, UserService $userService)
    {
        $this->err_log = $errorLogRepository;
        $this->userService = $userService;

        $this->response = [
            'success' => ['success' => true,'data' => ['social_login' => false]],
            'fail' => ['success' => false,'data' => ['social_login' => false, 'msg' => null]]
        ];
    }

    public function redirectToProvider($isDirectPage = false)
    {
        if($isDirectPage){
            Cookie::queue('isDirectPage', 'isDirectPage', 600000);
            Cookie::queue('AfterLoginRedirectURL', url()->previous(), 600000);
        }
        return Socialite::driver('facebook')->redirect();
    }

    public function handleCallback()
    {
        try {
            $social_fb = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            $this->err_log->err('get fb callback fail',self::CLASS_NAME, __FUNCTION__);
            $data = $this->response{'fail'};
            return view(self::CALLBACK_URL,compact('data'));
        }
        if (Auth::check()) {
            //return $this->handleProviderCallback_loged($social_fb);
            //暫時禁止已登入帳號使用社群登入
            $this->err_log->err('Already login',self::CLASS_NAME, __FUNCTION__);
            $data = $this->response{'success'};
            return view(self::CALLBACK_URL,compact('data'));
        }else {
            return $this->handleProviderCallback_unloged($social_fb);
        }
    }

    private function handleProviderCallback_loged($social_fb){
        $user = User::where('social_fb_id',$social_fb->getId())->first();
        /**
         * 如FB號已綁定其中一個pneko 號，返回
         */
        if($user){
            $data = $this->response{'fail'};
            return view(self::CALLBACK_URL,compact('data')); //它帳號綁定後處理
        }


        Auth::user()->update(['social_fb_id' =>$social_fb->getId() ]);
        $data = $this->response{'success'};
        $data{'data'}{'social_login'} = true;
        return view(self::CALLBACK_URL,compact('data')); //它帳號綁定後處理




    }

    private function handleProviderCallback_unloged($social_fb){
        $user = User::where('social_fb_id',$social_fb->getId())->first();
        //----------------------
        //嘗試登入
        //----------------------
        if($user){
            auth()->login($user);
            $data = $this->response{'success'};
            $data{'data'}{'social_login'} = true;
            return view(self::CALLBACK_URL,compact('data'));
        }
        //---------------------------------
        // 檢查是否有相同email
        //---------------------------------
        if(isset($social_fb['email']) && !empty($social_fb['email'])){
            $user = User::where('email',$social_fb->email)->first();
            if($user){
                $this->err_log->err('This email is pneko ac already',self::CLASS_NAME, __FUNCTION__);
                $this->response{'fail'}{'data'}{'msg'} = '此社群帳號的電郵地址已是Pneko用戶，請用電子郵箱方式登入。';
                $data = $this->response{'fail'};
                //---------
                //   之後增君綑綁帳號功能，所以要先login。目前處理方法是：
                //   此帳號已存在，請用pneko方式登入
                //-----------
                return view(self::CALLBACK_URL,compact('data'));//need Login
            }
        }
        //-------------------------------------------------
        // 嘗試註冊
        //-------------------------------------------------
        //性別
        $email = isset($social_fb['email']) ? $social_fb['email'] : null;
        /*
        switch($social_fb{'gender'}){
            case 'male':
                $gender = 'M';
                break;
            case 'female':
                $gender = 'F';
                break;
            default:
                $gender = null;
        }
        */
        //uni name create
        if(isset($social_fb['email']) && !empty($social_fb['email'])){
            $uni_name = explode('@',$social_fb->email)[0];
        }elseif(isset($social_fb['name']) && !empty($social_fb['name'])){
            $uni_name = str_replace(' ','_',$social_fb['name']);
        }else{
            $uni_name = $social_fb->getId();
        }
        if($uni_name != null){
            if(User::where('uni_name',$uni_name)->exists()){
                for($i = 1; $i<1000; $i++){
                    $tmp_uni_name = $uni_name.$i;
                    $check_uni_name_exist = User::where('uni_name',$tmp_uni_name)->exists();
                    if(!$check_uni_name_exist){
                        $uni_name = $tmp_uni_name;
                        break;
                    }
                    $i++;
                }
            }
        }
        //-------------------------------------
        //Create Account
        //-------------------------------------
        DB::beginTransaction();
        $create_user = User::create([
            'social_fb_id' => $social_fb->getId(),
            'email' => $email,
            'name' => $social_fb['name'],
            //'sex' => $gender,
            'uni_name' => $uni_name
        ]);
        if($create_user){
            User::where('social_fb_id',$social_fb->getId())->update(['is_activated' => 1]);
        }

        $user = User::where('social_fb_id',$social_fb->getId())->first();

        SocialFbInfo::create([
            'user_id' => $user->id,
            'fb_id' => $social_fb->getId(),
            'fb_email' => $email,
            'avatar_url' => $social_fb->avatar
        ]);
        //insert img
        if(!empty($social_fb->avatar)){
            $this->userService->upload_and_set_user_icon_by_url($social_fb->avatar,$social_fb->avatar ,$user->id);
        }
        DB::commit();
        //-------------------------------------
        // 再次登入
        //-------------------------------------

        auth()->login($user);
        $data = $this->response{'success'};
        $data{'data'}{'social_login'} = true;
        return view(self::CALLBACK_URL,compact('data'));

    }
}
?>

