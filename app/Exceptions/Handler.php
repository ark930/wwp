<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof BadRequestException) {
            return response()->json(['msg' => $exception->getMessage()], $exception->getCode());
        } else if ($exception instanceof ModelNotFoundException) {
            return response()->json(['msg' => '资源不存在'], 404);
        } else if ($exception instanceof TokenMismatchException){
            return response()->json(['msg' => '会话已过期, 请重新登录'], 401);
        } else if ($exception instanceof AuthenticationException) {
            return response()->json(['msg' => '未授权'], 401);
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json(['msg' => '无效的访问方式'], 404);
        } else if($exception instanceof ValidationException) {
            $errors = [
                'params' => $exception->getResponse()->getData(),
            ];
            return response()->json(array_merge(['msg' => '参数验证失败'], (array)$errors), 422);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
//        if ($request->expectsJson()) {
//            return response()->json(['msg' => '未授权'], 401);
//        }
//
//        return redirect()->guest('login');
        return response()->json(['msg' => '未授权'], 401);
    }
}
