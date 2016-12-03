<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = [
        'tel',
    ];

    protected $hidden = [
        'verify_code',
        'verify_code_expire_at',
        'verify_code_refresh_at',
        'verify_code_retry_times',
        'deleted_at',
    ];

    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    /**
     * 设置验证码
     */
    public function setVerifyCode()
    {
        $verify_code = mt_rand(1000, 9999);
        $verify_code_refresh_at = date('Y-m-d H:i:s', strtotime("+2 minute"));
        $verify_code_expire_at = date('Y-m-d H:i:s', strtotime("+2 minute"));
        $verify_code_retry_times = 4;

        $this['verify_code'] = $verify_code;
        $this['verify_code_refresh_at'] = $verify_code_refresh_at;
        $this['verify_code_expire_at'] = $verify_code_expire_at;
        $this['verify_code_retry_times'] = $verify_code_retry_times;
        $this->save();

        return $verify_code;
    }

    /**
     * 检查是否获取验证码过于频繁
     *
     * @return bool
     */
    public function ifGetVerifyCodeTooFrequently()
    {
        if(!empty($this['verify_code_refresh_at']) && strtotime($this['verify_code_refresh_at']) > time()) {
            return true;
        }

        return false;
    }

    /**
     * 需要多少秒才能重新获取验证码
     *
     * @return false|int
     */
    public function verifyCodeRetryAfterSeconds()
    {
        $seconds = strtotime($this['verify_code_refresh_at']) - time();

        return $seconds;
    }

    /**
     * 判断用户输入的验证码是否正确
     *
     * @param $userInputVerifyCode string 用户输入的验证码
     * @return bool
     */
    public function ifVerifyCodeWrong($userInputVerifyCode)
    {
        if($this['verify_code'] != $userInputVerifyCode) {
            // 验证码错误, 重试次数减一
            $this['verify_code_retry_times'] -= 1;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * 判断验证码是否失效
     *
     * @return bool
     */
    public function ifVerifyCodeExpired()
    {
        if(strtotime($this['verify_code_expire_at']) <= time()) {
            return true;
        }

        return false;
    }

    /**
     * 判断验证码手否重试了太多次
     *
     * @return bool
     */
    public function ifVerifyCodeRetryTimesExceed()
    {
        if($this['verify_code_retry_times'] <= 0) {
            return true;
        }

        return false;
    }

    /**
     * 登录成功后, 验证码立即失效
     */
    public function disableVerifyCode()
    {
        $this['verify_code_expire_at'] = null;
        $this['verify_code_refresh_at'] = null;
        $this['verify_code_retry_times'] = 0;
        $this->save();
    }

    /**
     * 判断用户是否是第一次登录, 如果是第一次登录, 则设置第一次登录时间
     */
    public function ifFirstLogin()
    {
        if(empty($this['first_login_at'])) {
            $now = date('Y-m-d H:i:s', time());
            $this['first_login_at'] = $now;
            $this->save();
        }
    }

    /**
     * 更新最后一次登录的时间
     */
    public function updateLastLogin()
    {
        $now = date('Y-m-d H:i:s', time());
        $this['last_login_at'] = $now;
        $this->save();
    }
}
