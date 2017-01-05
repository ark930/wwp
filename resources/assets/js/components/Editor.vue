<template>
    <!--<div class="articleview readmode">-->
        <!--<div class='articleComponent'>-->
            <!--<header>-->
                <!--<address class='address'>-->
                    <!--<span class="authorName" v-bind:contenteditable="editable" @keyup="changeAuthor($event)" placeholder="Author">{{ author }}</span>-->
                    <!--<span class="publishDate" v-if="is_read_only == 'true'"> · {{ publish_date }} ·</span>-->
                    <!--<span class="publishChannel" v-if="is_read_only == 'true'">通过 <a href="http://www.a-z.press" target="blank">A-Z.press</a> 发布</span>-->
                <!--</address>-->
                <!--<h1 class='title' v-bind:contenteditable="editable" @keyup="changeTitle($event)" placeholder="Title">-->
                    <!--{{ myTitle }}-->
                <!--</h1>-->
            <!--</header>-->
            <!--<article class="article" v-bind:contenteditable="editable" @keyup="changeContent($event)"  placeholder="Your story (Plain Text (Yet">-->
                <!--{{ content }}-->
            <!--</article>-->
        <!--</div>-->
        <!--<div class="actions">-->
            <!--<button class="btn-publish" v-if="show_edit_button == 'true'" @click="toEdit">{{ editorButtonText }}</button>-->
        <!--</div>-->
    <!--</div>-->
    <div class="articleview readmode">
        <div class='articleComponent'>
            <address class='info' v-if="is_read_only == 'true'">
                <time class="publishDate">{{ publish_date }}</time>
                <span class="readTime">阅读 5 分钟</span>
            </address>
            <h1 id="title" class='title' v-bind:contenteditable="editable" placeholder="标题" @keyup="changeTitle($event)">
                {{ title }}
            </h1>
            <article id="article" class="article" v-bind:contenteditable="editable" placeholder="你的故事 (Plain Text (Yet"  @keyup="changeContent($event)">
                {{ content }}
            </article>
            <address class="info">
                <div id="author" class="authorName" v-bind:contenteditable="editable" placeholder="作者 (可选项)"  @keyup="changeAuthor($event)">
                    {{ author }}
                </div>
                <span class="publishChannel" v-if="is_read_only == 'true'">发布于 <a href="http://www.a-z.press" target="blank">A-Z.press</a></span>
            </address>
            <div class="actions">
                <button class="btn-publish" v-if="is_read_only == 'false'" @click="toEdit">编辑/发布</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title', 'content', 'author', 'publish_date', 'show_edit_button', 'is_read_only'],
        data: function() {
            return {
                editorButtonText: '发布',
                editable: true,
                canPublish: true,
                myTitle: this.title,
                myAuthor: this.author,
                myContent: this.content,
//                publishDate: this.publishDate,
            }
        },
        mounted() {
            if(!this.title) {
                this.$el.querySelector('#title').innerText = '';
            }
            if(!this.author) {
                this.$el.querySelector('#author').innerText = '';
            }
            if(!this.content) {
                this.$el.querySelector('#article').innerText = '';
            }
            if(this.is_read_only == 'true') {
                this.editable = false;
            }
            console.log('Component mounted.');
        },
        methods: {
            toEdit: function() {
                if(this.canPublish === true) {
                    Vue.http.post('articles', {
                            title: this.myTitle,
                            author: this.myAuthor,
                            content: this.myContent,
                        })
                        .then((response) => {
                            const body = response.body;
                            location.replace(body.show_url);
                        }, (response) => {
                            const body = response.body;
                            alert(_.values(body)[0]);
                        });
                }
            },
            changeAuthor: function(event) {
                this.myAuthor = this.$el.querySelector('#author').innerText;
            },
            changeTitle: function(event) {
                this.myTitle = this.$el.querySelector('#title').innerText;
            },
            changeContent: function(event) {
                this.myContent = this.$el.querySelector('#article').innerText;
            }
        }
    }
</script>
