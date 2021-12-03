<template>
<!-- BEGIN: Header-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-semi-light bg-gradient-x-grey-blue">
    <div class="navbar-wrapper">
        <div class="navbar-container content" style="margin-left: 0 !important;">
            <div class="collapse navbar-collapse" id="navbar-mobile">
                <ul class="nav navbar-nav mr-auto float-left">
                    <li class="nav-item d-none d-md-block">
                      <v-btn
                        class="nav-link nav-menu-main menu-toggle hidden-xs"
                        icon
                        @click.stop="$emit('update:mini', !mini);"
                        color="#FFFFFF"
                      >
                        <i class="feather icon-menu"></i>
                      </v-btn>
                    </li>
                </ul>
                <ul class="nav navbar-nav float-right">
                    <li id="main-header-notify" class="dropdown dropdown-notification nav-item">
                      <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                        <v-menu
                          offset-y
                        >
                          <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                color="white"
                                text
                                icon
                                max-width="20px"
                                max-height="20px"
                                v-bind="attrs"
                                v-on="on"
                                depressed
                                @click="clearHeaderNotify"
                              >
                                <v-badge
                                  color="error"
                                  :content="notifyLength"
                                  :value="notifyLength"
                                  transition="scale-rotate-transition"
                                  overlap
                                >
                                <v-icon>
                                  feather icon-bell
                                </v-icon>
                                </v-badge>
                              </v-btn>

                          </template>
                          <v-list-item-group
                            color="primary"
                          >
                            <v-list dense ref="notify_list">
                              <template v-for="(item, key) in guestList">
                                <v-list-item v-if="item.guest_list[0].status == 'notify'" :to="{ name: '客服中心' }" :key="key">
                                  <v-list-item-content>您有新客戶( {{item.guest_list[0].client_name}} )進線</v-list-item-content>
                                  <v-list-item-action>
                                    <v-list-item-action-text>{{momentSince(item.guest_list[0].time)}}</v-list-item-action-text>
                                  </v-list-item-action>
                                </v-list-item>
                              </template>
                            </v-list>
                          </v-list-item-group>
                        </v-menu>
                      </a>
                    </li>
                    <li class="dropdown dropdown-notification nav-item">
                      <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                        <v-menu
                          offset-y
                        >
                          <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                color="white"
                                text
                                icon
                                max-width="20px"
                                max-height="20px"
                                v-bind="attrs"
                                v-on="on"
                                depressed
                              >
                                <v-badge
                                  color="error"
                                  :content="0"
                                  :value="0"
                                  overlap
                                >
                                <v-icon>
                                  feather icon-mail
                                </v-icon>
                                </v-badge>
                              </v-btn>

                          </template>

                          <v-list>
                            <v-list-item>
                              <v-list-item-title>Logout</v-list-item-title>
                            </v-list-item>
                          </v-list>
                        </v-menu>
                      </a>
                    </li>
                    <li class="dropdown dropdown-user nav-item">
                      <a class="nav-link dropdown-user-link text-center">
                        <div class="avatar avatar-online">
                          <img src="plugins/app-assets/images/portrait/small/avatar-s-1.png" alt="avatar">
                          <i></i>
                        </div>
                        <v-menu
                          offset-y
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              color="white"
                              plain
                              v-bind="attrs"
                              v-on="on"
                            >
                              {{user? user.name : ''}}
                              <v-icon right>
                                feather icon-chevron-down
                              </v-icon>
                            </v-btn>
                          </template>

                          <v-list-item-group
                            color="primary"
                          >
                            <v-list dense>
                              <v-list-item>
                                <v-list-item-content @click="logout">Logout</v-list-item-content>
                              </v-list-item>
                            </v-list>
                          </v-list-item-group>
                        </v-menu>
                      </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
</template>

<script>
import { mapGetters } from 'vuex'
import LocaleDropdown from './LocaleDropdown'

export default {
  components: {
    LocaleDropdown
  },

  props: {
    mini: {
      type: Boolean,
      required: true
    },
    headerNotify: {
      type: Array,
      required: true
    }
  },

  data: () => ({
    appName: window.config.appName,
    notifyLength: 0
  }),

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    guestList() {
      return this.$store.getters['agent/guest_list']
    },
  },

  watch: {
    guestList: function(val) {
      this.notifyLength = 0
      this.countNotify(true)
    }
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    },
    clearHeaderNotify () {
      // this.headerNotify = []
    },
    momentSince: function (datetime) {
      return moment(datetime).fromNow().replace('ago', '');
    },
    countNotify(increase = true) {
      this.guestList.forEach(element => {
        if(element.guest_list[0].status == 'notify') {
          this.notifyLength++
        }
      });
    }
  }
}
</script>

<style scoped>
.profile-photo {
  width: 2rem;
  height: 2rem;
  margin: -.375rem 0;
}
.nav-item {
  padding: 0 1.5rem 0 2px;
}
</style>
