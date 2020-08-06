import Vue from 'vue'
import Child from './Child'
import CreateUrl from './url/Create'
import Loading from './Loading'

// Components that are registered globaly.
;[Child, Loading, CreateUrl].forEach((Component) => {
  Vue.component(Component.name, Component)
})
