<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use App\ErrorLog;
use Route;

class ErrorLogRepository{

    protected $errorLog;

    public function __construct(ErrorLog $errorLog)
    {
        $this->errorLog = $errorLog;
    }

    public function err($msg, $class, $function){
        $user_id = isset(Auth::user()->id) ? Auth::user()->id : null;
        $query = $this->errorLog->create([
            'user_id' => $user_id,
            'msg' => $msg,
            'route' => Route::current()->uri,
            'class_name' => $class,
            'func_name' => $function
        ]);

        return ;
    }

}
?>

