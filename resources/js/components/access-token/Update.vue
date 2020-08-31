<template>
  <div>
    <modal
      name="update-access-token"
      :resizable="true"
      :scrollable="true"
      height="auto"
    >
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Update Access Token') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group mb-4">
          <input
            type="text"
            class="form-input w-full"
            name="name"
            :placeholder="__('Name')"
            v-model="name"
          />
        </div>
        <div class="form-group mb-4">
          <label
            v-for="permission in $config.user_api_permissions"
            v-bind:key="permission"
            class="cursor-pointer block"
            :for="permission"
          >
            <input
              class="mr-1"
              type="checkbox"
              :value="permission"
              :name="permission"
              :id="permission"
              v-model="permissions"
            />
            <span class="text-sm text-gray-900">{{ __(permission) }}</span>
          </label>
        </div>
        <div class="form-group mb-4 text-center">
          <button class="btn btn-primary" @click="update">
            {{ __('Update') }}
          </button>
          <button
            class="btn btn-secondary"
            @click="$modal.hide('update-access-token')"
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
  name: 'update-access-token',

  props: ['token'],

  data: () => ({
    name: null,
    permissions: [],
  }),

  mounted() {
    this.name = this.token.name
    this.permissions = this.token.permissions
  },

  methods: {
    update() {
      axios
        .post(`access-tokens/${this.token.id}`, {
          name: this.name,
          permissions: this.permissions,
        })
        .then((response) => {
          this.$emit('tokenUpdated', response.data.data)

          this.$modal.hide('update-access-token')
        })
    },
  },
}
</script>
