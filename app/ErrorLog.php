<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ERRORLOG extends Model
{
	protected $connection = 'log';
    protected $table = 'error_log';
    protected $fillable = ['id', 'user_id', 'msg', 'position','func_name', 'class_name','route'];

}
?>

