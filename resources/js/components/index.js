import Vue from 'vue'
import Child from './Child'
import CreateUrl from './url/Create'
import Loading from './Loading'
import SidebarButton from './sidebar/Button'
import SidebarContent from './sidebar/Content'

// Components that are registered globaly.
;[Child, Loading, CreateUrl, SidebarButton, SidebarContent].forEach(
  (Component) => {
    Vue.component(Component.name, Component)
  }
)
