<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="RT鋼板厚度係數表"
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
                  <span class="text-h5">{{ editedItem.file_name }}</span>
                </v-card-title>
                <v-card-subtitle>
                  {{ editedItem.description }}
                </v-card-subtitle>
                <v-card-text>
                  <v-simple-table
                    fixed-header
                    height="300px"
                  >
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-left">
                            厚度
                          </th>
                          <th class="text-left">
                            係數
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item, index) in selectItem"
                          :key="index"
                        >
                          <td>{{ item.thickness }}</td>
                          <td>{{ item.numness }}</td>
                        </tr>
                        <tr v-if="selectItem.length == 0">
                          <td colspan="2">
                            <v-progress-linear
                              indeterminate
                              color="cyan"
                            ></v-progress-linear>
                            </td>
                        </tr>
                      </tbody>
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
            <!-- <v-btn
                color="secondary"
                small
                dark
                class="mx-2"
                @click="editItem(item)"
              >
              <v-icon small>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
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

export default {
  name: 'RTthicknessView',

  middleware: 'auth',

  metaInfo () {
    return { title: this.$t('import_title') }
  },

  created () {
    console.log("RTthicknessView")
    this.initialize()
  },

  computed: {
    formTitle () {
      return '上傳檔案'
    },
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
        text: '檔名',
        align: 'start',
        value: 'file_name',
      },
      { text: '描述', value: 'description' },
      { text: '更新日期', value: 'updated_at' },
      { text: '操作者', value: 'updated_user' },
      { text: 'Actions', value: 'actions', sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      file_name: '',
      description: '',
      updated_user: '',
      updated_at: '',
      id: -1,
    },
    defaultItem: {
      file_name: '',
      description: '',
      updated_user: '',
      updated_at: '',
      id: -1,
    },
  }),
  methods: {
    initialize () {
      this.listDatas()
    },
    listDatas() {
      this.loading = true
      let url = 'api/import/data/rt_ness'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            console.log(res.data)
            this.dataItems = res.data.data.map((_, idx) => ({
                file_name: res.data.data[idx].file_name,
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
      let url = 'api/import/data/rt_ness/' + this.editedItem.id
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            console.log(res.data)
            this.selectItem = res.data.data.map((_, idx) => ({
                thickness: res.data.data[idx].thickness,
                numness: res.data.data[idx].numness,
              }))
          })
          .catch(err => {
            console.log(err)
          })
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
          let url = 'api/import/rtness'
          formData.append("ex_file", this.excelFile);
          this.$axios
            .fetchUpload(url, formData)
              .then(res => {
                that.loadingSave = false
                that.close()
                that.listDatas()
              })
              .catch(err => {
                that.loadingSave = false
                that.close()
              })
        }
      }
      
    },
    // handleFileUpload() {
    //   this.excelFile = this.$refs.file.files[0];
    // },
  }
}
</script>
