<template>
  <div class="pr-4 self-center lg:hidden w-8 text-center">
    <font-awesome-icon
      v-if="!$root.showSidebar"
      @click="toggle"
      icon="bars"
      class="text-white"
    />
    <font-awesome-icon
      v-if="$root.showSidebar"
      @click="toggle"
      icon="times"
      class="text-white"
    />
  </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBars, faTimes } from '@fortawesome/free-solid-svg-icons'

library.add([faBars, faTimes])

export default {
  name: 'sidebar-button',

  methods: {
    toggle() {
      this.$root.showSidebar = !this.$root.showSidebar
    },
  },
}
</script>
