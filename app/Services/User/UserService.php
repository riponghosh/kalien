<?php

namespace App\Services\User;
use App\Repositories\User\UserRepo;


class UserService
{
    protected $repo;

    function __construct(UserRepo $userRepo)
    {
        $this->repo = $userRepo;
    }

    function first(){
        $user = $this->repo->first();

        return $user;
    }

    function get(){
        $users = $this->repo->get();

        return $users;
    }
}
