import Vue from 'vue'
import Vuex from 'vuex'
import pathify from '~/plugins/vuex-pathify'

Vue.use(Vuex)

// Load store modules dynamically.
// const requireContext = require.context('./modules', false, /.*\.js$/)

// Modules
import * as modules from './modules'

// const modules = requireContext.keys()
//   .map(file =>
//     [file.replace(/(^.\/)|(\.js$)/g, ''), requireContext(file)]
//   )
//   .reduce((modules, [name, module]) => {
//     if (module.namespaced === undefined) {
//       module.namespaced = true
//     }

//     return { ...modules, [name]: module }
//   }, {})

const store = new Vuex.Store({
  modules,
  plugins: [
    pathify.plugin,
  ],
})

store.subscribe(mutation => {
  if (!mutation.type.startsWith('user/')) return

  store.dispatch('user/update', mutation)
})

store.dispatch('app/init')

export default store

export const ROOT_DISPATCH = Object.freeze({ root: true })
