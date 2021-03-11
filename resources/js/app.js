import Vue from 'vue'
import App from './views/App'
import router from './router'
import { store } from './store'
import BootstrapVue from 'bootstrap-vue'
import VueLodash from 'vue-lodash'
import VueTimeago from 'vue-timeago'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'
import VeeValidate, { Validator } from 'vee-validate'
import { VALIDATION_DICTIONARY } from './utils/validation-dictionary'
import VueI18n from 'vue-i18n'
import i18n from './lang'
import VueSweetalert2 from 'vue-sweetalert2';
import en from 'vee-validate/dist/locale/en'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'font-awesome/css/font-awesome.min.css'
import 'video.js/dist/video-js.css'
import 'sweetalert2/dist/sweetalert2.min.css';
import './assets/scss/style.scss'

const options = { name: 'lodash' } // customize the way you want to call it

Vue.use(VueLodash, options) // options is optional

Vue.use(BootstrapVue)

Vue.use(VueTimeago, {
    name: 'Timeago', // Component name, `Timeago` by default
    locale: 'en', // Default locale
    // We use `date-fns` under the hood
    // So you can use all locales from it
    locales: {
        'zh-CN': require('date-fns/locale/zh_cn'),
        ja: require('date-fns/locale/ja'),
    },
    converter (date, locale, { includeSeconds, addSuffix = true }) {
        const distanceInWordsStrict = require(
            'date-fns/distance_in_words_strict')
        return distanceInWordsStrict(Date.now(), date,
            { locale, addSuffix, includeSeconds })
    },
})

Vue.use(VueMoment, {
    moment,
})

Validator.localize({ en })
Validator.localize(VALIDATION_DICTIONARY)
Vue.use(VeeValidate)
Vue.use(VueI18n)
Vue.use(VueSweetalert2);

Vue.config.productionTip = false

const app = new Vue({
    el: '#app',
    router,
    store,
    i18n,
    watch: {
        $route (to, from) {
            document.title = to.meta.title || 'InfyChat'
        },
    },
    render: h => h(App),
})

export default app
