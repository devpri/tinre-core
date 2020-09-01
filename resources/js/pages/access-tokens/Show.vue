<template>
  <div class="content w-auto flex-1 px-4">
    <div v-if="$route.params.plainTextToken" class="card p-4 mb-4 text-center">
      <p class="text-lg font-bold">
        {{ __('Your access token has been generated.') }}
      </p>
      <p class="text-lg font-bold text-red-600 mb-2">
        {{ __('The token is visible only once.') }}
      </p>
      <div class="break-all">{{ $route.params.plainTextToken }}</div>
    </div>
    <div class="card">
      <div class="list overflow-hidden">
        <div class="list-header w-full self-start">
          <div class="flex flex-wrap -mx-2">
            <div class="w-auto flex-1 px-2 self-center text-xl">
              {{ token.name }}
            </div>
            <div class="w-auto px-2">
              <button
                v-if="
                  token.authorized_actions &&
                  token.authorized_actions.includes('update')
                "
                class="btn btn-secondary btn-sm py-2"
                @click="$modal.show('update-access-token')"
              >
                <font-awesome-icon :icon="['far', 'edit']" />
              </button>
              <button
                v-if="
                  token.authorized_actions &&
                  token.authorized_actions.includes('delete')
                "
                class="btn btn-danger btn-sm py-2"
                @click="$modal.show('delete-access-token')"
              >
                <font-awesome-icon icon="trash-alt" />
              </button>
            </div>
          </div>
        </div>
        <div v-if="loading" class="w-full text-center p-4">
          <font-awesome-icon icon="spinner" class="fa-2x fa-spin" />
        </div>
        <div v-if="!loading" class="flex flex-wrap w-full">
          <div class="list-content w-full">
            <div class="list-item flex flex-wrap">
              <div class="w-full lg:w-1/2">{{ __('Name') }}</div>
              <div class="w-full lg:w-1/2">{{ token.name }}</div>
            </div>
            <div v-if="token.user" class="list-item flex flex-wrap">
              <div class="w-full lg:w-1/2">{{ __('User') }}</div>
              <div class="w-full lg:w-1/2">
                <a
                  v-if="token.user.authorized_actions.includes('view')"
                  class="text-sm cursor-pointer"
                  @click="
                    $router.push({
                      name: 'users.show',
                      params: { id: token.user.id },
                    })
                  "
                >
                  {{ token.user.name }}
                </a>
                <span class="text-sm" v-else>{{ token.user.name }} </span>
              </div>
            </div>
            <div class="list-item flex flex-wrap">
              <div class="w-full lg:w-1/2">{{ __('Permissions') }}</div>
              <div class="w-full lg:w-1/2">
                <div
                  v-if="
                    Array.isArray(token.permissions) && token.permissions.length
                  "
                >
                  <p
                    v-for="permission in token.permissions"
                    v-bind:key="permission"
                  >
                    {{ permission }}
                  </p>
                </div>
                <div v-else>-</div>
              </div>
            </div>
            <div class="list-item flex flex-wrap">
              <div class="w-full lg:w-1/2">{{ __('Last Used At') }}</div>
              <div class="w-full lg:w-1/2">
                {{ token.last_used_at | date }}
              </div>
            </div>
            <div class="list-item flex flex-wrap">
              <div class="w-full lg:w-1/2">{{ __('Created At') }}</div>
              <div class="w-full lg:w-1/2">{{ token.created_at | date }}</div>
            </div>
            <div class="list-item flex flex-wrap">
              <div class="w-full lg:w-1/2">{{ __('Updated At') }}</div>
              <div class="w-full lg:w-1/2">{{ token.updated_at | date }}</div>
            </div>
          </div>
        </div>
      </div>
      <update-access-token
        v-if="token.id"
        :token="token"
        @tokenUpdated="token = $event"
      ></update-access-token>
      <delete-access-token v-if="token.id" :token="token"></delete-access-token>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import UpdateAccessToken from '~/components/access-token/Update'
import DeleteAccessToken from '~/components/access-token/Delete'
import { faSpinner, faTrashAlt } from '@fortawesome/free-solid-svg-icons'
import { faEdit } from '@fortawesome/free-regular-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

library.add([faSpinner, faEdit, faTrashAlt])

export default {
  metaInfo() {
    return { title: this.__('Access Token') }
  },

  components: {
    UpdateAccessToken,
    DeleteAccessToken,
  },

  data: () => ({
    token: {},
    loading: true,
  }),

  mounted() {
    this.getToken()
  },

  methods: {
    getToken() {
      this.loading = true

      axios
        .get(`access-tokens/${this.$route.params.id}`)
        .then((response) => {
          this.loading = false
          this.token = response.data.data
        })
        // eslint-disable-next-line no-unused-vars
        .catch((error) => {
          this.loading = false
        })
    },
  },
}
</script>
