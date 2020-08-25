<template>
  <div class="content w-auto flex-1 px-4">
    <div class="card mb-8">
      <div v-if="!loading" class="card-header p-4">
        <div class="flex flex-wrap items-center">
          <div class="w-full md:w-2/3">
            <h1 class="text-xl break-all">
              {{ $config.app_url }}{{ url.path }}
            </h1>
            <span class="text-sm break-all">{{ url.long_url }}</span>
          </div>
          <div class="w-full md:w-1/3 text-left md:text-right">
            <span
              class="py-1 px-2 rounded border-0 bg-white"
              :class="url.active ? 'bg-white' : 'bg-red-200'"
              >{{ url.active ? __('Active') : __('Disabled') }}</span
            >
            <button
              class="btn btn-secondary btn-sm py-2"
              v-tooltip="{
                content: __('Copied!'),
                trigger: 'manual',
                show: copyTooltip,
              }"
              @click="copy"
            >
              <font-awesome-icon :icon="['far', 'copy']" />
            </button>
            <button
              v-if="
                url.authorized_actions &&
                url.authorized_actions.includes('update')
              "
              class="btn btn-secondary btn-sm my-2"
              @click="$modal.show('update-url')"
            >
              <font-awesome-icon :icon="['far', 'edit']" />
            </button>
            <button
              v-if="
                url.authorized_actions &&
                url.authorized_actions.includes('delete')
              "
              class="btn btn-danger btn-sm my-2"
              @click="$modal.show('delete-url')"
            >
              <font-awesome-icon icon="trash-alt" />
            </button>
          </div>
        </div>
      </div>
      <div class="card-content p-4">
        <div v-if="!loading" class="flex justify-center">
          <div class="w-full lg:w-1/2">
            <date-picker
              type="datetime"
              v-model="date"
              value-type="format"
              :placeholder="__('Date')"
              :shortcuts="shortcuts"
              range
            ></date-picker>
          </div>
        </div>
        <div v-if="loading" class="w-full text-center p-4">
          <font-awesome-icon icon="spinner" class="fa-2x fa-spin" />
        </div>
      </div>
    </div>
    <div class="flex flex-wrap -m-4">
      <div v-if="url.id" class="w-full p-4">
        <div class="card">
          <div class="list flex flex-wrap overflow-hidden">
            <div class="list-header w-full">Clicks</div>
            <div class="p-4 w-full">
              <clicks-chart :date="date" :url="url"></clicks-chart>
            </div>
          </div>
        </div>
      </div>
      <div v-if="url.id" class="w-full lg:w-1/3 p-4">
        <data-list
          :title="__('Top Countries')"
          column="country"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full lg:w-1/3 p-4">
        <data-list
          :title="__('Top Regions')"
          column="region"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full lg:w-1/3 p-4">
        <data-list
          :title="__('Top Cities')"
          column="city"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full lg:w-1/3 p-4">
        <data-list
          :title="__('Top Device Types')"
          column="device_type"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full p-4 lg:w-1/3">
        <data-list
          :title="__('Top Device Brands')"
          column="device_brand"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full p-4 lg:w-1/3">
        <data-list
          :title="__('Top Device Models')"
          column="device_model"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full lg:w-1/2 p-4">
        <data-list
          :title="__('Top Operating Systems')"
          column="os"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full p-4 lg:w-1/2">
        <data-list
          :title="__('Top Browsers')"
          column="browser"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full p-4 lg:w-1/2">
        <data-list
          :title="__('Top Referer Domains')"
          column="referer_host"
          :date="date"
          :url="url"
        ></data-list>
      </div>
      <div v-if="url.id" class="w-full p-4 lg:w-1/2">
        <data-list
          :title="__('Top Referer Pages')"
          column="referer"
          :date="date"
          :url="url"
        ></data-list>
      </div>
    </div>
    <update-url
      v-if="url.id"
      :url="url"
      @urlUpdated="url = $event"
    ></update-url>
    <delete-url v-if="url.id" :url="url"></delete-url>
  </div>
</template>

<script>
import axios from 'axios'
import * as moment from 'moment/moment'
import DatePicker from 'vue2-datepicker'
import UpdateUrl from '~/components/url/Update'
import DeleteUrl from '~/components/url/Delete'
import ClicksChart from '~/components/stats/ClicksChart'
import DataList from '~/components/stats/DataList'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrashAlt, faSpinner } from '@fortawesome/free-solid-svg-icons'
import { faEdit, faCopy } from '@fortawesome/free-regular-svg-icons'
import timeShortcuts from '../mixins/TimeShortcuts'

library.add([faEdit, faTrashAlt, faSpinner, faCopy])

export default {
  components: {
    DatePicker,
    UpdateUrl,
    DeleteUrl,
    ClicksChart,
    DataList,
  },

  metaInfo() {
    return { title: this.__('URL Details') }
  },

  name: 'Url',

  data: () => ({
    url: {},
    loading: false,
    copyTooltip: false,
    date: [
      moment().startOf('day').format('YYYY-MM-DD HH:mm:ss'),
      moment().endOf('day').format('YYYY-MM-DD HH:mm:ss'),
    ],
    shortcuts: timeShortcuts,
  }),

  mounted() {
    this.getUrl()
  },

  methods: {
    getUrl() {
      this.loading = true

      axios
        .get('urls/' + this.$route.params.path)
        .then((response) => {
          this.loading = false
          this.url = response.data.data
        })
        .catch((error) => {
          this.loading = false
        })
    },

    copy() {
      let text = document.createElement('textarea')
      document.body.appendChild(text)

      text.value = this.$config.app_url + this.url.path
      text.select()

      document.execCommand('copy')
      document.body.removeChild(text)

      this.copyTooltip = true

      setTimeout(() => {
        this.copyTooltip = false
      }, 600)
    },
  },
}
</script>
