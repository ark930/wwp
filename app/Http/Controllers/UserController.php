<?php

namespace App\Http\Controllers;

use App\Contracts\SMSServiceContract;
use App\Exceptions\BadRequestException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|regex:/^1\d{10}$/',
            'password' => 'required|regex:/^\d{6}$/',
        ], [
            'username.required' => '请填写手机号',
            'username.regex' => '请填写正确的手机号',
            'password.required' => '请填写验证码',
            'password.regex' => '验证码格式不正确',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('tel', $username)->first();

        if(empty($user)) {
            return redirect()->back()->withErrors('登录失败')->withInput();
        }

        if($user->ifVerifyCodeExpired()) {
            return redirect()->back()->withErrors('验证码过期, 请重新获取')->withInput();
        }

        if($user->ifVerifyCodeRetryTimesExceed()) {
            return redirect()->back()->withErrors('验证码输入错误次数过多, 已失效, 请重新获取')->withInput();
        }

        if($user->ifVerifyCodeWrong($password)) {
            return redirect()->back()->withErrors('验证码错误')->withInput();
        }

        // 保存邀请人信息
        if(Session::has('inviter_id')) {
            $inviter_id = Session::pull('inviter_id');
            $inviter = User::find($inviter_id);

            if(!empty($inviter)) {
                $user->inviter()->associate($inviter);
                $user->save();
                $inviter['invite_count'] += 1;
                $inviter->save();
            }
        }

        $user->disableVerifyCode();
        $user->ifFirstLogin();
        $user->updateLastLogin();

        Session::put('user', $user);

        if($user['apply_status'] == 'approve') {
            return redirect()->route('people');
        } else {
            return redirect('apply');
        }
    }

    public function logout() {
        Session::flush();
        Session::regenerate();
        return redirect('/login');
    }

    /**
     * 获取登录验证码
     *
     * @param Request $request
     * @param SMSServiceContract $SMS
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     */
    public function verifyCode(Request $request, SMSServiceContract $SMS)
    {

        $this->validate($request, [
            'tel' => 'required|regex:/^1\d{10}$/',
        ], [
            'tel.required' => '请填写手机号',
            'tel.regex' => '请填写正确的手机号',
        ]);

        $tel = $request->input('tel');
        $user = User::where('tel', $tel)->first();
        if(empty($user)) {
            $user = User::create(['tel' => $tel]);
        }

        if($user->ifGetVerifyCodeTooFrequently()) {
            $seconds = $user->verifyCodeRetryAfterSeconds();
            throw new BadRequestException("请求过于频繁, 请在 $seconds 秒后重新请求");
        }

        $verify_code = $user->setVerifyCode();

        // 向手机发送验证码短信
//        $message = "【TGIF 验证】您的验证码是$verify_code";
//        $SMS->SendSMS($tel, $message);
        $temp_id = 1579488;
        $SMS->SendSMSByTemplate($tel, $temp_id, $verify_code);

        return response()->json(['msg' => '验证码已发送至客户端, 请注意查收']);
    }
}
