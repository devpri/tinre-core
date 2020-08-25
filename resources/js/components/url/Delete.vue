<template>
  <div>
    <modal name="delete-url" :resizable="true" :scrollable="true" height="auto">
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Are you sure?') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group text-center">
          <button class="btn btn-primary" @click="this.delete">
            {{ __('Delete') }}
          </button>
          <button class="btn btn-secondary" @click="$modal.hide('delete-url')">
            {{ __('Cancel') }}
          </button>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'delete-url',

  props: ['url'],

  methods: {
    delete() {
      axios.delete('urls/' + this.url.id).then((response) => {
        this.$router.push('/')

        this.$modal.hide('delete-url')
      })
    },
  },
}
</script>
