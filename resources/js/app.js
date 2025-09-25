/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue/dist/vue.esm.js'; //'vue';
import bootstrap from './bootstrap.js';
import '../../node_modules/vue-ads-pagination/dist/vue-ads-pagination.css';
import VueAdsPaginations from 'vue-ads-pagination';
import {ResultList, FilterInline, Filter, SurveySearch, SortingList, ParsedText} from "./components/index.js";
import {isArray, get} from 'lodash';

bootstrap();

Vue.mixin({
    methods:{
        field(field, item) {
            let key = get(item,`answers.${field}`,'-');
            let value =  get(OPTIONS, `${field}.${key}`, key);
            if(isArray(value)){
                return this.arrayToString(field, value);
            }
            return value;
        },
        arrayToString(field, value) {
            if(isArray(value)) {
                return value.map(item => {
                   return get(OPTIONS, `${field}.${item}`, item)
                }).join(', ')
            }

            return value;
        }
    }
})

Vue.component('vue-pagination', VueAdsPaginations);
Vue.component('result-list', ResultList);
Vue.component('filter-display', Filter);
Vue.component('filter-inline-display', FilterInline);
Vue.component('survey-search', SurveySearch);
Vue.component('sorting-list', SortingList);
Vue.component('parsed-text', ParsedText);

import BackToTop from 'vue-backtotop'
Vue.use(BackToTop)

import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
Vue.component('v-select', vSelect);

import VModal from 'vue-js-modal'
Vue.use(VModal);

import PortalVue from 'portal-vue';
Vue.use(PortalVue);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data: {

    },

});

import '../css/app.css';
