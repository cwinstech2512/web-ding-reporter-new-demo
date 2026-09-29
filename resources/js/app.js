import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import axios from '~/plugins/axios'
import i18n from '~/plugins/i18n'
import eventBus from '~/plugins/eventBus'
import App from '~/components/App'
import vuetify from '~/plugins/vuetify'
import '../../node_modules/vuetify/dist/vuetify.min.css'
import '../../node_modules/@mdi/font/css/materialdesignicons.min.css'

import VueLoading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import swal from 'sweetalert2'

import '~/plugins'
import '~/components'

Vue.use(VueLoading)

Vue.prototype.$swal = swal;
Vue.prototype.$axios = axios
Vue.prototype.$bus = eventBus
Vue.prototype.$base_url = process.env.MIX_FRONTEND_DEMO === 'true' ? '.' : process.env.MIX_APP_URL

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
	vuetify: vuetify,
	i18n,
	store,
	router,
	...App
})
