<template>
  <div>
    <modal
      name="delete-access-token"
      :resizable="true"
      :scrollable="true"
      height="auto"
    >
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Are you sure?') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group text-center">
          <button class="btn btn-primary" @click="this.delete">
            {{ __('Delete') }}
          </button>
          <button
            class="btn btn-secondary"
            @click="$modal.hide('delete-access-token')"
          >
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
  name: 'delete-access-token',

  props: ['token'],

  methods: {
    delete() {
      axios.delete(`access-tokens/${this.token.id}`).then((response) => {
        this.$router.push({ name: 'access-tokens.index' })

        this.$modal.hide('delete-access-token')
      })
    },
  },
}
</script>
