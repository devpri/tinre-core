<template>
  <div>
    <modal
      name="change-email"
      :resizable="true"
      :scrollable="true"
      height="auto"
    >
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Change Email') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group mb-4">
          <input
            type="email"
            class="form-input w-full"
            name="email"
            :placeholder="__('Email')"
            v-model="email"
          />
        </div>
        <div class="form-group mb-4">
          <input
            type="password"
            class="form-input w-full"
            name="password"
            :placeholder="__('Password')"
            v-model="password"
          />
        </div>
        <div class="form-group mb-4 text-center">
          <button class="btn btn-primary" @click="change">
            {{ __('Change') }}
          </button>
          <button
            class="btn btn-secondary"
            @click="$modal.hide('change-email')"
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
  name: 'change-email',

  data: () => ({
    email: null,
    password: null,
  }),

  methods: {
    change() {
      axios({
        method: 'post',
        url: 'email/change',
        baseURL: '/dashboard/',
        data: {
          email: this.email,
          password: this.password,
        },
        // eslint-disable-next-line no-unused-vars
      }).then((response) => {
        this.email = null
        this.password = null

        this.$modal.hide('change-email')
      })
    },
  },
}
</script>
