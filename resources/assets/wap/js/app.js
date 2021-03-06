
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

window.Vue = require('vue');

// import { Button } from 'vant';
// Vue.use(Button);

// import Vant from 'vant';
// Vue.use(Vant);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('hello', require('./components/Hello.vue'));
// Vue.component('goods', require('./components/Goods.vue'));

const app = new Vue({
    el: '#app'
});
