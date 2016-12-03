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
            'tel' => 'required|regex:/^1\d{10}$/',
            'verify_code' => 'required|regex:/^\d{4}$/',
        ], [
            'tel.required' => '请填写手机号',
            'tel.regex' => '请填写正确的手机号',
            'verify_code.required' => '请填写验证码',
            'verify_code.regex' => '验证码格式不正确',
        ]);

        $tel = $request->input('tel');
        $verifyCode = $request->input('verify_code');

        $user = User::where('tel', $tel)->first();

        if(empty($user)) {
            throw new BadRequestException('登录失败');
        }

        if($user->ifVerifyCodeExpired()) {
            throw new BadRequestException('验证码过期, 请重新获取验证码');
        }

        if($user->ifVerifyCodeRetryTimesExceed()) {
            throw new BadRequestException('验证码输入错误次数过多, 已失效, 请重新获取验证码');
        }

        if($user->ifVerifyCodeWrong($verifyCode)) {
            throw new BadRequestException('验证码错误');
        }

        $user->disableVerifyCode();
        $user->ifFirstLogin();
        $user->updateLastLogin();

        return response('', 204);
    }

    public function logout() {
        Session::flush();
        Session::regenerate();

        return response('', 204);
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
