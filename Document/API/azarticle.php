<?php
/**
 * @apiDefine ParamArticleSlug
 * @apiParam {Number} slug 文章 slug.
 */
/**
 * @apiDefine ParamTitle
 * @apiParam {String{最大255}} title 标题
 */

/**
 * @apiDefine ParamAuthor
 * @apiParam {String{最大255}} author 作者
 */

/**
 * @apiDefine ParamHtmlContent
 * @apiParam {String} html_content HTML格式文章正文
 */

/**
 * @apiDefine ParamTextContent
 * @apiParam {String} text_content 纯文本格式文章正文
 */

/**
 * @apiDefine ParamDescription
 * @apiParam {String} description 文章描述，取文章正文第一行
 */

/**
 * @apiDefine ArticleObject
 * @apiSuccessExample Success-Response:
 *  HTTP/1.1 200 OK
 *  {
 *      "slug": "f1g0nsqz",
 *      "author": "作者",
 *      "title": "标题",
 *      "html_content": "&lt;p&gt;内容&lt;/p&gt;",
 *      "text_content": "内容",
 *      "description": "文章第一行",
 *      "read_time": 1,
 *      "created_at": "2017-01-21 16:15:17"
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
 * @api {post} /articles 新建文章
 * @apiGroup Articles
 * @apiUse ParamTitle
 * @apiUse ParamAuthor
 * @apiUse ParamHtmlContent
 * @apiUse ParamTextContent
 * @apiUse ParamDescription
 *
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse UnprocessableEntity
 */

/**
 * @api {get} /articles/slug 获取指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleSlug
 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 */

/**
 * @api {put} /articles/:slug 更新指定文章
 * @apiGroup Articles
 * @apiUse ParamArticleSlug
 * @apiUse ParamTitle
 * @apiUse ParamAuthor
 * @apiUse ParamHtmlContent
 * @apiUse ParamTextContent
 * @apiUse ParamDescription

 * @apiUse ArticleObject
 * @apiUse Unauthorized
 * @apiUse NotFound
 * @apiUse UnprocessableEntity
 */
