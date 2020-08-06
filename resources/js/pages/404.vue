<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card mb-8 py-8 px-4 text-center">
      <h3 class="mb-4">{{ __('Page not found!') }}</h3>
      <div class="links">
        <router-link :to="{ name: 'home' }">{{ __('Go home') }}</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  metaInfo() {
    return { title: this.__('Not Found') }
  },
}
</script>
