<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="射源表"
      color="accent"
    >
      <v-data-table
        :headers="headers"
        :items="dataItems"
        sort-by="calories"
        class="elevation-1"
        hide-default-footer
        :page.sync="page"
        :items-per-page="itemsPerPage"
        @page-count="pageCount = $event"
        :loading="loading"
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
            <v-dialog
              v-model="dialogNew"
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
                  上傳檔案
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
                      >
                        <v-text-field
                          v-model="editedItem.identify"
                          label="許可證字號"
                          outlined
                          prepend-icon="mdi-map-marker"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                      >
                        <v-text-field
                          v-model="editedItem.diameter"
                          label="Diameter"
                          outlined
                          prepend-icon="mdi-flash-outline"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                      >
                        <v-text-field
                          v-model="editedItem.length"
                          label="Length"
                          outlined
                          prepend-icon="mdi-flash-outline"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                      >
                        <v-text-field
                          v-model="editedItem.diagonal"
                          label="Diagonal"
                          outlined
                          prepend-icon="mdi-flash-outline"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="12"
                      >
                        <v-file-input
                          label="請選擇Excel"
                          outlined
                          dense
                          show-size
                          ref="files"
                          v-model="excelFile"
                        ></v-file-input>
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
                    :loading="loadingSave"
                  >
                    儲存
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog
              v-model="dialogView"
              width="600px"
            >
              <v-card>
                <v-card-title>
                  <span class="text-h5">{{ editedItem.identify }} 的列表</span>
                </v-card-title>
                <v-card-text>
                  <v-simple-table
                    fixed-header
                    height="325px"
                    width="405px"
                  >
                    <template v-for="(t, idx) in selectItem">
                      <v-timeline
                        align-top
                        dense
                        :key="idx"
                      >
                        <v-timeline-item
                          :color="timeCorlorList(Math.random())"
                          small
                        >
                          <v-row class="pt-1">
                            <v-col cols="3">
                              <strong>{{ t.use_date }}</strong>
                            </v-col>
                            <v-col>
                              <strong>{{ t.data_value }}</strong>
                              <!-- <div class="text-caption">
                                Mobile App
                              </div> -->
                            </v-col>
                          </v-row>
                        </v-timeline-item>
                      </v-timeline>
                    </template>
                  </v-simple-table>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="green darken-1"
                    text
                    @click="closeView"
                  >
                    關閉
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
              @click="viewItem(item)"
            >
            <v-icon small>mdi-eye</v-icon>
          </v-btn>
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
import { mapGetters } from 'vuex'
export default {
  name: 'DiameterLengthView',

  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('import_title') }
  },

  computed: {
    formTitle () {
      return '上傳設備'
    },
    ...mapGetters({
      user: 'auth/user'
    }),
  },

  created () {
    console.log("DiameterLengthView")
    this.initialize()
  },

  data: () => ({
    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    dialogNew: false,
    dialogView: false,
    dialogDelete: false,
    loading: false,
    loadingSave: false,
    dataItems: [],
    selectItem: [],
    excelFile: null,
    headers: [
      {
        text: '許可證字號',
        align: 'start',
        value: 'identify',
      },
      { text: '更新日期', value: 'updated_at' },
      { text: '操作者', value: 'role' },
      { text: 'Actions', value: 'actions', sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      id: -1,
      file_name: '',
      identify: '',
      length: '',
      diameter: '',
      diagonal: '',
    },
    defaultItem: {
      id: -1,
      identify: '',
      file_name: '',
      length: '',
      diameter: '',
      diagonal: '',
    },
  }),
  methods: {
    initialize () {
      this.listDatas()
    },
    listDatas() {
      this.loading = true
      let url = 'api/import/data/dialength'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            console.log(res.data)
            this.dataItems = res.data.data.map((_, idx) => ({
                identify: res.data.data[idx].identify,
                description: res.data.data[idx].description,
                updated_user: res.data.data[idx].updated_user,
                updated_at: res.data.data[idx].updated_at,
                id: res.data.data[idx].id
              }))
            this.loading = false
          })
          .catch(err => {
            console.log(err)
            this.loading = false
          })
    },
    
    itemData() {
      let url = 'api/import/data/dialength/' + this.editedItem.id
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            console.log(res.data)
            this.selectItem = res.data.data.map((_, idx) => ({
                data_value: res.data.data[idx].data_value,
                use_date: res.data.data[idx].use_date,
              }))
          })
          .catch(err => {
            console.log(err)
          })
    },

    timeCorlorList (rnd) {
      var list = ['pink', 'purple', 'teal', 'green', 'amber', 'blue-grey']
      var n = Math.floor(rnd * list.length + 1)-1
      return list[n]
    },

    viewItem(item) {
      this.dialogView = true

      this.editedIndex = this.dataItems.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.itemData()
    },

    deleteItemConfirm () {
      this.userItems.splice(this.editedIndex, 1)
      this.closeDelete()
    },

    close () {
      this.dialogNew = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    closeView () {
      this.dialogView = false
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
      let that = this
      if (this.editedIndex > -1) {
        // Object.assign(this.desserts[this.editedIndex], this.editedItem)
      } else {
        if(this.excelFile) {
          that.loadingSave = true
          let formData = new FormData();
          let url = 'api/import/dialength'
          formData.append("ex_file", this.excelFile)
          formData.append("identify", this.editedItem.identify)
          formData.append("length", this.editedItem.length)
          formData.append("diameter", this.editedItem.diameter)
          formData.append("diagonal", this.editedItem.diagonal)
          this.$axios
            .fetchUpload(url, formData)
              .then(res => {
                if(res.data.data.code != 1000) {
                  that.$swal.fire({
                    title: "錯誤!",
                    text: res.data.data.message,
                    type: "error",
                    confirmButtonClass: "btn btn-danger"
                  });
                } else {
                  that.$swal.fire({
                    title: "成功!",
                    text: "儲存數據成功",
                    type: "success",
                    confirmButtonClass: "btn btn-success"
                  });

                  that.close()
                  that.listDatas()
                }
                that.loadingSave = false
              })
              .catch(err => {
                that.$swal.fire({
                  title: "錯誤!",
                  text: "儲存數據錯誤",
                  type: "error",
                  confirmButtonClass: "btn btn-danger"
                });
                that.loadingSave = false
                that.close()
              })
        }
      }
    },
  }
}
</script>
