<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card mb-8">
      <div class="card-header flex items-center justify-between p-4">
        <h1 class="text-xl">{{ user.name }}</h1>
        <div>
          <button
            v-if="
              user.authorized_actions &&
              user.authorized_actions.includes('delete')
            "
            class="btn btn-danger btn-sm py-2"
            @click="$modal.show('delete-user')"
          >
            <font-awesome-icon icon="trash-alt" />
          </button>
          <button
            v-if="
              user.authorized_actions &&
              user.authorized_actions.includes('update')
            "
            class="btn btn-secondary btn-sm py-2"
            @click="$modal.show('update-user')"
          >
            <font-awesome-icon :icon="['far', 'edit']" />
          </button>
        </div>
      </div>
      <div class="card-content">
        <div class="list">
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Active') }}</div>
            <div class="w-full lg:w-1/2">
              {{ user.active ? __('Yes') : __('No') }}
            </div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Name') }}</div>
            <div class="w-full lg:w-1/2">{{ user.name }}</div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Email') }}</div>
            <div class="w-full lg:w-1/2">
              {{ user.email }}
            </div>
          </div>
          <div class="list-item flex flex-wrap">
            <div class="w-full lg:w-1/2">{{ __('Role') }}</div>
            <div class="w-full lg:w-1/2">
              {{ user.role }}
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
    <update-user
      v-if="
        user.authorized_actions && user.authorized_actions.includes('update')
      "
      :user="user"
      @userUpdated="user = $event"
    ></update-user>
    <delete-user
      v-if="
        user.authorized_actions && user.authorized_actions.includes('delete')
      "
      :user="user"
    ></delete-user>
  </div>
</template>

<script>
import axios from 'axios'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faEdit } from '@fortawesome/free-regular-svg-icons'
import { faTrashAlt } from '@fortawesome/free-solid-svg-icons'
import UpdateUser from '../../components/user/Update'
import DeleteUser from '../../components/user/Delete'

library.add([faEdit, faTrashAlt])

export default {
  components: { UpdateUser, DeleteUser },

  metaInfo() {
    return { title: this.__('User') }
  },

  data: () => ({
    user: {},
  }),

  mounted() {
    this.getUser()
  },

  methods: {
    getUser() {
      axios.get(`users/${this.$route.params.id}}`).then((response) => {
        this.user = response.data.data
      })
    },
  },
}
</script>
