<?php
/**
 * @apiDefine ParamArticleId
 * @apiParam {Number} id Articles unique ID.
 */
/**
 * @apiDefine ParamTitle
 * @apiParam {String{最大255}} title 文章标题
 */

/**
 * @apiDefine ParamContent
 * @apiParam {String} content 文章正文
 */

/**
 * @apiDefine ArticleObject
 * @apiSuccessExample Success-Response:
 *  HTTP/1.1 200 OK
 *  {
 *      "id": 1,
 *      "cover_url": "http://whitewrite.press/img/avatar/105ac9f2700b67b28bc1febd7e83ea55.png",
 *      "title": "标题",
 *      "content": "内容",
 *      "read_time": 2,
 *      "status": "draft",
 *      "created_at": "2016-12-06 07:49:16",
 *      "updated_at": "2016-12-06 07:49:16"
 *  }
 */

/**
 * @apiDefine NotFound
 * @apiErrorExample {json} NotFound-Response:
 *  HTTP/1.1 404 Not Found
 *  {
 *      "msg": "资源不存在"
 *  }
 */

/**
 * @apiDefine Unauthorized
 * @apiErrorExample {json} Unauthorized-Response:
 *  HTTP/1.1 401 Unauthorized
 *  {
 *      "msg": "未授权"
 *  }
 *
 *  HTTP/1.1 401 Unauthorized
 *  {
 *      "msg": "会话已过期, 请重新登录"
 *  }
 */

/**
 * @apiDefine UnprocessableEntity
 * @apiErrorExample {json} Unprocessable-Entiry-Response
 * HTTP/1.1 422 Unprocessable Entity
 *  {
 *      "msg": "参数验证失败",
 *      "params":{
 *          "tel":[
 *              "The xxx field is required."
 *          ]
 *      }
 *  }
 */

/**
 * @api {get} /articles 文章列表
 * @apiGroup Articles
 * @apiSuccessExample Success-Response:
 *  HTTP/1.1 200 OK
 *  [
 *      {
 *          "id": 1,
 *          "cover_url": "http://whitewrite.press/img/avatar/105ac9f2700b67b28bc1febd7e83ea55.png",
 *          "title": "标题一",
 *          "content": "内容",
 *          "read_time": 5,
 *          "status": "draft",
 *          "created_at": "2016-12-06 07:49:16",
 *          "updated_at": "2016-12-06 07:49:16"
 *      },
 *      {
 *          "id": 2,
 *          "cover_url": "http://whitewrite.press/img/avatar/105ac9f2700b67b28bc1febd7e83ea55.png",
 *          "title": "标题二",
 *          "content": "内容",
 *          "read_time": 10,
 *          "status": "published",
 *          "created_at": "2016-12-06 07:49:16",
 *          "updated_at": "2016-12-06 07:49:16"
 *      }
 *  ]
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {post} /articles 新建文章
 * @apiGroup Articles
 * @apiUse ParamTitle
 * @apiUse ParamContent
 * @apiParamExample {json} Request-Example:
 * {
 *      "title": "标题",
 *      "content": "内容"
 * }
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 * @apiUse UnprocessableEntity
 */

/**
 * @api {get} /articles/:id 获取指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {put} /articles/:id 更新指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiUse ParamTitle
 * @apiUse ParamContent
 * @apiParamExample {json} Request-Example:
 * {
 *      "title": "又一个标题标题",
 *      "content": "新的内容"
 * }
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 * @apiUse UnprocessableEntity
 */

/**
 * @api {delete} /articles/:id 删除指定文章
 * @apiDescription 彻底删除对象
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiSuccessExample Success-Response:
 *  HTTP/1.1 200 OK
 *
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {post} /articles/:id/publish 发布指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {post} /articles/:id/unpublish 移除发布指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {post} /articles/:id/trash 将指定文章放入回收站
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {post} /articles/:id/untrash 从收站取回指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {post} /articles/:id/cover 上传文章封面图片
 * @apiGroup Articles
 * @apiUse ParamArticleId
 * @apiParam {File{最大5M}} cover 封面图片
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 * @apiUse UnprocessableEntity
 */