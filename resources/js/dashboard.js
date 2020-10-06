import Vue from 'vue'
import router from './router'
import Localization from '@/mixins/Localization'
import VTooltip from 'v-tooltip'
import VModal from 'vue-js-modal'
import * as moment from 'moment/moment'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import './plugins'
import './components'

Vue.mixin(Localization)

Vue.use(VTooltip, {
  defaultBoundariesElement: 'viewPort',
})

Vue.use(VModal)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.filter('date', function (date) {
  if (moment(date).isValid()) {
    return moment(date).format(window.config.date_format)
  }

  return '-'
})

Vue.prototype.$config = window.config

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,

  data: () => ({
    showSidebar: false,
  }),

  mounted() {
    this.$loading = this.$refs.loading
  },
})
