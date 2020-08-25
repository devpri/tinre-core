<template>
  <div>
    <modal
      name="update-profile"
      :resizable="true"
      :scrollable="true"
      height="auto"
    >
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Update Profile') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group mb-4">
          <input
            type="text"
            class="form-input w-full"
            name="name"
            placeholder="Name"
            v-model="name"
          />
        </div>
        <div class="form-group mb-4">
          <input
            type="password"
            class="form-input w-full"
            name="old-password"
            :placeholder="__('Current Password')"
            v-model="current_password"
          />
        </div>
        <div class="form-group mb-4">
          <input
            type="password"
            class="form-input w-full"
            name="new-password"
            :placeholder="__('New Password')"
            v-model="new_password"
          />
        </div>
        <div class="form-group mb-4 text-center">
          <button class="btn btn-primary" @click="update">
            {{ __('Update') }}
          </button>
          <button
            class="btn btn-secondary"
            @click="$modal.hide('update-profile')"
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
  name: 'update-profile',

  props: ['user'],

  data: () => ({
    name: null,
    current_password: null,
    new_password: null,
  }),

  mounted() {
    this.name = this.user.name
  },

  methods: {
    update() {
      axios
        .post('users/me', {
          name: this.name,
          current_password: this.current_password,
          new_password: this.new_password,
        })
        .then((response) => {
          this.$emit('userUpdated', response.data.data)

          this.name = null
          this.old_password = null
          this.new_password = null

          this.$modal.hide('update-profile')
        })
    },
  },
}
</script>
