<template>
    <div>
        <div id="article" :mode="myMode">
            <div class='container'>
                <address class="info fontLevel-s1">
                    <time class="publishDate">{{ publish_date }}</time><span class="readTime">阅读 {{ read_min }} 分钟</span>
                </address>
                <h1 id="title" class="title fontLevel-h1" :contenteditable="editable" placeholder="标题" required="true"
                    @keydown="changeTitle" v-empty-error="titleEmptyError" v-temp-save v-paste>
                    {{ title }}
                </h1>
                <article id="content" class="body fontLevel-p" :contenteditable="editable" placeholder="正文" required='true'
                         @keydown="keydownContent" v-empty-error="contentEmptyError" v-temp-save v-paste>
                    {{ html_content }}
                </article>
                <address class="info fontLevel-s1">
                    <div id="author" :contenteditable="editable" placeholder="作者（选填）" data_anonymous="匿名用户" class="author"
                         @keydown="changeAuthor" v-temp-save v-paste>
                        {{ author }}
                    </div><span class="channel">发布于&nbsp;<a href="https://a-z.press" target="blank">A-Z.press</a></span>
                </address>
                <div class="actions">
                    <button class="btn-publish btn-rounded-primary" @click="toPublish">{{ publishButtonText }}</button>
                    <button class="btn-edit btn-rounded-ghost" @click="toEdit">编辑</button>
                </div>
            </div>
        </div>

        <!--<div class="comments">-->
            <!--<form class="addComment">-->
                <!--<input placeholder="添加评论" class="content">-->
            <!--</form>-->
            <!--<ul class="commentlist">-->
                <!--<li>-->
                    <!--<div class="info fontLevel-s1"><span class="name">{{ article.comments[0].name }}</span>-->
                        <!--<time class="date">{{ article.comments[0].date }}</time>-->
                    <!--</div>-->
                    <!--<div class="content">{{ article.comments[0].content }}</div>-->
                <!--</li>-->
                <!--<li>-->
                    <!--<div class="info fontLevel-s1"><span class="name">{{ article.comments[1].name }}</span>-->
                        <!--<time class="date">{{ article.comments[1].date }}</time>-->
                    <!--</div>-->
                    <!--<div class="content">{{ article.comments[1].content }}</div>-->
                <!--</li>-->
            <!--</ul>-->
        <!--</div>-->
        <!--<div id="mypannel" v-bind:class="shown">-->
            <!--<button @click="toggleMyPannel" class="btn-mypannel"></button>-->
            <!--<div class="pannel">-->
                <!--<ul>-->
                    <!--<li class="draft">-->
                        <!--<div class="title">{{ article_list[0].title }}</div>-->
                        <!--<div class="description">{{ article_list[0].description }}</div>-->
                    <!--</li>-->
                    <!--<li class="current">-->
                        <!--<div class="title">{{ article_list[1].title }}</div>-->
                        <!--<div class="description">{{ article_list[1].description }}</div>-->
                    <!--</li>-->
                    <!--<li>-->
                        <!--<div class="title">{{ article_list[2].title }}</div>-->
                        <!--<div class="description">{{ article_list[2].description }}</div>-->
                    <!--</li>-->
                <!--</ul>-->
            <!--</div>-->
        <!--</div>-->

    </div>
</template>

