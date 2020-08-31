<template>
  <div>
    <modal
      name="create-access-token"
      :resizable="true"
      :scrollable="true"
      height="auto"
    >
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Generate Access Token') }}</p>
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
          <button class="btn btn-primary" @click="create">
            {{ __('Create') }}
          </button>
          <button
            class="btn btn-secondary"
            @click="$modal.hide('create-access-token')"
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
  name: 'create-access-token',

  data: () => ({
    name: null,
    permissions: [],
  }),

  methods: {
    create() {
      axios
        .post('access-tokens', {
          name: this.name,
          permissions: this.permissions,
        })
        .then((response) => {
          this.name = null
          this.permissions = []

          this.$modal.hide('create-access-token')

          this.$router.push({
            name: 'access-tokens.show',
            params: {
              id: response.data.data.id,
              plainTextToken: response.data.data.plain_text_token,
            },
          })
        })
    },
  },
}
</script>
