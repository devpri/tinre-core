<template>
  <div>
    <div
      class="sidebar absolute lg:relative h-full w-56 z-20 bg-primary-light"
      :class="{ active: $root.showSidebar }"
    >
      <slot></slot>
    </div>
    <div
      @click="togleSidebar"
      class="absolute z-10 h-full w-full top-0 left-0 opacity-25 bg-black lg:hidden transition-opacity duration-300"
      :class="{ hidden: !$root.showSidebar }"
    ></div>
  </div>
</template>

<script>
import { library } from '@fortawesome/fontawesome-svg-core'
import { faBars } from '@fortawesome/free-solid-svg-icons'

library.add([faBars])

export default {
  name: 'sidebar-content',

  data: () => ({
    showSidebar: false,
  }),

  watch: {
    $route(to, from) {
      this.sidebarState()
    },
  },

  created: function () {
    window.addEventListener('resize', this.sidebarState)
  },

  beforeDestroy: function () {
    window.removeEventListener('resize', this.sidebarState)
  },

  mounted() {
    this.sidebarState()
  },

  methods: {
    sidebarState() {
      if (window.innerWidth > '1024') {
        this.$root.showSidebar = true
      } else {
        this.$root.showSidebar = false
      }
    },

    togleSidebar() {
      this.$root.showSidebar = !this.$root.showSidebar
    },
  },
}
</script>
