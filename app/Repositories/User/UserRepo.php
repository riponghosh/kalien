<?php

namespace App\Repositories\User;

use App\User;

class UserRepo
{

    protected $model;

    function __construct(User $user)
    {
        $this->model = $user;
    }

    function get(){
        $user = $this->model->get();

        return $user;
    }

    function first(){
        $user = $this->model->first();

        return $user;
    }

}
?>

