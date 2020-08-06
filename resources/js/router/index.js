import Vue from 'vue'
import Meta from 'vue-meta'
import routes from './routes'
import Router from 'vue-router'

Vue.use(Meta)
Vue.use(Router)

const router = createRouter()

export default router

/**
 * Create a new router instance.
 *
 * @return {Router}
 */
function createRouter() {
  const router = new Router({
    scrollBehavior,
    mode: 'history',
    base: config.dashboard_path,
    routes,
  })

  router.beforeEach(beforeEach)
  router.afterEach(afterEach)

  return router
}

/**
 * Global router guard.
 *
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
async function beforeEach(to, from, next) {
  const components = await resolveComponents(
    router.getMatchedComponents({ ...to })
  )

  if (components.length === 0) {
    return next()
  }

  if (components[components.length - 1].loading !== false) {
    router.app.$nextTick(() => router.app.$loading.start())
  }

  next()
}

/**
 * Global after hook.
 *
 * @param {Route} to
 * @param {Route} from
 * @param {Function} next
 */
async function afterEach(to, from, next) {
  //await router.app.$nextTick()
  router.app.$loading.finish()
}

/**
 * Resolve async components.
 *
 * @param  {Array} components
 * @return {Array}
 */
function resolveComponents(components) {
  return Promise.all(
    components.map((component) => {
      return typeof component === 'function' ? component() : component
    })
  )
}

/**
 * Scroll Behavior
 *
 * @link https://router.vuejs.org/en/advanced/scroll-behavior.html
 *
 * @param  {Route} to
 * @param  {Route} from
 * @param  {Object|undefined} savedPosition
 * @return {Object}
 */
function scrollBehavior(to, from, savedPosition) {
  if (savedPosition) {
    return savedPosition
  }

  if (to.hash) {
    return { selector: to.hash }
  }

  const [component] = router.getMatchedComponents({ ...to }).slice(-1)

  if (component && component.scrollToTop === false) {
    return {}
  }

  return { x: 0, y: 0 }
}
