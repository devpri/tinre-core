<template>
  <div>
    <div class="p-4">
      <div class="flex justify-center">
        <div v-if="!created" class="w-full lg:w-2/3 text-left">
          <div class="create-guest flex flex-wrap shadow-lg">
            <div
              class="url w-full"
              :class="customPath ? 'md:w-1/2' : 'md:w-auto flex-1'"
            >
              <input
                type="url"
                class="form-input w-full p-4 border rounded-none border-white"
                :class="
                  !long_url || valid_url ? 'valid' : 'invalid border-red-500'
                "
                name="long_url"
                :placeholder="__('URL')"
                v-model="long_url"
                v-on:keyup.enter="create"
              />
            </div>
            <div v-if="customPath" class="path w-full md:w-auto flex-1">
              <input
                type="text"
                class="form-input w-full p-4 border rounded-none border-white"
                :class="
                  !path || valid_path ? 'valid' : 'invalid border-red-500'
                "
                name="path"
                required
                :placeholder="__('Path')"
                v-model="path"
                v-on:keyup.enter="create"
              />
            </div>
            <div class="w-full md:w-auto">
              <button
                class="btn btn-primary w-full lg:w-auto h-full"
                @click="create"
              >
                {{ __('Shorten') }}
              </button>
            </div>
          </div>
        </div>
        <div v-if="created" class="w-full lg:w-2/3 text-left">
          <div
            class="flex flex-wrap rounded shadow-lg overflow-hidden bg-white"
          >
            <div class="w-full flex items-center justify-between">
              <div class="flex-1">
                <input
                  id="short-url"
                  class="w-full outline-none p-4"
                  type="text"
                  :value="appUrl + path"
                  readonly
                />
              </div>
              <div class="px-4">
                <a
                  class="btn btn-secondary btn-sm py-2 inline-block w-8"
                  v-if="urlPreview"
                  :href="appUrl + path + urlPreviewSuffix"
                  target="_blank"
                >
                  <font-awesome-icon :icon="['far', 'eye']" />
                </a>
                <button
                  class="btn btn-secondary btn-sm py-2 w-8"
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
                  class="btn btn-secondary btn-sm py-2 w-8"
                  @click="clear"
                >
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
import { faCopy, faEye } from '@fortawesome/free-regular-svg-icons'
import { faTimes } from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'

library.add([faCopy, faTimes, faEye])

export default {
  name: 'create-url-guest',

  props: ['appUrl', 'customPath', 'urlPreview', 'urlPreviewSuffix'],

  data: () => ({
    long_url: null,
    valid_url: true,
    path: null,
    valid_path: true,
    created: false,
    copyTooltip: false,
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

      this.copyTooltip = true

      setTimeout(() => {
        this.copyTooltip = false
      }, 600)
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
