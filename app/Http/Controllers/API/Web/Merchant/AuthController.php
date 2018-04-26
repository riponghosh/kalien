<?php

namespace App\Http\Controllers\API\Web\Merchant;

use App\Http\Controllers\Controller;
use League\Flysystem\Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Merchant\Merchant;

class AuthController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function authenticated(Request $request){
        $credentials = $request->only('email', 'password');

        try{
            Config::set('auth.providers.users.model', Merchant::class);
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json(['success' => 'false', 'msg' => 'failed', 'code' => '1'], 401);
            }
        } catch (JWTException $e){
            return response()->json(['success' => 'false', 'msg' => 'Something wrong', 'code' => '2'], 500);
        }
        $this->apiModel->setData(compact('token'));
        return $this->apiFormatter->success($this->apiModel);
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email|max:255|unique:merchants',
            'password' => 'required|min:6|confirmed',
        ]);
        if($validator->fails()){
            return response()->json(['success' => 'false', 'msg' => $validator->errors()->all(), 'code' => '2'], 500);

        }
        $create = Merchant::create([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        if(!$create){
          throw new Exception('註冊失敗。');
        }
        //登入

        try{
            Config::set('auth.providers.users.model', Merchant::class);
            if(! $token = JWTAuth::attempt($request->only('email', 'password'))){
                return response()->json(['success' => 'false', 'msg' => 'failed', 'code' => '1'], 401);
            }
        } catch (JWTException $e){
            return response()->json(['success' => 'false', 'msg' => 'Something wrong', 'code' => '2'], 500);
        }
        $this->apiModel->setData(compact('token'));
        return $this->apiFormatter->success($this->apiModel);
    }

    public function logout(Request $request){
        JWTAuth::invalidate(explode(' ', $request->header('Authorization'))[1]);
        return $this->apiFormatter->success($this->apiModel);
    }
}