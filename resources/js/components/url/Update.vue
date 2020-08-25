<template>
  <div>
    <modal name="update-url" :resizable="true" :scrollable="true" height="auto">
      <div class="bg-gray-200 p-4">
        <p class="h4">{{ __('Update URL') }}</p>
      </div>
      <div class="p-4">
        <div class="form-group mb-4">
          <label class="cursor-pointer" for="active">
            <input
              class="mr-1"
              type="checkbox"
              name="active"
              id="active"
              v-model="active"
            />
            <span class="text-sm text-gray-900">{{ __('Active') }}</span>
          </label>
        </div>
        <div class="form-group mb-4">
          <input
            type="url"
            class="form-input w-full"
            :class="valid_url ? 'valid' : 'invalid'"
            name="long_url"
            :placeholder="__('URL')"
            v-model="long_url"
          />
          <span class="error-message" v-if="!valid_url" role="alert">{{
            __('Invalid URL')
          }}</span>
        </div>
        <div class="form-group mb-4">
          <div class="flex flex-wrap bg-gray-200">
            <div class="w-full md:w-1/2 p-2">{{ $config.app_url }}</div>
            <div class="w-full md:w-1/2">
              <input
                type="text"
                class="form-input w-full"
                :class="valid_path ? 'valid' : 'invalid'"
                name="path"
                required
                :placeholder="__('Path')"
                v-model="path"
              />
            </div>
          </div>
          <span class="error-message" v-if="!valid_path" role="alert">{{
            __('Invalid Path')
          }}</span>
        </div>
        <div class="form-group mb-4 text-center">
          <button class="btn btn-primary" @click="this.update">
            {{ __('Update') }}
          </button>
          <button class="btn btn-secondary" @click="$modal.hide('update-url')">
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
  name: 'update-url',

  props: ['url'],

  data() {
    return {
      active: 1,
      long_url: null,
      valid_url: true,
      path: null,
      valid_path: true,
    }
  },

  mounted() {
    this.active = this.url.active
    this.long_url = this.url.long_url
    this.path = this.url.path
  },

  watch: {
    long_url(value) {
      this.validateUrl(value)
    },
  },

  methods: {
    update() {
      axios
        .post('urls/' + this.url.id, {
          active: this.active,
          long_url: this.long_url,
          path: this.path,
        })
        .then((response) => {
          this.$emit('urlUpdated', response.data.data)

          if (this.url.path != this.path) {
            this.$router.push('/urls/' + response.data.data.path)
          }

          this.$modal.hide('update-url')
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
