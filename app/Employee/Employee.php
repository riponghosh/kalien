<?php

namespace App\Employee;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $table = 'employees';
    protected $guard = 'employees';

    protected $hidden = ['password'];
}
