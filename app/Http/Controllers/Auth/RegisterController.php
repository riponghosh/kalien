<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Laravel\Socialite\Facades\Socialite;
use Validator;
use App\Services\ActivationService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $activation_service;
    public function __construct(ActivationService $activationService)
    {
        $this->middleware('guest');
        $this->activation_service = $activationService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'sex'      => 'required|in:M,F',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        //activate code create
        $activate_code = sha1('addSalt'.time().'forConfirm'.$data['email']);
        //uni name create
        $uni_name = explode('@',$data['email'])[0];
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

        $query = User::create([
            'name' => $data['first_name'].' '.$data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'sex'   => $data['sex'],
            'activate_code' => $activate_code,
            'uni_name' => $uni_name
        ]);
        if($query){
            $this->activation_service->send_confirm_mail($data['email'], $activate_code, $uni_name);
        }
        return $query;
    }


}
