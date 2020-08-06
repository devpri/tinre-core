import Vue from 'vue'
import Localization from '@/mixins/Localization'
import CreateUrlGuest from '@/components/url/CreateGuest'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import './plugins'

Vue.mixin(Localization)

Vue.component('create-url-guest', CreateUrlGuest)

Vue.component('font-awesome-icon', FontAwesomeIcon)

/* eslint-disable no-new */
new Vue({
  el: '#app',
})
