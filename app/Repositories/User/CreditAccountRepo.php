<?php

namespace App\Repositories\User;

use App\UserCreditAccount;

class CreditAccountRepo
{

    protected $model;

    function __construct(UserCreditAccount $userCreditAccount)
    {
        $this->model = $userCreditAccount;
    }

    function first_by_user($user_id){
        $user = $this->model->where('user_id', $user_id)->first();

        return $user;
    }

    function update($user_id, $data){
        return $this->model->where('user_id', $user_id)->update($data);
    }

}
?>

