<?php

/**
 * @api {post} /verifycode 获取验证码
 * @apiGroup Users
 *
 * @apiParam {String{11}} tel 用户手机号
 * @apiParamExample {json} Request-Example:
 * {
 *      "tel": "18012345678",
 * }
 *
 * @apiSuccessExample Success-Response:
 *  HTTP/1.1 200 OK
 *
 * @apiErrorExample Error-Response:
 *  HTTP/1.1 400 Bad Request
 *  {
 *      "msg": "请求过于频繁, 请在 120 秒后重新请求"
 *  }
 * @apiUse UnprocessableEntity
 */

/**
 * @api {post} /login 用户登录
 * @apiGroup Users
 *
 * @apiParam {String{11}} tel 用户手机号
 * @apiParam {String{4}} verify_code 验证码
 * @apiParamExample {json} Request-Example:
 * {
 *      "tel": "18012345678",
 *      "verify_code": "1234"
 * }
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
 * @apiUse UnprocessableEntity
 */

/**
 * @api {post} /logout 用户登出
 * @apiGroup Users
 *
 * @apiSuccessExample Success-Response:
 *  HTTP/1.1 200 OK
 */