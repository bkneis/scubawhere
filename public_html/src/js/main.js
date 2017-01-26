'use strict';

import Vue from 'vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource';
import router from './routes.js';
import * as App from './App.vue';

/**
 * Vue Plugins
 */
Vue.use(VueResource);
Vue.use(VueRouter);

/**
 * Register HTTP interceptors
 * @todo How to extract this to seperate file then import them like 'import './middleware'
 */

/**
 * Attach a csrf token to each POST request
 */
Vue.http.interceptors.push((request, next) => {
    if (request.method === 'POST') {
        request.body._token = window.token;
        //request.headers.set('X-CSRF-TOKEN', window.token);
    }
    // continue to next interceptor
    next();
});

/**
 * If a response is returned with a HTTP status 401 (Unauthorized)
 * then emit the logout event to return the user to the login page.
 */
/*Vue.http.interceptors.push((request, next) => {
    next(response => {
        if (response.status === 401) {
            Vue.prototype.$bus.$emit('logout');
        }
    });
});*/

/**
 * Register global Vue components
 */
import './globals.js';

/**
 * Quick and dirty event bus, this can now be accessed on all vue instances via this.$bus
 */
const EventBus = new Vue({});
Vue.prototype.$bus = EventBus;

/**
 * Main Vue Component
 */
const app = new Vue({
    el : '#app',
    render : (createElement) => createElement(App),
    router
});
