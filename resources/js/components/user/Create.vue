<template>
  <div>
    <modal
      name="create-user"
      :resizable="true"
      :scrollable="true"
      height="auto"
    >
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Create User') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group mb-4">
          <label class="cursor-pointer" for="active">
            <input
              class="mr-1"
              type="checkbox"
              name="active"
              id="active"
              v-model="active"
            />
            <span class="text-sm text-gray-900">{{ __('Active') }}</span>
          </label>
        </div>
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
          <input
            type="email"
            class="form-input w-full"
            name="email"
            :placeholder="__('Email')"
            v-model="email"
          />
        </div>
        <div class="form-group mb-4">
          <select v-model="role" class="form-input w-full">
            <option :value="null" disabled>{{ __('Role') }}</option>
            <option v-for="role in $config.roles" :key="role" :value="role">
              {{ __(role) }}
            </option>
          </select>
          <div
            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
          >
            <svg
              class="fill-current h-4 w-4"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
            >
              <path
                d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
              />
            </svg>
          </div>
        </div>
        <div class="form-group mb-4">
          <input
            type="password"
            class="form-input w-full"
            name="old-password"
            :placeholder="__('Password')"
            v-model="password"
          />
        </div>
        <div class="form-group mb-4 text-center">
          <button class="btn btn-primary" @click="create">
            {{ __('Create') }}
          </button>
          <button class="btn btn-secondary" @click="$modal.hide('create-user')">
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
  name: 'create-user',

  data: () => ({
    active: true,
    name: null,
    email: null,
    role: null,
    password: null,
  }),

  methods: {
    create() {
      axios
        .post('users', {
          active: this.active,
          name: this.name,
          email: this.email,
          role: this.role,
          password: this.password,
        })
        .then((response) => {
          this.$emit('userCreated', response.data.data)

          this.active = null
          this.name = null
          this.email = null
          this.role = null
          this.password = null

          this.$modal.hide('create-user')
        })
    },
  },
}
</script>
