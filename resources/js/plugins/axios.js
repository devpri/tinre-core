import axios from 'axios'
import Noty from 'noty'

Noty.overrideDefaults({
  timeout: 5000,
})

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

axios.defaults.baseURL = '/web/'

axios.interceptors.request.use((request) => {
  const token = document.head.querySelector('meta[name="csrf-token"]')
  request.headers.common['X-CSRF-TOKEN'] = token.content
  return request
})

axios.interceptors.response.use(
  function (response) {
    if (response.data.message) {
      new Noty({
        type: 'success',
        text: response.data.message,
      }).show()
    }

    return response
  },
  function (error) {
    if (error.response.status === 401 || error.response.status === 404) {
      window.location =
        window.location.protocol + '//' + window.location.hostname
    }

    let errorMessage = error.response.data.message
    if (error.response.data.errors) {
      const errors = Object.values(error.response.data.errors)
      errors.forEach((element) => {
        element.forEach((message) => {
          errorMessage += '<br>' + message
        })
      })
    }

    new Noty({
      type: 'error',
      text: errorMessage,
    }).show()

    return Promise.reject(error)
  }
)
