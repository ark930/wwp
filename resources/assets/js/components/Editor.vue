<template>
    <div id="articleview-read" class="articleview editmode">
        <div class='articleComponent'>
            <header>
                <address class='address'>
                    <span class="authorName" v-bind:contenteditable="editable">{{ author }}</span> ·
                    <!--<span class="publishDate">{{ publish_date }}</span> ·-->
                    <!--<span class="publishChannel">通过 <a href="http://www.a-z.press" target="blank">A-Z.press</a> 发布</span>-->
                </address>
                <h1 class='title' contenteditable="true">
                    {{ title }}
                </h1>
            </header>
            <article class="article" v-bind:contenteditable="editable" placeholder="Your story - Plain Text---Yet">
                {{ content }}
                <!--<p>之前怎么也找不到的写作工具，现在有了。</p>-->
                <!--<ul>-->
                    <!--<li>优质的阅读体验，配得上你的读者</li>-->
                    <!--<li>写完了分享到任何地方</li>-->
                    <!--<li>可以写一篇，也可以写一篇篇（在专栏里（还没做</li>-->
                    <!--<li>没有模板样式之类的鬼，专注创作本身</li>-->
                <!--</ul>-->
                <!--<p><a href="" class='block' id="startWriting" @click="showModal = true">开始写作</a></p>-->
            </article>
        </div>
        <div class="actions">
            <button class="btn-publish" v-if="show_edit_button == 'true'" @click="toEdit">{{ editorButtonText }}</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title', 'content', 'author', 'publish_date', 'show_edit_button'],
        data: function() {
            return {
                editorButtonText: '编辑',
                editable: false,
                canPublish: false,
                author: this.author,
                title: this.title,
                content: this.content,
//                author: this.author,
//                publishDate: this.publishDate,
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            toEdit: function() {
                if(this.canPublish === true) {
                    Vue.http.post('articles',
                        {
                            title: this.title,
                            author: this.author,
                            content: this.content,
                        })
                        .then((response) => {
                            const body = response.body;
                            window.location.replace(body.show_url);
                        }, (response) => {
                            const body = response.body;
                            alert(_.values(body)[0]);
                        });
                } else {
                    this.editorButtonText = '发布';
                    this.editable = true;
                    this.canPublish = true;
                }
            }
        }
    }
</script>
