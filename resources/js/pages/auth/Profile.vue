<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      :title="$t('profile')"
      color="accent"
    >
    <v-container>
      <form>
        <v-text-field
          v-model="account"
          prepend-icon="mdi-account"
          label="個人帳號"
          outlined
          readonly
        ></v-text-field>
        <v-text-field
          v-model="role_name"
          prepend-icon="mdi-eye"
          label="使用權限"
          outlined
          readonly
        ></v-text-field>
      </form>
    </v-container>

    </material-card>

  </v-container>
</template>
<script>
  // Utilities
  import { get } from 'vuex-pathify'
  import Vue from 'vue'
  import { mapGetters } from 'vuex'

  export default {
    name: 'ProdfileView',

    middleware: 'auth',

    metaInfo () {
      return { title: this.$t('profile') }
    },

    created () {
      this.initialize()
    },

    computed: {
      ...mapGetters({
        user: 'auth/user'
      }),
    },

    data: () => ({
      show1: false,
      old_password: null,
      new_password: null,
      renew_password: null,
      account: "",
      role_name: "",
      roleItems: [],
    }),
    methods: {
      initialize () {
        this.account = this.user? this.user.email.split("@")[0] : "123"
        this.getRoles()
      },

      getRoles () {
        this.loading = true
        let that = this
        let url = 'api/roles'
        let params = {}
        this.$axios
          .fetchGet(url, params)
            .then(res => {
              console.log(res.data)
              console.log(that.user)
              this.roleItems = res.data.roles.map((_, idx) => ({
                  value: res.data.roles[idx].id,
                  text: res.data.roles[idx].slug
                }))
              this.role_name = this.roleItems.find(function (role) {
                return role.value === that.user.role
              }).text
            })
            .catch(err => {
              console.log(err)
            })
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