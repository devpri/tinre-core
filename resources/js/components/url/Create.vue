<template>
  <div>
    <modal name="create" :resizable="true" :scrollable="true" height="auto">
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Shorten an URL') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group mb-4">
          <input
            type="url"
            class="form-input w-full"
            :class="!long_url || valid_url ? 'valid' : 'invalid'"
            name="long_url"
            :placeholder="__('URL')"
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
        <div class="form-group mb-4">
          <div class="flex flex-wrap bg-gray-200 items-center">
            <div class="w-full md:w-1/2 px-4 py-2">{{ $config.app_url }}</div>
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
          <button class="btn btn-secondary" @click="$modal.hide('create')">
            {{ __('Cancel') }}
          </button>
        </div>
      </div>
    </modal>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'create-url',

  data: () => ({
    long_url: null,
    valid_url: true,
    path: null,
    valid_path: true,
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
          this.long_url = null
          this.path = null

          this.$modal.hide('create')

          this.$router.push('/urls/' + response.data.data.path)
        })
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
