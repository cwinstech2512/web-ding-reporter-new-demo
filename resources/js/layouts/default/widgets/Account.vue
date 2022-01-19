<template>
  <v-menu
    bottom
    left
    min-width="200"
    offset-y
    origin="top right"
    transition="scale-transition"
  >
    <template v-slot:activator="{ attrs, on }">
      <v-btn
        class="ml-2"
        min-width="0"
        text
        v-bind="attrs"
        v-on="on"
      >
        <v-icon>mdi-account</v-icon>
      </v-btn>
    </template>

    <v-list
      :tile="false"
      flat
      nav
    >
      <template v-for="(p, i) in profile">
        <v-divider
          v-if="p.divider"
          :key="`divider-${i}`"
          class="mb-2 mt-2"
        />

        <app-bar-item
          v-else
          :key="`item-${i}`"
        >
          <v-list-item-title v-text="p.title"  @click="dropdownClick(p.link)" />
        </app-bar-item>
      </template>
    </v-list>
  </v-menu>
</template>

<script>
  export default {
    name: 'DefaultAccount',

    data: () => ({
      profile: [
        { title: '個人中心' },
        { title: '修改密碼' },
        { divider: true },
        { title: '登出', link: 'logout' },
      ],
    }),

    methods: {
      dropdownClick(link) {
        console.log(link)
        switch (link) {
          case 'logout':
            this.logout()
            break;
        
          default:
            break;
        }
      },
      async logout () {
        // Log out the user.
        await this.$store.dispatch('auth/logout')

        // Redirect to login.
        this.$router.push({ name: 'login' })
      },
    }
  }
</script>
