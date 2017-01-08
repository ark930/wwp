<template>
    <div v-bind:mode="myMode" class="articleview">
        <div class='articleComponent'>
            <address class='info'>
                <time class="publishDate">{{ publish_date }}</time><span class="readTime">阅读 {{ read_min }} 分钟</span>
            </address>
            <h1 id="title" class="title" v-bind:class="titleError" v-bind:contenteditable="editable" placeholder="标题" required="true" @focus="titleFocus()" @keydown="changeTitle($event)">
                {{ title }}
            </h1>
            <article id="article" class="article" v-bind:class="contentError" v-bind:contenteditable="editable"
                     placeholder="你的故事" required='true' @focus="articleFocus()" @keydown="keydownContent($event)"
                     @keyup="keyupContent($event)">
                {{ html_content }}
            </article>
            <address class="info">
                <div id="author" v-bind:contenteditable="editable" placeholder="作者（选填）" class="authorName" @keydown="changeAuthor($event)">
                    {{ author }}
                </div><span class="publishChannel">发布于 <a href="http://a-z.press" target="blank">A-Z.press</a></span>
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
                editable: this.mode === 'author-edit',
                titleEmptyError: false,
                contentEmptyError: false,
                myMode: this.mode,
                isUpdate: false,
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
//                let br = document.createElement('br');
//                this.$el.querySelector('#article').appendChild(br);
            } else {
                this.$el.querySelector('#title').textContent = decodeURI(this.title);
                this.$el.querySelector('#author').textContent = decodeURI(this.author);
                this.$el.querySelector('#article').innerHTML = this.html_content;
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
                    } else if(_.isEmpty(this.$el.querySelector('#article').innerHTML)) {
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
                        Vue.http.post(url, {
                            title: encodeURI(this.$el.querySelector('#title').textContent),
                            author: encodeURI(this.$el.querySelector('#author').textContent),
                            html_content: this.$el.querySelector('#article').innerHTML,
                            text_content: this.$el.querySelector('#article').textContent,
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
                    this.isUpdate = true;
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
            keydownContent: function(e) {
//                let code = e.keyCode || e.which;
//                // Backspace event
//                if(code == 8) {
//                    let article = e.target;
//                    if(article.textContent.length == 1) {
//                        e.preventDefault();
//                        article.textContent = '';
//                        let br = document.createElement('br');
//                        article.appendChild(br);
//                    }
//                }
//                else if(_.isEmpty(article.textContent) && e.key.length == 1) {
//                    e.preventDefault();
//                    let p = document.createElement('p');
//                    p.innerText = e.key;
//                    article.innerText = '';
//                    article.appendChild(p);
//                    this.putCursorAtEnd(article);
//                }
            },
            keyupContent: function(e) {
//                let article = e.target;
//                console.log(article, article.innerHTML);
//                if(_.isEmpty(article.innerHTML)) {
//                    let br = document.createElement('br');
//                    article.appendChild(br);
//                }
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
