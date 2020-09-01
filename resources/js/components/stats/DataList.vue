<template>
  <div class="card">
    <div class="list overflow-hidden">
      <div class="list-header w-full self-start">{{ title }}</div>
      <div v-if="loading" class="w-full text-center p-4">
        <font-awesome-icon icon="spinner" class="fa-2x fa-spin" />
      </div>
      <div v-if="!loading" class="flex flex-wrap w-full">
        <div class="list-content w-full">
          <div
            class="list-item flex flex-wrap items-center break-all -mx-2"
            v-for="item in data.data"
            :key="item.label"
          >
            <div class="flex-grow px-2">{{ __(item.label) }}</div>
            <div class="px-2">{{ item.value }}</div>
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
            <span class="px-2">{{ page }}/{{ data.last_page }}</span>
            <button
              @click="nextPage"
              class="btn btn-sm btn-secondary"
              :disabled="page === data.last_page"
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
import {
  faSpinner,
  faChevronLeft,
  faChevronRight,
} from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

library.add([faSpinner, faChevronLeft, faChevronRight])

export default {
  name: 'data-list',

  props: ['url', 'date', 'column', 'title'],

  data: () => ({
    data: {},
    page: 1,
    loading: true,
  }),

  watch: {
    date() {
      if (!this.date || !this.date[0] || !this.date[1]) {
        return
      }

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
        .get(`stats/${this.url.id}/${this.column}`, {
          params: {
            start_date: this.date[0],
            end_date: this.date[1],
            page: this.page,
          },
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
      this.getUrls()
    },

    nextPage() {
      if (this.data.last_page === this.page) {
        return
      }

      this.page = this.page + 1
      this.getData()
    },
  },
}
</script>
