<template>
  <v-list-item class="mb-0 justify-space-between pl-3" link :to="{ name: '統計分析' }">
    <v-list-item-avatar>
      <v-img
        :src="$base_url + '/assets/images/x_m_logo.png'"
      />
    </v-list-item-avatar>

    <v-list-item-content class="pl-2">
      <v-list-item-title class="text-h4">
        <strong class="mr-1 font-weight-black">祥鼎</strong>
      </v-list-item-title>
    </v-list-item-content>
  </v-list-item>
</template>

<script>
  // Utilities
  import { get } from 'vuex-pathify'

  export default {
    name: 'DefaultDrawerHeader',

    computed: { version: get('app/version') },
  }
</script>
