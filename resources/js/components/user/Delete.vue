<template>
  <div>
    <modal
      name="delete-user"
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
          <button class="btn btn-secondary" @click="$modal.hide('delete-user')">
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
  name: 'delete-user',

  props: ['user'],

  methods: {
    delete() {
      axios.delete('users/' + this.user.id).then((response) => {
        this.$router.push('/users')

        this.$modal.hide('delete-user')
      })
    },
  },
}
</script>
