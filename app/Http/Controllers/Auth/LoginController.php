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

        return response('', 204);
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
