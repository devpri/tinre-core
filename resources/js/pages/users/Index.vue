<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card mb-8">
      <div class="list overflow-hidden">
        <div class="list-header flex-wrap justify-between flex -mx-2">
          <div class="w-auto flex-1 lg:w-2/6 px-2">
            <div class="mb-0 form-group">
              <input
                type="text"
                v-model="params.search"
                :placeholder="__('Search')"
                class="form-input w-full"
              />
            </div>
          </div>
          <div class="w-auto lg:w-2/6 text-left md:text-right self-center px-2">
            <button
              v-if="
                users.authorized_actions &&
                users.authorized_actions.includes('create')
              "
              class="btn btn-secondary btn-sm"
              @click="$modal.show('create-user')"
            >
              <font-awesome-icon icon="user-plus" />
            </button>
          </div>
        </div>
        <div v-if="loading" class="w-full text-center p-4">
          <font-awesome-icon icon="spinner" class="fa-2x fa-spin" />
        </div>
        <div v-if="!loading && !users.data.length" class="text-center">
          <p class="p-4 m-0">{{ __('No Data') }}</p>
        </div>
        <div v-if="!loading && users.data.length">
          <div
            class="list-item flex flex-wrap items-center break-all -mx-2"
            v-for="user in users.data"
            :key="user.id"
          >
            <div class="w-auto flex-1 px-2">
              <div
                @click="$router.push('/users/' + user.id)"
                class="text-primary cursor-pointer font-bold"
              >
                {{ user.name }}
              </div>
              <div
                @click="$router.push('/users/' + user.id)"
                class="text-primary cursor-pointer"
              >
                {{ user.email }}
              </div>
            </div>
            <div class="w-auto px-2 py-2 text-left md:text-right">
              <button
                @click="$router.push('/users/' + user.id)"
                class="btn btn-sm btn-secondary"
              >
                <font-awesome-icon :icon="['far', 'eye']" />
              </button>
            </div>
          </div>
        </div>
        <div
          v-if="!loading && users.data.length"
          class="list-footer flex-wrap flex"
        >
          <div class="w-full flex justify-center items-center">
            <button
              @click="prevPage"
              class="btn btn-sm btn-secondary"
              :disabled="params.page === 1"
            >
              <font-awesome-icon icon="chevron-left" />
            </button>
            <span class="px-2"
              >{{ params.page }}/{{ users.meta.last_page }}</span
            >
            <button
              @click="nextPage"
              class="btn btn-sm btn-secondary"
              :disabled="params.page === users.meta.last_page"
            >
              <font-awesome-icon icon="chevron-right" />
            </button>
          </div>
        </div>
      </div>
    </div>
    <create-user
      v-if="
        users.authorized_actions && users.authorized_actions.includes('create')
      "
      @userCreated="getUsers"
    ></create-user>
  </div>
</template>

<script>
import axios from 'axios'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faSpinner,
  faChevronLeft,
  faChevronRight,
  faChartArea,
  faUserPlus,
} from '@fortawesome/free-solid-svg-icons'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import CreateUser from '../../components/user/Create'

library.add([
  faSpinner,
  faChevronLeft,
  faChevronRight,
  faChartArea,
  faEye,
  faUserPlus,
])

export default {
  components: { CreateUser },

  metaInfo() {
    return { title: this.__('Users') }
  },

  data: () => ({
    users: {},
    params: {
      page: 1,
      search: null,
    },
    timer: null,
    loading: true,
  }),

  computed: {
    search() {
      return this.params.search
    },
  },

  watch: {
    search() {
      if (this.timer) {
        clearTimeout(this.timer)
        this.timer = null
      }

      this.timer = setTimeout(() => {
        this.params.page = 1
        this.getUsers()
      }, 500)
    },
  },

  mounted() {
    this.getUsers()
  },

  methods: {
    async getUsers() {
      this.loading = true

      axios
        .get('users', {
          params: this.params,
        })
        .then((response) => {
          this.users = response.data

          this.loading = false
        })
        // eslint-disable-next-line no-unused-vars
        .catch((error) => {
          this.loading = false
        })
    },

    prevPage() {
      if (this.params.page === 1) {
        return
      }

      this.params.page = this.params.page - 1
      this.getUsers()
    },

    nextPage() {
      if (this.urls.meta.last_page === this.params.page) {
        return
      }

      this.params.page = this.params.page + 1
      this.getUsers()
    },
  },
}
</script>
