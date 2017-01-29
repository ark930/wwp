
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('editor', require('./components/Editor.vue'));
Vue.directive('demo', {
    twoWay: true,
    bind: function (el) {
        this.handler = function () {
            this.set(this.el.innerHTML)
        }.bind(this);
        el.addEventListener('keyup', this.handler)
    },
    update: function (el, binding) {
        el.innerHTML = binding.value || ''
    },
    unbind: function (el) {
        el.removeEventListener('keyup', this.handler)
    }
});

Vue.directive('empty-error', {
    inserted: function (el) {
        el.addEventListener('focus', function(e) {
            e.target.classList.remove('form-error');
        });
    },
    update: function (el, binding) {
        if (binding.value) {
            el.classList.add('form-error');
        }
    }
});

Vue.directive('temp-save', {
    inserted: function (el) {
        el.addEventListener('keyup', function (e) {
            if(e.target.id == 'content') {
                localStorage.setItem(e.target.id, e.target.innerHTML);
            } else {
                localStorage.setItem(e.target.id, e.target.innerText);
            }
        });
    }
});

Vue.directive('paste', {
    inserted: function (el) {
        el.addEventListener('paste', function (e) {
            // only paste plain text to input field
            e.preventDefault();
            // get text representation of clipboard
            let text = e.clipboardData.getData("text/plain");
            // insert text manually
            document.execCommand("insertHTML", false, text);
        });
    }
});

const app = new Vue({
    el: '#app'
});
