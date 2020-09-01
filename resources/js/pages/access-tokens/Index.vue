<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card">
      <div class="list overflow-hidden">
        <div class="list-header w-full self-start">
          <div class="flex flex-wrap -mx-2">
            <div class="w-auto flex-1 px-2 self-center">
              {{ __('Access Tokens') }}
            </div>
            <div class="w-auto px-2">
              <button
                v-if="
                  data.authorized_actions &&
                  data.authorized_actions.includes('create')
                "
                class="btn btn-secondary btn-sm py-2"
                @click="$modal.show('create-access-token')"
              >
                <font-awesome-icon icon="plus" />
              </button>
            </div>
          </div>
        </div>
        <div v-if="loading" class="w-full text-center p-4">
          <font-awesome-icon icon="spinner" class="fa-2x fa-spin" />
        </div>
        <div v-if="!loading" class="flex flex-wrap w-full">
          <div class="list-content w-full">
            <div
              class="list-item flex flex-wrap items-center break-all -mx-2"
              v-for="item in data.data"
              :key="item.id"
            >
              <div class="flex-grow px-2">
                <p>
                  <span
                    class="cursor-pointer font-bold"
                    @click="$router.push(`/access-tokens/${item.id}`)"
                  >
                    {{ __(item.name) }}</span
                  >
                  <a
                    v-if="
                      item.user && item.user.authorized_actions.includes('view')
                    "
                    class="text-sm cursor-pointer"
                    @click="
                      $router.push({
                        name: 'users.show',
                        params: { id: item.user.id },
                      })
                    "
                  >
                    {{ __('by ') }} {{ item.user.name }}
                  </a>
                  <span class="text-sm" v-else-if="item.user"
                    >{{ __('by ') }} {{ item.user.name }}
                  </span>
                </p>
                <p class="text-sm">
                  {{ __('Created At: ') }}{{ item.created_at | date }}
                </p>
                <p class="text-sm">
                  {{ __('Last Used At: ') }}{{ item.last_used_at | date }}
                </p>
              </div>
              <div class="p-2">
                <button
                  @click="$router.push(`/access-tokens/${item.id}`)"
                  class="btn btn-sm btn-secondary"
                >
                  <font-awesome-icon :icon="['far', 'eye']" />
                </button>
              </div>
            </div>
          </div>
          <div v-if="!data.data.length" class="text-center">
            <p class="p-4 m-0">{{ __('No Data') }}</p>
          </div>
          <div v-if="data.data.length" class="list-footer self-end w-full">
            <div class="w-full flex justify-center items-center">
              <button
                @click="prevPage"
                class="btn btn-sm btn-secondary"
                :disabled="page === 1"
              >
                <font-awesome-icon icon="chevron-left" />
              </button>
              <span class="px-2">{{ page }}/{{ data.meta.last_page }}</span>
              <button
                @click="nextPage"
                class="btn btn-sm btn-secondary"
                :disabled="page === data.meta.last_page"
              >
                <font-awesome-icon icon="chevron-right" />
              </button>
            </div>
          </div>
        </div>
      </div>
      <create-access-token></create-access-token>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import CreateAccessToken from '~/components/access-token/Create'
import {
  faSpinner,
  faChevronLeft,
  faChevronRight,
  faPlus,
} from '@fortawesome/free-solid-svg-icons'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

library.add([faSpinner, faChevronLeft, faChevronRight, faPlus, faEye])

export default {
  metaInfo() {
    return { title: this.__('Access Tokens') }
  },

  components: {
    CreateAccessToken,
  },

  data: () => ({
    data: {},
    page: 1,
    loading: true,
  }),

  watch: {
    date() {
      this.page = 1

      this.getData()
    },

    page() {
      this.getData()
    },
  },

  mounted() {
    this.getData()
  },

  methods: {
    async getData() {
      this.loading = true

      axios
        .get('access-tokens', {
          params: { page: this.page },
        })
        .then((response) => {
          this.data = response.data

          this.loading = false
        })
        // eslint-disable-next-line no-unused-vars
        .catch((error) => {
          this.loading = false
        })
    },

    prevPage() {
      if (this.page === 1) {
        return
      }

      this.page = this.page - 1
      this.getData()
    },

    nextPage() {
      if (this.data.meta.last_page === this.page) {
        return
      }

      this.page = this.page + 1
      this.getData()
    },
  },
}
</script>
