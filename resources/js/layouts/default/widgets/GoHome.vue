<template>
  <v-tooltip bottom>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        class="ml-2"
        min-width="0"
        text
        :to="{path: '/dashboard'}"
        exact
        v-bind="attrs"
        v-on="on"
      >
        <v-icon>mdi-view-dashboard</v-icon>
      </v-btn>
    </template>
    <span>首頁</span>
  </v-tooltip>
</template>

<script>
  export default {
    name: 'DefaultGoHome',
  }
</script>