<script>
    export default {
        props: [
            'title',
            'author',
            'html_content',
            'text_content',
            'read_min',
            'publish_date',
            'mode'],
        data: function() {
            return {
                publishButtonText: '发布',
                editable: this.mode === 'author-edit',
                titleEmptyError: false,
                contentEmptyError: false,
                myMode: this.mode,
                isUpdate: false,
                article: {
                    publish_date: "2017-01-07",
                    read_min: "0",
                    title: "a-z.press",
                    content: "Loading",
                    author: "Vinci",
                    channelURL: "azpress.pro",
                    channelName: "azpress.pro",
                    comments: [{name: "小瓜瓜", content: "哇塞好棒！", date: "2017-01-07 12:21"}, {
                        name: "大瓜瓜",
                        content: "哇塞好傻！！！！！",
                        date: "2017-01-07 12:21"
                    }]
                },
                article_list: [{title: "马阿姨怎么会有这种奇怪的东西", description: "草稿"}, {
                    title: "所以说都不用注册是吗",
                    description: "所以说都不用注册是吗，怎么找之前发布过的内容呢"
                }, {title: "这写的什么啊", description: "马阿姨需要一个设计师"}]
            }
        },
        mounted() {
            if(this.myMode === 'author-edit') {
                this.$el.querySelector('#title').innerHTML = localStorage.getItem('title') || '';
                this.$el.querySelector('#author').innerHTML = localStorage.getItem('author') || '';
                this.$el.querySelector('#content').innerHTML = localStorage.getItem('content') || '';
//                let br = document.createElement('br');
//                this.$el.querySelector('#content').appendChild(br);
            } else {
                this.$el.querySelector('#title').textContent = decodeURI(this.title);
                this.$el.querySelector('#author').textContent = decodeURI(this.author);
                this.$el.querySelector('#content').innerHTML = this.html_content;
            }
        },
        methods: {
            putCursorAtEnd: function(el) {
                el.focus();
                if (typeof window.getSelection != "undefined"
                    && typeof document.createRange != "undefined") {
                    let range = document.createRange();
                    range.selectNodeContents(el);
                    range.collapse(false);
                    let sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                } else if (typeof document.body.createTextRange != "undefined") {
                    let textRange = document.body.createTextRange();
                    textRange.moveToElementText(el);
                    textRange.collapse(false);
                    textRange.select();
                }
            },
            toPublish: function() {
                this.titleEmptyError = false;
                this.contentEmptyError = false;
                if(this.myMode === 'author-edit') {
                    if(_.isEmpty(this.$el.querySelector('#title').innerHTML)) {
                        this.titleEmptyError = true;
                    } else if(_.isEmpty(this.$el.querySelector('#content').innerHTML)) {
                        this.contentEmptyError = true;
                    } else {
                        let url = '';
                        if(this.isUpdate) {
                            let fullUrl = window.location.href;
                            let tags = fullUrl.split('/');
                            let articleTag = tags[tags.length - 1];
                            url = '/a/' + articleTag;
                        } else {
                            url = '/articles';
                        }

                        let content = this.$el.querySelector('#content');
                        let description = '';
                        if(content.firstChild) {
                            description = encodeURI(content.firstChild.textContent);
                        }
                        Vue.http.post(url, {
                            title: encodeURI(this.$el.querySelector('#title').textContent),
                            author: encodeURI(this.$el.querySelector('#author').textContent),
                            html_content: content.innerHTML,
                            text_content: encodeURI(content.textContent),
                            description: description,
                        })
                        .then((response) => {
//                            console.log('success', response);
                            const body = response.body;
                            localStorage.removeItem('title');
                            localStorage.removeItem('content');
                            localStorage.removeItem('author');
                            location.replace(body.show_url);
                        }, (response) => {
//                            console.log('error', response);
                            const body = response.body;
//                            alert(_.values(body)[0]);
                        });
                    }
                }
            },
            toEdit: function() {
                if(this.myMode === 'author-read') {
                    this.myMode = 'author-edit';
                    this.editable = true;
                    this.isUpdate = true;
                    this.publishButtonText = '更新';
                }
            },
            changeTitle: function(e) {
//                console.log(e);
                let code = e.keyCode || e.which;
                if(code == 13 || code == 40) {
                    // make enter, arrow down to move caret to next text input
                    e.preventDefault();
                    e.target.nextElementSibling.focus();
                }
            },
            changeAuthor: function(e) {
                let code = e.keyCode || e.which;
                if(code == 13) {
                    e.preventDefault();
                } else if(code == 38) {
                    // make arrow up to move caret to previous text input
                    e.preventDefault();
                    e.target.parentElement.previousElementSibling.focus();
                }
            },
            keydownContent: function(e) {
//                console.log('key down');
//                let code = e.keyCode || e.which;
//                // Backspace event
//                if(code == 8) {
//                    let content = e.target;
//                    if(content.textContent.length == 1) {
//                        e.preventDefault();
//                        content.textContent = '';
//                        let br = document.createElement('br');
//                        content.appendChild(br);
//                    }
//                }
//                else if(_.isEmpty(content.textContent) && e.key.length == 1) {
//                    e.preventDefault();
//                    let p = document.createElement('p');
//                    p.innerText = e.key;
//                    content.innerText = '';
//                    content.appendChild(p);
//                    this.putCursorAtEnd(content);
//                }
            }
        }
    }
</script>
