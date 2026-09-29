<template>
  <div id="app">
    <div v-if="frontendDemo" class="frontend-demo-badge">FRONTEND DEMO · LOCAL DATA</div>
    <loading ref="loading" />
    <v-fade-transition name="page" mode="out-in">
      <router-view :is="layout" v-if="layout"/>
    </v-fade-transition>
  </div>
</template>

<script>
import Loading from './Loading'

// Styles
import '~/styles/overrides.sass'
// Load layout components dynamically.
const requireContext = require.context('~/layouts', false, /.*\.vue$/)

const layouts = requireContext.keys()
  .map(file =>
    [file.replace(/(^.\/)|(\.vue$)/g, ''), requireContext(file)]
  )
  .reduce((components, [name, component]) => {
    components[name] = component.default || component
    return components
  }, {})

export default {
  el: '#app',

  components: {
    Loading
  },

  data: () => ({
    layout: null,
    defaultLayout: 'default',
    frontendDemo: process.env.MIX_FRONTEND_DEMO === 'true'
  }),

  metaInfo () {
    const { appName } = window.config

    return {
      title: appName,
      titleTemplate: `%s · ${appName}`,
      htmlAttrs: { lang: 'zh-TW' },
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
    }
  },

  mounted () {
    this.$loading = this.$refs.loading
  },

  methods: {
    /**
     * Set the application layout.
     *
     * @param {String} layout
     */
    setLayout (layout) {
      if (!layout || !layouts[layout]) {
        layout = this.defaultLayout
      }

      this.layout = layouts[layout]
    }
  }
}
</script>
<style>
  .frontend-demo-badge {
    position: fixed;
    right: 16px;
    bottom: 16px;
    z-index: 9999;
    padding: 8px 12px;
    border-radius: 6px;
    background: #ffc107;
    color: #202020;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    box-shadow: 0 4px 14px rgba(0, 0, 0, .24);
  }
</style>
