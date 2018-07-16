window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */
import VueResource from 'vue-resource';
import Vue from 'vue/dist/vue';

window.Vue = Vue;
Vue.use(VueResource);


import VueSweetalert from 'vue-sweetalert';
Vue.use(VueSweetalert);
window.VueSweetalert = VueSweetalert;
window.axios=require('axios')


Vue.use(require('vue-moment'));
Vue.use(require('moment'));

import VModal from 'vue-js-modal'
 
Vue.use(VModal)
import mymodal from './components/Mobiles/login.vue'
Vue.component('mymodal', mymodal)
Vue.prototype.$mymodal=mymodal

import loader from './components/Mobiles/loader.vue'
Vue.component('loader', loader)
Vue.prototype.$loader=loader;

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */
/*
Vue.http.interceptors.push((request, next) => {
    request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;
    next();
});
*/
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });




import VueInternalization from 'vue-i18n';
import Locales from './Vue/translations.js';

Vue.use(VueInternalization);

// Vue.config.lang = 'fr'; 
Object.keys(Locales).forEach(function (lang) {
  Vue.locale(lang, Locales[lang])
});