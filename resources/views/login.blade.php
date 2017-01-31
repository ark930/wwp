@extends('layout')

{{--@section('left_top_button')--}}
    {{--<a href="{{ url('/') }}" class="right btn_login btn_cta">返回首页</a>--}}
{{--@endsection--}}

@section('content')
    <div class="form_login">
        <form id="loginForm" style="padding: 0.5rem" class="phone" method="post" action="/login">
            {{ csrf_field() }}
            <input id="telInput" type="text" placeholder="输入你的手机" name="tel"
                   value="{{ old('username') ? old('username') : Session::get('username') }}">
            <button id="requireVerifyCode" class="submit btn_cta">发送验证码</button>
            <input id="verifyCodeInput" type="text" placeholder="输入短信验证码" name="verify_code" value="{{ old('password') }}">
            <button id="submitButton" class="submitbtn_success btn_success">下一步</button>
        </form>
    </div>
@endsection

@section('custom_script')
    <script>
        window.onload = function() {
            var submitButton = document.getElementById('submitButton');
            submitButton.onclick = function (e) {
                e.preventDefault();
                var tel = document.getElementById('telInput').value;
                var verifyCode = document.getElementById('verifyCodeInput').value;
                if (tel == '') {
                    alert('请输入手机号');
                } else if (verifyCode == '') {
                    alert('请输入验证码');
                } else {
                    document.getElementById('loginForm').submit();
                }
            };

            document.getElementById('requireVerifyCode').onclick = function (e) {
                e.preventDefault();
                var tel = document.getElementById('telInput').value;
                if (tel == '') {
                    alert('请输入手机号');
                } else {
                    getVerifyCode();
                }
            };

            function getVerifyCode() {
                var tel = document.getElementById('telInput').value;

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/verifycode', true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send('tel=' + tel);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        countdown(120, document.getElementById('requireVerifyCode'));
                    } else {
                        errorHandler(xhr.responseText);
                    }
                };
            }

            function errorHandler(data) {
                var res = JSON.parse(data.responseText);
                console.log(res);
                if (res.error) {
                    alert(res.error);
                }
            }

            function countdown(time, button) {
                console.log(time);
                if (time == 0) {
                    button.attr("disabled", false);
                    button.removeClass('btn_disable');
                    button.addClass('btn_cta');
                    button.text("获取");
                } else {
                    button.attr("disabled", true);
                    button.removeClass('btn_cta');
                    button.addClass('btn_disable');
                    button.text("重新发送(" + time + ")");
                    time--;
                    setTimeout(function () {
                        countdown(time, button)
                    }, 1000);
                }
            }
        }
    </script>
@endsection