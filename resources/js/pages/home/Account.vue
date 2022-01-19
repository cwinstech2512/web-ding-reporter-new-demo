<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="帳號管理"
      color="accent"
    >
      <v-data-table
        :headers="headers"
        :items="userItems"
        sort-by="calories"
        class="elevation-1"
        hide-default-footer
        :page.sync="page"
        :items-per-page="itemsPerPage"
        @page-count="pageCount = $event"
        :loading="loading"
        :search="search"
      >
        <template v-slot:top>
          <v-toolbar
            flat
          >
            <v-toolbar-title>列表</v-toolbar-title>
            <v-divider
              class="mx-4"
              inset
              vertical
            ></v-divider>
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="搜尋..."
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-dialog
              v-model="dialog"
              max-width="500px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  新增帳號
                </v-btn>
              </template>
              <v-card>
                <v-card-title>
                  <span class="text-h5">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col
                        cols="12"
                        md="12"
                      >
                        <v-text-field
                          v-model="editedItem.name"
                          label="帳號＊"
                          hint="登入時所需要輸入的帳號"
                          outlined
                          :readonly="editedIndex !== -1"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col
                        cols="12"
                        md="12"
                      >
                        <v-text-field
                          v-model="editedItem.email"
                          outlined
                          label="Email"
                          hint="請輸入Email"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col
                        cols="12"
                        md="12"
                      >
                        <v-text-field
                          v-model="editedItem.password"
                          outlined
                          password
                          label="密碼＊"
                          hint="登入時所需要輸入的密碼"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col
                        cols="12"
                        md="12"
                      >
                        <v-select
                          label="權限＊"
                          outlined
                          hint="請選擇使用者權限"
                          :items="roleItems"
                          item-text="text"
                          item-value="value"
                          v-model="editedItem.role"
                        ></v-select>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="blue darken-1"
                    text
                    @click="close"
                  >
                    取消
                  </v-btn>
                  <v-btn
                    color="success"
                    text
                    @click="save"
                  >
                    儲存
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h5">是否確認要刪除資料?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="closeDelete">取消</v-btn>
                  <v-btn color="error" text @click="deleteItemConfirm">確定</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
            <v-btn
                color="secondary"
                small
                dark
                class="mx-2"
                @click="editItem(item)"
              >
              <v-icon small>mdi-pencil</v-icon>
            </v-btn>
            <!-- <v-btn
                color="error"
                small
                dark
                class="mx-2"
                @click="deleteItem(item)"
              >
              <v-icon small>mdi-delete</v-icon>
            </v-btn> -->
        </template>
        <template v-slot:no-data>
          <v-btn
            color="primary"
            @click="initialize"
          >
            重新整理
          </v-btn>
        </template>
      </v-data-table>
      <div class="text-center pt-2">
        <v-pagination
          v-model="page"
          :length="pageCount"
        ></v-pagination>
      </div>
    </material-card>

  </v-container>
