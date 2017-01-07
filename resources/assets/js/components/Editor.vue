<template>
    <div v-bind:mode="mode" class="articleview">
        <div class='articleComponent'>
            <address class='info'>
                <time class="publishDate">{{ publish_date }}</time>
                <span class="readTime">阅读 {{ read_min }} 分钟</span>
            </address>
            <h1 id="title" class="title" v-bind:class="titleError" v-bind:contenteditable="editable" placeholder="标题" required="true" @focus="titleFocus()">
                {{ title }}
            </h1>
            <article id="article" class="article" v-bind:class="contentError" v-bind:contenteditable="editable" placeholder="你的故事" required='true' @focus="articleFocus()" @keyup="changeContent($event)">
                {{ content }}
            </article>
            <address class="info">
                <div id="author" v-bind:contenteditable="editable" placeholder="作者（选填）" class="authorName">
                    {{ author }}
                </div>
                <span class="publishChannel">发布于 <a href="http://www.a-z.press" target="blank">A-Z.press</a></span>
            </address>
            <div class="actions">
                <button class="btn-publish" @click="toPublish">发布</button>
                <button class="btn-edit" @click="toEdit">编辑</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title', 'author', 'content', 'read_min', 'publish_date', 'mode'],
        data: function() {
            return {
                editable: this.mode === 'author-edit',
                titleEmptyError: false,
                contentEmptyError: false,
            }
        },
        computed: {
            titleError: function () {
                return {
                    'form-error': this.titleEmptyError
                }
            },
            contentError: function () {
                return {
                    'form-error': this.contentEmptyError
                }
            }
        },
        mounted() {
            if(this.mode === 'author-edit') {
                this.$el.querySelector('#title').innerHTML = '';
                this.$el.querySelector('#author').innerHTML = '';
                this.$el.querySelector('#article').innerHTML = '';
//                let p = document.createElement('p');
//                let br = document.createElement('br');
//                p.appendChild(br);
//                this.$el.querySelector('#article').appendChild(p);
            } else {
                this.$el.querySelector('#title').innerHTML = this.title;
                this.$el.querySelector('#author').innerHTML = this.author;
                this.$el.querySelector('#article').innerHTML = this.content;
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
                if(this.mode === 'author-edit') {
                    if(_.isEmpty(this.$el.querySelector('#title').innerHTML)) {
                        this.titleEmptyError = true;
                    }else if(_.isEmpty(this.$el.querySelector('#article').innerHTML)) {
                        this.contentEmptyError = true;
                    } else {
                        Vue.http.post('/articles', {
                            title: this.$el.querySelector('#title').innerHTML,
                            author: this.$el.querySelector('#author').innerHTML,
                            content: this.$el.querySelector('#article').innerHTML,
                        })
                        .then((response) => {
                            console.log('success', response);
                            const body = response.body;
                            location.replace(body.show_url);
                        }, (response) => {
                            console.log('error', response);
                            const body = response.body;
//                            alert(_.values(body)[0]);
                        });
                    }
                }
            },
            toEdit: function() {
                if(this.mode === 'author-read') {
                    this.mode = 'author-edit';
                    this.editable = true;
                }
            },
            changeContent: function(event) {
                event.preventDefault();
                let article = this.$el.querySelector('#article');

                if(_.isEmpty(this.myContent) && event.key.length == 1) {
                    let p = document.createElement('p');
                    p.innerText = event.key;
                    article.innerText = '';
                    article.appendChild(p);
                    this.putCursorAtEnd(article);
                }

                this.myContent = article.innerHTML;
            },
            titleFocus: function() {
                if(this.titleEmptyError) {
                    this.titleEmptyError = false;
                }
            },
            articleFocus: function() {
                if(this.contentEmptyError) {
                    this.contentEmptyError = false;
                }
            }
        }
    }
</script>
