<?php

namespace App\Http\Controllers\API\Web\Employee;

use App\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function authenticated(Request $request){
        $credentials = $request->only('email', 'password');

        try{
            Config::set('auth.providers.users.model', Employee::class);
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json(['success' => 'false', 'msg' => 'failed', 'code' => '1'], 401);
            }
        } catch (JWTException $e){
            return response()->json(['success' => 'false', 'msg' => 'Something wrong', 'code' => '2'], 500);
        }
        $this->apiModel->setData(compact('token'));
        return $this->apiFormatter->success($this->apiModel);
    }
}