</template>
<script>
  // Utilities
  import { get } from 'vuex-pathify'
  import Vue from 'vue'

  export default {
    name: 'AccountView',

    middleware: 'auth',

    metaInfo () {
      return { title: this.$t('home') }
    },

    created () {
      console.log("AccountView")
      this.initialize()
    },

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? '新增帳號' : '編輯帳號'
      },
    },

    data: () => ({
      search: '',
      page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      dialog: false,
      dialogDelete: false,
      loading: false,
      userItems: [],
      roleItems: [],
      headers: [
        {
          text: '帳號',
          align: 'start',
          value: 'name',
        },
        { text: 'Email', value: 'email' },
        { text: '角色', value: 'role' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        name: '',
        email: '',
        password: '',
        role: '',
      },
      defaultItem: {
        name: '',
        email: '',
        password: '',
        role: '',
      },
    }),
    methods: {
      initialize () {
        this.getRoles()
      },

      getUsers () {
        this.loading = true
        let url = 'api/account'
        let params = {}
        this.$axios
          .fetchGet(url, params)
            .then(res => {
              console.log(res.data)
              this.userItems = res.data.users.map((_, idx) => ({
                  name: res.data.users[idx].name,
                  roleId: res.data.users[idx].role,
                  email: res.data.users[idx].email,
                  id: res.data.users[idx].id,
                  role: this.roleItems.find(function (role) {
                          return role.value === res.data.users[idx].role
                        }).text,
                  // id: res.data.users[idx].id
                }))
              this.loading = false
            })
            .catch(err => {
              console.log(err)
              this.loading = false
            })
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
              this.roleItems = res.data.roles.map((_, idx) => ({
                  value: res.data.roles[idx].id,
                  text: res.data.roles[idx].slug
                }))
              that.getUsers()
            })
            .catch(err => {
              console.log(err)
            })
      },

      editItem (item) {
        this.editedIndex = this.userItems.indexOf(item)
        this.editedItem = Object.assign({}, item)
        console.log(this.editedItem)
        this.editedItem.role = this.editedItem.roleId
        this.dialog = true
      },

      deleteItem (item) {
        console.log(item)
        this.$swal.fire({
          title: '確定要刪除數據?',
          showCancelButton: true,
          confirmButtonText: '確認',
          showLoaderOnConfirm: true,
          cancelButtonText: '取消',
          preConfirm: () => {
            let url = `api/account/${item.id}`;
            return this.$axios
              .fetchDelete(url, {})
              .then(res => {
                return res.data.data
              })
              .catch(err => {
                // this.tLoading = false
                console.log(err)
              })
          },
          allowOutsideClick: () => this.$swal.isLoading()
        }).then((result) => {
            if(result.value.code == 200) {
                this.getKeyword()
                this.getData()
                this.$swal.fire({
                  title: "成功!",
                  text: "刪除數據成功",
                  type: "success",
                  confirmButtonClass: "btn btn-success",
                  buttonsStyling: false
                });
            } else {
                this.$swal.fire({
                  title: "錯誤!",
                  text: "刪除數據錯誤",
                  type: "error",
                  confirmButtonClass: "btn btn-danger",
                  buttonsStyling: false
                });
            }
        })
        // this.editedIndex = this.userItems.indexOf(item)
        // this.editedItem = Object.assign({}, item)
        // this.dialogDelete = true
      },

      deleteItemConfirm () {
        this.userItems.splice(this.editedIndex, 1)
        this.closeDelete()
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          let url = `api/account/${this.editedIndex}`;

          let data = {};
          console.log(this.editedItem.password)
          if (!this.editedItem.password) {
            data = {
              name: this.editedItem.name,
              email: this.editedItem.email,
              role: this.editedItem.role
            };
          } else {
            data = { ...this.editedItem };
          }

          this.$axios
            .fetchPut(url, data)
            .then(({ data }) => {
              // const newUsers = this.userItems.filter(
              //   user => user.id !== data.user.id
              // );
              // newUsers.unshift(data.user);
              // this.userItems = newUsers;

              this.$swal.fire({
                  title: "成功!",
                  text: "更新帳號成功",
                  type: "success",
                  confirmButtonClass: "btn btn-success",
                  buttonsStyling: false
                });
              this.getRoles()
            })
            .catch(({ response }) => {
              if (response.status === 422) {
                this.notify(response.data.errors, "error");
              } else {
                Swal.fire({
                  text: JSON.stringify(response)
                });
              }
            });
        } else {
          let url = "api/account";

          this.$axios
            .fetchPost(url, this.editedItem)
            .then(({ data }) => {
              this.userItems.push(data.user);
              
              this.$swal.fire({
                  title: "成功!",
                  text: "新增帳號成功",
                  type: "success",
                  confirmButtonClass: "btn btn-success",
                  buttonsStyling: false
                });

              this.getRoles()
            })
            .catch(({ response }) => {
              if (response.status === 422) {
                this.notify(response.data.errors, "error");
              } else {
                Swal.fire({
                  text: JSON.stringify(response)
                });
              }
            });
        }
        this.close()
      },
    },
  }
</script>