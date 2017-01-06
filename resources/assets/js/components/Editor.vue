<template>
    <div id="articleview-read" class="articleview" v-bind:class="classObject">
        <div class='articleComponent'>
            <address class='info' v-if="is_read_only">
                <time class="publishDate">{{ publish_date }}</time>
                <span class="readTime">阅读 {{ read_min }} 分钟</span>
            </address>
            <h1 id="title" class="title" v-if="showTitle" v-bind:contenteditable="editable" placeholder="标题" @keyup="changeTitle($event)" v-text="title">
            </h1>
            <article id="article" class="article" v-bind:class="contentError" v-bind:contenteditable="editable" placeholder="你的故事" required='true' @keyup="changeContent($event)">
                {{ content }}
                <!--<p>举个例子，有一类算法称为分类算法，它可以将数据划分为不同的组别。一个用来识别手写数字的分类算法，不用修改一行代码，就可以把这个算法用来将电子邮件分为垃圾邮件和普通邮件。算法没变，但是输入的训练数据变了，因此它得出了不同的分类逻辑。</p>-->
                <!--<p>假设你是一名房地产经纪人，生意越做越大，因此你雇了一批新员工来帮你。但是问题来了——你可以看一眼房子就知道它到底值多少钱，新员工没有经验，不知道如何估价。</p>-->
                <!--<p>为了帮助你的新员工（也许就是为了给自己放个假嘻嘻），你决定写个小软件，可以根据房屋大小、地段以及类似房屋的成交价等因素来评估一间房屋的价格。</p>-->
                <!--<p>为了编写软件，你将包含每<a href="" class='block'>开始写作</a>一套房产的训练数据输入你的机器学习算法。算法尝试找出应该使用何种运算来得出价格数字。</p>-->
                <!--<p>看了这些题，你能明白这些测验里面是什么样的数学问题吗？你知道，你应该对算式左边的数字“做些什么”以得出算式右边的答案。</p>-->
                <!--<p>“我不喜欢本杰明·格雷厄姆和巴菲特遵循的那套他的做法。必须理解巴菲特，他在那么年轻的年纪发现了格雷厄姆，然后就以此人为目标努力。格雷厄姆的观点改变了巴菲特的一生，他早年大多在近距离崇拜格雷厄姆。</p>-->
                <!--<q>我有否忽视了什么？有没有更好的选择？我是否忽视了某些依据？当有人力介入时，我是否考虑到了其局限性？什么因素是不确定的，为什么？我是否只考虑到了目前的趋势？我有否误解了什么？我使用了正确的定义吗？我是否综合考虑了所有相关的因素？我采用了合适的衡量标准吗？我有否混淆了成因和相关性？如果我的目标是基于某个我认为正确但其实是错误的理念，会如何？其中会有随机性的或者系统性的错误吗？对于我所得到的结果，有没有其他原因可以解释？我有否考虑过，整个系统或者某些互动环节的可能会出现我不希望发生的变化？</q>-->
                <!--<p>之前怎么也找不到的写作工具，现在有了</p>-->
                <!--<ul>-->
                    <!--<li>优质的阅读体验，配得上你的读者</li>-->
                    <!--<li>写完了分享到任何地方</li>-->
                    <!--<li>可以写一篇，也可以写一篇篇（在专栏里（还没做</li>-->
                    <!--<li>没有模板样式之类的鬼，专注创作本身</li>-->
                <!--</ul>-->
                <!--<p><a href="" class='block'>开始写作</a></p>-->
            </article>
            <address class="info">
                <div id="author" class="authorName" v-if="showAuthor" v-bind:contenteditable="editable" placeholder="作者（选填）" @keyup="changeAuthor($event)">
                    {{ author }}
                </div>
                <span class="publishChannel" v-if="is_read_only">发布于 <a href="http://www.a-z.press" target="blank">A-Z.press</a></span>
            </address>
            <div class="actions">
                <button class="btn-publish" v-if="!is_read_only" @click="toEdit">编辑/发布</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title', 'content', 'author', 'publish_date', 'show_edit_button', 'is_read_only', 'read_min'],
        data: function() {
            return {
                editorButtonText: '发布',
                editable: true,
                canPublish: true,
                myTitle: this.title,
                myAuthor: this.author,
                myContent: this.content,
                contentEmptyError: false,
                showAuthor: true,
                showTitle: true,
//                publishDate: this.publishDate,
            }
        },
        computed: {
            classObject: function () {
                return {
                    editmode: !this.is_read_only,
                    readmode: this.is_read_only,
                }
            },
            contentError: function () {
                return {
                    'form-error': this.contentEmptyError
                }
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
            if(this.is_read_only) {
                this.editable = false;
            }

            if(this.is_read_only) {
                if(_.isEmpty(this.author)) {
                    this.showAuthor = false;
                }
                if(_.isEmpty(this.title)) {
                    this.showTitle = false;
                }
            }

            console.log('Component mounted.');
        },
        methods: {
            toEdit: function() {
                if(this.canPublish === true) {
                    if(_.isEmpty(this.myContent)) {
                        this.contentEmptyError = true;
                    } else {
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
                }
            },
            changeAuthor: function(event) {
                this.myAuthor = this.$el.querySelector('#author').innerText;
            },
            changeTitle: function(event) {
                this.myTitle = this.$el.querySelector('#title').innerText;
            },
            changeContent: function(event) {
                if(this.contentEmptyError && this.myContent) {
                    this.contentEmptyError = false;
                }
                this.myContent = this.$el.querySelector('#article').innerText;
            }
        }
    }
</script>
