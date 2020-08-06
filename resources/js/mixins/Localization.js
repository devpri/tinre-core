export default {
  methods: {
    /**
     * Translate the given key.
     */
    __(key, replace) {
      var translation = window.translations[key]
        ? window.translations[key]
        : key

      if (replace) {
        Object.keys(replace).forEach((key) => {
          translation = translation.replace(':' + key, replace[key])
        })
      }

      return translation
    },
  },
}
