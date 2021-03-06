<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Status;
//Authenticatable是授权功能的引用
class User extends Authenticatable
{
    //消息通知的引用
    use Notifiable;

    //关联好users表
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     public function feed()
     {
         $user_ids = $this->followings->pluck('id')->toArray();
         array_push($user_ids, $this->id);
         return Status::whereIn('user_id', $user_ids)
                               ->with('user')
                               ->orderBy('created_at', 'desc');
     }
    //防止批量赋值安全漏洞的字段白名单
    protected $fillable = [
        'name', 'email', 'password',
    ];

    // public function feed()
    // {
    //     return $this->statuses()
    //         ->orderBy('created_at','desc');
    // }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    //当使用$user-toArray()或 $user-toJson()时隐藏这些字段
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *  指定模型数据类型
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //生成用户头像
    public function gravatar($size='100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    public function follow($user_ids)
    {
        if(! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }

    public function unfollow($user_ids)
    {
        if(! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
