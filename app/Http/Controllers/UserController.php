<?php

namespace App\Http\Controllers;

use App\Contracts\SMSServiceContract;
use App\Exceptions\BadRequestException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
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
//            'tel.required' => '请填写手机号',
//            'tel.regex' => '请填写正确的手机号',
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

        if(app()->environment() == 'local') {
            return response([
                'verify_code' => $verify_code
            ], 200);
        }

        // 向手机发送验证码短信
        $temp_id = 1645222;
        $SMS->SendSMSByTemplate($tel, $temp_id, $verify_code);

        return response('', 204);
    }

    /**
     * 上传用户头像
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|max:5000',
        ]);

        $avatar = $request->file('avatar');
        $filePath = $avatar->store('img/avatar');

        $user = Auth::user();
        $user['avatar_url'] = $request->getSchemeAndHttpHost() . '/' . $filePath;
        $user->save();

        return response()->json($user);
    }

    /**
     * 保存用户昵称
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveNickname(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'required',
        ]);

        $nickname = $request->input('nickname');
        $user = Auth::user();
        $user['nickname'] = $nickname;
        $user->save();

        return response()->json($user);
    }

    public function uc(Request $request)
    {
        return $request->header('user-agent');
    }
}
