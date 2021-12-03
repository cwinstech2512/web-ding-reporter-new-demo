<template>
<v-footer
    app
    dark
    padless
  >
    <v-card
      class="flex"
      flat
      tile
    >
      <v-card-text class="py-2 white--text text-center">
        Copyright &copy;{{ new Date().getFullYear() }} EHS — <strong>高雄研發中心</strong>
      </v-card-text>
    </v-card>
</v-footer>
</template>

<script>

export default {
  components: {
  },

  props: {
  },

  data: () => ({
  }),

  methods: {
  }
}
</script>