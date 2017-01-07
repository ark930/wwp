<template>
    <div v-bind:mode="myMode" class="articleview">
        <div class='articleComponent'>
            <address class='info'>
                <time class="publishDate">{{ publish_date }}</time><span class="readTime">阅读 {{ read_min }} 分钟</span>
            </address>
            <h1 id="title" class="title" v-bind:class="titleError" v-bind:contenteditable="editable" placeholder="标题" required="true" @focus="titleFocus()" @keydown="changeTitle($event)">
                {{ title }}
            </h1>
            <article id="article" class="article" v-bind:class="contentError" v-bind:contenteditable="editable" placeholder="你的故事" required='true' @focus="articleFocus()" @keydown="changeContent($event)">
                {{ content }}
            </article>
            <address class="info">
                <div id="author" v-bind:contenteditable="editable" placeholder="作者（选填）" class="authorName" @keydown="changeAuthor($event)">
                    {{ author }}
                </div><span class="publishChannel">发布于 <a href="http://www.a-z.press" target="blank">A-Z.press</a></span>
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
                myMode: this.mode,
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
            if(this.myMode === 'author-edit') {
                this.$el.querySelector('#title').innerHTML = '';
                this.$el.querySelector('#author').innerHTML = '';
                this.$el.querySelector('#article').innerHTML = '';
//                let p = document.createElement('p');
//                let br = document.createElement('br');
//                p.appendChild(br);
//                this.$el.querySelector('#article').appendChild(p);
            } else {
                this.$el.querySelector('#title').textContent = this.title;
                this.$el.querySelector('#author').textContent = this.author;
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
                if(this.myMode === 'author-edit') {
                    if(_.isEmpty(this.$el.querySelector('#title').innerHTML)) {
                        this.titleEmptyError = true;
                    }else if(_.isEmpty(this.$el.querySelector('#article').innerHTML)) {
                        this.contentEmptyError = true;
                    } else {
                        Vue.http.post('/articles', {
                            title: this.$el.querySelector('#title').textContent,
                            author: this.$el.querySelector('#author').textContent,
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
                if(this.myMode === 'author-read') {
                    this.myMode = 'author-edit';
                    this.editable = true;
                }
            },
            changeTitle: function(e) {
                console.log(e);
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
            changeContent: function(e) {
                console.log(e, e.target.textContent);
                let article = e.target;

                let code = e.keyCode || e.which;
                // Backspace event
                if(code == 8) {
                    if(article.textContent.length == 1) {
                        e.preventDefault();
                        article.textContent = '';
//                        article.innerHtml = '';
                    }
                } else if(_.isEmpty(article.textContent) && e.key.length == 1) {
                    e.preventDefault();
                    let p = document.createElement('p');
                    p.innerText = e.key;
                    article.innerText = '';
                    article.appendChild(p);
                    this.putCursorAtEnd(article);
                }
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
