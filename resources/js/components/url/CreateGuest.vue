<template>
  <div>
    <div class="p-4">
      <div class="flex justify-center">
        <div v-if="!created" class="w-full md:w-1/2 lg:w-1/3 text-left">
          <div class="form-group mb-4">
            <input
              type="url"
              class="form-input w-full"
              :class="!long_url || valid_url ? 'valid' : 'invalid'"
              name="long_url"
              placeholder="URL"
              v-model="long_url"
              v-on:keyup.enter="create"
            />
            <span
              class="error-message"
              v-if="long_url && !valid_url"
              role="alert"
              >{{ __('Invalid URL') }}</span
            >
          </div>
          <div v-if="customPath" class="form-group mb-4">
            <div class="flex flex-wrap bg-gray-200 items-center">
              <div class="w-full md:w-1/2 px-4 py-2">{{ appUrl }}</div>
              <div class="w-full md:w-1/2">
                <input
                  type="text"
                  class="form-input w-full"
                  :class="!path || valid_path ? 'valid' : 'invalid'"
                  name="path"
                  required
                  :placeholder="__('Path')"
                  v-model="path"
                  v-on:keyup.enter="create"
                />
              </div>
            </div>
            <span class="error-message" v-if="path && !valid_path" role="alert">
              {{ __('Invalid Path') }}
            </span>
          </div>
          <div class="form-group mb-4 text-center">
            <button class="btn btn-primary" @click="create">
              {{ __('Shorten') }}
            </button>
          </div>
        </div>
        <div v-if="created" class="w-full md:w-1/2 lg:w-1/3 text-left">
          <div class="flex flex-wrap bg-gray-200">
            <div class="w-full p-2 flex items-center justify-between">
              <div class="flex-1">
                <input
                  id="short-url"
                  class="w-full bg-gray-200 py-2"
                  type="text"
                  :value="appUrl + path"
                  readonly
                />
              </div>
              <div>
                <button class="btn btn-secondary btn-sm py-2" @click="copy">
                  <font-awesome-icon :icon="['far', 'copy']" />
                </button>
                <button class="btn btn-secondary btn-sm py-2" @click="clear">
                  <font-awesome-icon :icon="['fas', 'times']" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { faCopy } from '@fortawesome/free-regular-svg-icons'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

library.add([faCopy, faTimes])

export default {
  name: 'create-url-guest',

  props: ['appUrl', 'customPath'],

  data: () => ({
    long_url: null,
    valid_url: true,
    path: null,
    valid_path: true,
    created: false,
  }),

  watch: {
    long_url(value) {
      this.validateUrl(value)
    },
  },

  methods: {
    create() {
      if (!this.long_url || !this.valid_url || !this.valid_path) {
        return
      }

      let url = this.long_url

      if (!/^(?:f|ht)tps?\:\/\//.test(url)) {
        url = 'http://' + url
      }

      axios
        .post('urls', {
          long_url: url,
          path: this.path,
        })
        .then((response) => {
          this.path = response.data.data.path
          this.created = true
        })
    },

    copy() {
      let copyTextarea = document.getElementById('short-url')
      copyTextarea.focus()
      copyTextarea.select()

      document.execCommand('copy')
    },

    clear() {
      this.long_url = null
      this.path = null
      this.created = false
    },

    validateUrl(value) {
      const pattern = new RegExp(
        '^(https?:\\/\\/)?' +
          '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' +
          '((\\d{1,3}\\.){3}\\d{1,3}))' +
          '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' +
          '(\\?[;&a-z\\d%_.~+=-]*)?' +
          '(\\#[-a-z\\d_]*)?$',
        'i'
      )

      if (pattern.test(value)) {
        return (this.valid_url = true)
      }

      this.valid_url = false
    },
  },
}
</script>
