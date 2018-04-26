<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\ActivationService;


class ActivateAccountController extends Controller
{
    public $activationService;

    public function __construct(ActivationService $activationService)
    {
        $this->activationService = $activationService;
    }
    public function email_activate(Request $request){
        if(!$this->activationService->email_activate($request->activate_code, $request->uni_name)) return abort('404');

        return redirect('/');
    }

    public function resend_activate_code(Request $request){
        if(Auth::user()->is_activated == 1){
            return ['success' => false, 'status' => 1];
        }

        $new_activate_code = $this->activationService->recreate_activate_code(Auth::user()->id);

        if(!$new_activate_code['success']){
            return ['success' => false, 'status' => 2];
        }

        $send_activate_email = $this->activationService->send_confirm_mail(Auth::user()->email, $new_activate_code['activate_code'], Auth::user()->uni_name);

        if(!$send_activate_email['success']){
            return ['success' => false, 'status' => 3];
        }

        return ['success' => true];

    }
}

?>

