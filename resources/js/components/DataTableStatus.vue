<template v-slot:[`item.status`]="{ item }">
  <v-chip
  :color="getStatusColor(item.status)"
  dark
  >
  {{ getStatusText(item.status) }}
  </v-chip>
</template>

<script>
  export default {
    name: 'DataTableStatus',

    props: { item: Object },

    methods: {
      getStatusColor(status) {
      switch (status) {
        case 0:
          return "secondary"
          break;
        case 1:
          return "secondary"
          break;
        case 2:
          return "blue lighten-1"
          break;
        case 3:
          return "orange darken-4"
          break;
        case 4:
          return "green"
          break;
        default:
          return "blue-grey lighten-1"
      }
    },

    getStatusText(status) {
      switch (status) {
        case 0:
          return "派單"
          break;
        case 1:
          return "派單"
          break;
        case 2:
          return "製作報告"
          break;
        case 3:
          return "審核"
          break;
        case 4:
          return "通過"
          break;
        default:
          return "未定義"
      }
    },
    }
  }
</script>
