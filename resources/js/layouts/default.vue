<template>
  <v-app>
    <default-bar />

    <default-drawer />

    <default-view />

    <default-footer />

    <!-- <default-settings /> -->
  </v-app>
</template>

<script>
  export default {
    name: 'DefaultLayout',

    components: {
      DefaultBar: () => import(
        /* webpackChunkName: "default-app-bar" */
        './default/AppBar'
      ),
      DefaultDrawer: () => import(
        /* webpackChunkName: "default-drawer" */
        './default/Drawer'
      ),
      DefaultFooter: () => import(
        /* webpackChunkName: "default-footer" */
        './default/Footer'
      ),
      DefaultSettings: () => import(
        /* webpackChunkName: "default-settings" */
        './default/Settings'
      ),
      DefaultView: () => import(
        /* webpackChunkName: "default-view" */
        './default/View'
      ),
    },
  }
</script>
<style>
html {
  font-family: 'DFKai-sb' !important;
}
</style>