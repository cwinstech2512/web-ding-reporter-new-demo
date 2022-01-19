<template>
  <!-- <v-text-field
    placeholder="Search"
    class="mr-16"
    color="secondary"
    hide-details
    style="max-width: 220px"
  >
    <template
      v-if="$vuetify.breakpoint.mdAndUp"
      v-slot:append-outer
    >
      <v-btn
        class="mt-n2 ml-n2"
        fab
        small
        elevation="2"
        height="44"
        width="44"
      >
        <v-icon>mdi-magnify</v-icon>
      </v-btn>
    </template>
  </v-text-field> -->
  <v-form
  class="mr-16 mt-8">
    <v-flex xs12>
      <v-autocomplete
        v-model="searchString"
        :disabled="isUpdating"
        :items="people"
        color="secondary"
        label="Search"
        item-text="name"
        item-value="name"
        clearable
        append-icon=""
        :open-on-click="false"
      >
        <template
          v-if="$vuetify.breakpoint.mdAndUp"
          v-slot:append-outer
        >
          <v-btn
            class="mt-n2 ml-n2"
            fab
            small
            elevation="2"
            height="44"
            width="44"
          >
            <v-icon>mdi-magnify</v-icon>
          </v-btn>
        </template>
    
        <!-- <template
          slot="selection"
          slot-scope="data"
        >
          <v-chip
            :selected="data.selected"
            close
            class="chip--select-multi"
            @input="data.parent.selectItem(data.item)"
          >
            <v-avatar>
              <img :src="data.item.avatar">
            </v-avatar>
            {{ data.item.name }}
          </v-chip>
        </template> -->
        <template
          slot="item"
          slot-scope="data"
        >
<!--                   <template v-if="typeof data.item !== 'object'">
            <v-list-tile-content v-text="data.item"></v-list-tile-content>
          </template> -->
          <template >
            <v-list-item-icon
              v-if="data.item.avatar"
              class="my-2 align-self-center"
            >
              <v-icon v-text="data.item.avatar" />
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-html="data.item.name"></v-list-item-title>
              <v-list-item-subtitle v-if="data.item.group=='Products'" v-html="data.item.seller"></v-list-item-subtitle>
            </v-list-item-content>
          </template>
        </template>
      </v-autocomplete>
    </v-flex>
  </v-form>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { get } from 'vuex-pathify'
  export default {
    name: 'DefaultSearch',

    computed: {
      ...mapGetters({
        user: 'auth/user'
      }),
      ...get('app', [
        'items',
      ]),
    },

    created () {
      this.initialize()
    },

    data: () => ({
      searchString:"",
      isUpdating: false,
      people: [
        // { header: '系統管理' },
        // { name: 'Sandra Adams', group: 'Sellers', avatar: '' },
        // { name: 'Willy Wonka', group: 'Sellers', avatar: '' },
        // { name: 'Trevor Hansen', group: 'Sellers', avatar: '' },
        // { name: 'Tucker Smith', group: 'Sellers', avatar: '' },
        // { divider: true },
        // { header: 'Products' },
        // { name: 'Remote V12X32', group: 'Products', seller: 'Arnold Systems', avatar: '' },
        // { name: 'Crunchy tab', group: 'Products', seller: 'Willy Wonka', avatar: '' },
        // { name: 'Love potion (potent)', group: 'Products', seller: 'Severus Snape', avatar: '' },
        // { name: 'Mechanical Kitty', group: 'Products', seller: 'Me Me', avatar: '' }
      ],
      title: 'The summer breeze'
    }),

    methods: {
      initialize () {
        this.items.forEach(element => {
          var isDivider = false
          if(element.to && this.authMenuItem(element.to)) {
            this.people.push({ header: element.title })
            this.people.push({ name: element.title, avatar: element.icon })
            isDivider = true
          } else if(!element.to){
            this.people.push({ header: element.title })
            isDivider = true
          }
          if(element.items) {
            element.items.forEach(i => {
              if(i.title && this.authMenuItem(i.to)) {
                this.people.push({ name: i.title, avatar: i.icon })
              }
            })
          }
          if(isDivider) this.people.push({ divider: true })
        });
      },
      authMenuItem (key) {
        return this.user.menu.menus.find(e => Object.values(e.menu_info).find((obj) => { return obj === key}))
      }
    }
  }
</script>
<style scoped>
.v-select.v-select--is-menu-active .v-input__icon--append .v-icon {
    transform: none;
}
</style>
