<?php
namespace App\UserGroupActivity;

use Illuminate\Database\Eloquent\Model;

/**
 * 原因： 禁止有時間抵觸未成團的團進行操作
 */
class UserGpActivityBlockForConflict extends Model
{
    protected $table = 'gp_activities_blocked_for_conflict';
    protected $guarded = ['id'];
}
?>