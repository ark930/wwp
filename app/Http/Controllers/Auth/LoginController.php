<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\BadRequestException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * @api {post} /login 用户登录
     * @apiGroup Users
     *
     * @apiParam {String} tel 用户手机号
     * @apiParam {String} verify_code 验证码
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "tel": "1801234567",
     *      "username": "ark",
     *      "nickname": "Edwin",
     *      "avatar_url": "http://whitewrite.press/img/avatar/105ac9f2700b67b28bc1febd7e83ea55.png",
     *      "first_login_at": "2016-12-06 03:48:18",
     *      "last_login_at": "2016-12-06 07:22:31"
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 400 Bad Request
     *  {
     *      "msg": "验证码过期, 请重新获取验证码"
     *  }
     */

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws BadRequestException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $ret = $this->attemptLogin($request);
        if ($ret === true) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        throw new BadRequestException($ret);
    }

    /**
     * @api {post} /logout 用户登出
     * @apiGroup Users
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     */

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return response('', 200);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'tel' => 'required|regex:/^1\d{10}$/',
            'verify_code' => 'required|regex:/^\d{4}$/',
        ], [
            'tel.required' => '请填写手机号',
            'tel.regex' => '请填写正确的手机号',
            'verify_code.required' => '请填写验证码',
            'verify_code.regex' => '验证码格式不正确',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);

        $user = User::where('tel', $credentials['tel'])->first();

        $ret = true;
        if(empty($user)) {
            $ret = '登录失败';
        } else if($user->ifVerifyCodeExpired()) {
            $ret = '验证码过期, 请重新获取验证码';
        } else if($user->ifVerifyCodeRetryTimesExceed()) {
            $ret = '验证码输入错误次数过多, 已失效, 请重新获取验证码';
        } else if($user->ifVerifyCodeWrong($credentials['verify_code'])) {
            $ret = '验证码错误';
        } else {
            $this->guard()->login($user);
            $user->disableVerifyCode();
            $user->ifFirstLogin();
            $user->updateLastLogin();
        }

        return $ret;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('tel', 'verify_code');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'tel';
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return response($this->guard()->user());
    }
}
