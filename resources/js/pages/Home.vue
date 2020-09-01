<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card mb-8">
      <div class="list overflow-hidden">
        <div class="list-header flex-wrap justify-between flex -mx-2">
          <div class="w-auto flex-1 mx-2">
            <div class="mb-0 lg:w-64 form-group">
              <input
                type="text"
                v-model="params.search"
                :placeholder="__('Search')"
                class="form-input w-full"
              />
            </div>
          </div>
          <div class="w-auto mx-2 self-center">
            <v-popover
              ref="menu"
              placement="bottom-start"
              :autoHide="autoHideFilter"
            >
              <button class="pointer btn btn-sm btn-secondary">
                <font-awesome-icon :icon="['fas', 'filter']" />
              </button>
              <template class="hidden" slot="popover">
                <div class="card p-4 w-64">
                  <div class="mb-2">
                    <p class="mb-1">{{ __('Date') }}</p>
                    <date-picker
                      type="datetime"
                      v-model="date"
                      value-type="format"
                      :placeholder="__('Date')"
                      :shortcuts="shortcuts"
                      @open="openDatePicker"
                      @close="closeDatePicker"
                      range
                    ></date-picker>
                  </div>
                  <div class="form-group mb-2">
                    <p class="mb-1">{{ __('Active') }}</p>
                    <select v-model="params.active" class="form-input w-full">
                      <option :value="null">-</option>
                      <option :value="1">{{ __('Yes') }}</option>
                      <option :value="0">{{ __('No') }}</option>
                    </select>
                  </div>
                  <div class="form-group mb-2">
                    <p class="mb-1">{{ __('Per Page') }}</p>
                    <select v-model="params.limit" class="form-input w-full">
                      <option :value="25">25</option>
                      <option :value="50">50</option>
                      <option :value="100">100</option>
                    </select>
                  </div>
                  <div class="form-group mb-2">
                    <p class="mb-1">{{ __('Sort By') }}</p>
                    <select v-model="params.sort_by" class="form-input w-full">
                      <option value="created_at">{{ __('Created At') }}</option>
                      <option value="updated_at">{{ __('Updated At') }}</option>
                      <option value="total_clicks">{{ __('Clicks') }}</option>
                    </select>
                  </div>
                  <div class="form-group mb-2">
                    <p class="mb-1">{{ __('Sort Direction') }}</p>
                    <select
                      v-model="params.sort_direction"
                      class="form-input w-full"
                    >
                      <option value="asc">{{ __('ASC') }}</option>
                      <option value="desc">{{ __('DESC') }}</option>
                    </select>
                  </div>
                </div>
              </template>
            </v-popover>
          </div>
        </div>
        <div v-if="loading" class="w-full text-center p-4">
          <font-awesome-icon icon="spinner" class="fa-2x fa-spin" />
        </div>
        <div v-if="!loading">
          <div
            class="list-item flex flex-wrap items-center break-all -mx-2"
            v-for="url in urls.data"
            :key="url.id"
          >
            <div class="w-full lg:w-2/3 px-2">
              <div class="text-xs">
                {{ url.created_at | date }}
                <span
                  v-if="url.user && urls.authorized_actions.includes('viewAny')"
                  >by {{ url.user.name }}</span
                >
              </div>
              <div
                @click="$router.push('/urls/' + url.path)"
                class="text-primary cursor-pointer font-bold"
              >
                {{ $config.app_url }}{{ url.path }}
              </div>
              <div
                @click="$router.push('/urls/' + url.path)"
                class="text-sm cursor-pointer"
              >
                {{ url.long_url }}
              </div>
            </div>
            <div class="w-full lg:w-1/3 px-2 py-2 text-left md:text-right">
              <button
                @click="$router.push('/urls/' + url.path)"
                class="btn btn-sm btn-secondary"
              >
                <font-awesome-icon icon="chart-area" />
                {{ url.total_clicks }}
              </button>
              <button
                @click="$router.push('/urls/' + url.path)"
                class="btn btn-sm btn-secondary"
              >
                <font-awesome-icon :icon="['far', 'eye']" />
              </button>
            </div>
          </div>
        </div>
        <div v-if="!loading && !urls.data.length" class="text-center">
          <p class="p-4 m-0">{{ __('No Data') }}</p>
        </div>
        <div
          v-if="!loading && urls.data.length"
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
              >{{ params.page }}/{{ urls.meta.last_page }}</span
            >
            <button
              @click="nextPage"
              class="btn btn-sm btn-secondary"
              :disabled="params.page === urls.meta.last_page"
            >
              <font-awesome-icon icon="chevron-right" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import DatePicker from 'vue2-datepicker'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faSpinner,
  faChevronLeft,
  faChevronRight,
  faChartArea,
  faFilter,
} from '@fortawesome/free-solid-svg-icons'
import { faEye } from '@fortawesome/free-regular-svg-icons'
import timeShortcuts from '../mixins/TimeShortcuts'

library.add([
  faSpinner,
  faChevronLeft,
  faChevronRight,
  faChartArea,
  faEye,
  faFilter,
])

export default {
  components: {
    DatePicker,
  },

  metaInfo() {
    return { title: this.__('Dashboard') }
  },

  data: () => ({
    selected: [],
    urls: {},
    params: {
      page: 1,
      search: null,
      start_date: null,
      end_date: null,
      active: null,
      sort_by: 'created_at',
      sort_direction: 'desc',
      limit: 25,
    },
    date: null,
    timer: null,
    loading: true,
    autoHideFilter: true,
    shortcuts: timeShortcuts,
  }),

  computed: {
    filter() {
      return `${this.date}|${this.params.active}|${this.params.limit}`
    },

    sort() {
      return `${this.params.active}|${this.params.sort_by}|${this.params.sort_direction}`
    },

    search() {
      return this.params.search
    },
  },

  watch: {
    filter() {
      this.params.page = 1

      if (this.date && this.date[0] && this.date[1]) {
        this.params.start_date = this.date[0]
        this.params.end_date = this.date[1]
      } else {
        this.params.start_date = null
        this.params.end_date = null
      }

      this.getUrls()
    },

    sort() {
      this.getUrls()
    },

    search() {
      if (this.timer) {
        clearTimeout(this.timer)
        this.timer = null
      }

      this.timer = setTimeout(() => {
        this.params.page = 1
        this.getUrls()
      }, 500)
    },
  },

  mounted() {
    this.getUrls()
  },

  methods: {
    getUrls() {
      this.loading = true

      axios
        .get('urls', {
          params: this.params,
        })
        .then((response) => {
          this.loading = false
          this.urls = response.data
        })
        .catch((error) => {
          this.loading = false
        })
    },

    prevPage() {
      if (this.params.page === 1) {
        return
      }

      this.params.page = this.params.page - 1
      this.getUrls()
      window.scrollTo(0, 0)
    },

    nextPage() {
      if (this.urls.meta.last_page === this.params.page) {
        return
      }

      this.params.page = this.params.page + 1
      this.getUrls()
      window.scrollTo(0, 0)
    },

    openDatePicker() {
      this.autoHideFilter = false
    },

    closeDatePicker() {
      setTimeout(() => {
        this.autoHideFilter = true
      }, 200)
    },
  },
}
</script>
