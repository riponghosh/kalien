<?php
namespace App\Services;

use App\Repositories\UserCreditAccountRepository;

class UserCreditAccountService
{
    protected $userCreditAccountRepository;

    function __construct(UserCreditAccountRepository $userCreditAccountRepository)
    {
        $this->userCreditAccountRepository;
    }

}
?>