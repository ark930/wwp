var articlepage = new Vue({
    el: "#app",
    data: {
        is_read_only: !1,
        showpannel: !1,
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
    },
    methods: {
        toggleMyPannel: function () {
            this.showpannel = !this.showpannel
        }
    }
});