import Vue from 'vue'
import Localization from '@/mixins/Localization'
import CreateUrlGuest from '@/components/url/CreateGuest'
import VTooltip from 'v-tooltip'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import './plugins'

Vue.mixin(Localization)

Vue.component('create-url-guest', CreateUrlGuest)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.use(VTooltip, {
  defaultBoundariesElement: 'viewPort',
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
})
