<template>
<!-- BEGIN: Main Menu-->
  <v-navigation-drawer
    app
    v-model="drawer"
    :mini-variant.sync="mini"
    permanent
    fixed
  >
    <v-list>
      <v-list-item class="px-2">
        <v-list-item-avatar>
          <img class="brand-logo" alt="ehsn admin logo" :src="$base_url + '/assets/images/etmall_logo.png'">
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title class="text-h6">
            <a class="navbar-brand" style="display: flex; color: #404E67; font-weight: 500;" href="./"><h2>ETMall</h2> </a>
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <v-divider></v-divider>
    <v-list 
      dense
      class="navigation navigation-main"
      style="overflow-x: hidden;"
    >
      <v-subheader class="text-uppercase" inset>General</v-subheader>
      <v-list-group
        prepend-icon="fa fa-user-o"
        no-action
        append-icon="feather icon-chevron-down"
        v-if="routes.account.length > 0 && authMenuGroup(routes.account)"
      >
        <template v-slot:activator>
          <v-list-item-title>帳號管理</v-list-item-title>
        </template>
        <v-list-item
          v-if="authMenuItem(item.path)"
          link
          class="nav-item"
          v-for="(item, key) in routes.account"
          :to="{ name: item.name }"
          :key="item.path"
        >
          <v-list-item-content>
            <v-list-item-title >
              {{ item.name }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>
      <v-list-group
        prepend-icon="fa fa-hospital-o"
        no-action
        append-icon="feather icon-chevron-down"
        v-if="routes.manager.length > 0 && authMenuGroup(routes.manager)"
      >
        <template v-slot:activator>
          <v-list-item-title>常見問題</v-list-item-title>
        </template>
        <v-list-item
          v-if="authMenuItem(item.path)"
          link
          class="nav-item"
          v-for="(item, key) in routes.manager"
          :to="{ name: item.name }"
          :key="item.path"
        >
          <v-list-item-content>
            <v-list-item-title >
              {{ item.name }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>
      <v-list-group
        prepend-icon="feather icon-message-square"
        no-action
        append-icon="feather icon-chevron-down"
        style="margin-top: 5px;"
        v-if="routes.chat.length > 0 && authMenuGroup(routes.chat)"
      >
        <template v-slot:activator>
          <v-spacer></v-spacer>
          <v-list-item-title >聊天紀錄</v-list-item-title>
        </template>
        <v-list-item
          v-if="authMenuItem(item.path)"
          link
          class="nav-item"
          v-for="(item, key) in routes.chat"
          :to="{ name: item.name }"
          :key="item.path"
        >
          <v-list-item-content>
            <v-list-item-title class="menu-title">{{ item.name }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-group>
      
    </v-list>
  </v-navigation-drawer>
</template>

<script>

import { mapGetters } from 'vuex'

export default {
  components: {
  },

  props: {
    mini: {
      type: Boolean,
      required: true
    }
  },

  data: () => ({
    appName: window.config.appName,
    drawer: true,
    items: [
          { title: 'Home', icon: 'mdi-home-city' },
          { title: 'My Account', icon: 'mdi-account' },
          { title: 'Users', icon: 'mdi-account-group-outline' },
        ],
    routes: {
      index: [
        {
          path: 'dashboard',
          name: '統計分析'
        },
      ],
      account: [
        {
          path: 'account',
          name: '帳戶管理'
        },
      ],
      manager: [
        {
          path: 'manager',
          name: '管理常見問題'
        },
      ],
      chat: [
        {
          path: 'agent',
          name: '客服中心'
        },
        {
          path: 'history',
          name: '歷史對話紀錄'
        },
      ]
    }
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    },
    authMenuGroup (keys) {
      let isShow = false
      Object.values(keys).map(item => item.path).every(value=>{
        if(this.authMenuItem(value)) {
          isShow = true
          return false
        }
      })
      return isShow
    },
    authMenuItem (key) {
      return this.user.menu.menus.find(e => Object.values(e.menu_info).find((obj) => {return obj == key}))
    }
  }
}
</script>