<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card mb-8">
      <div class="card-header flex items-center justify-between p-4">
        <h1 class="text-xl">{{ user.name }}</h1>
        <button
          v-if="
            user.authorized_actions &&
            user.authorized_actions.includes('updateOwn')
          "
          class="btn btn-secondary btn-sm py-2"
          @click="$modal.show('update-profile')"
        >
          <font-awesome-icon :icon="['far', 'edit']" />
        </button>
      </div>
      <div class="card-content">
        <div class="list">
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Name') }}</div>
            <div class="w-full lg:w-1/2">{{ user.name }}</div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Email') }}</div>
            <div class="w-full lg:w-1/2">
              {{ user.email }}
              <button
                v-if="
                  user.authorized_actions &&
                  user.authorized_actions.includes('changeEmail')
                "
                class="btn btn-secondary btn-sm"
                @click="$modal.show('change-email')"
              >
                <font-awesome-icon :icon="['far', 'edit']" />
              </button>
            </div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Email Verified At') }}</div>
            <div class="w-full lg:w-1/2">
              {{ user.email_verified_at | date }}
            </div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Created At') }}</div>
            <div class="w-full lg:w-1/2">{{ user.created_at | date }}</div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Updated At') }}</div>
            <div class="w-full lg:w-1/2">{{ user.updated_at | date }}</div>
          </div>
        </div>
      </div>
    </div>
    <change-email
      v-if="
        user.authorized_actions &&
        user.authorized_actions.includes('changeEmail')
      "
    ></change-email>
    <update-profile
      v-if="
        user.authorized_actions && user.authorized_actions.includes('updateOwn')
      "
      :user="user"
      @userUpdated="user = $event"
    ></update-profile>
  </div>
</template>

<script>
import axios from 'axios'
import ChangeEmail from '~/components/email/Change'
import UpdateProfile from '~/components/profile/Update'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEdit } from '@fortawesome/free-regular-svg-icons'

library.add([faEdit])

export default {
  components: {
    ChangeEmail,
    UpdateProfile,
  },

  metaInfo() {
    return { title: this.__('Profile') }
  },

  data: () => ({
    user: {},
  }),

  mounted() {
    this.getUser()
  },

  methods: {
    async getUser() {
      try {
        const { data } = await axios.get('users/me')

        this.user = data.data
      } catch (e) {}
    },
  },
}
</script>
