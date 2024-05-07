<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <v-dialog v-model="dialogView" max-width="800px">
      <v-card>
        <v-card-title class="headline">詳細資料</v-card-title>
        <v-card-text>
          <v-list dense>
            <v-list-item-group>
              <v-list-item v-for="(file, fi) in editedItem.export_paths" :key="fi">
                <v-list-item-icon class="vertical-center">
                  <v-icon>mdi-file-document</v-icon>
                </v-list-item-icon>
                <v-list-item-content class="vertical-center">
                  <v-list-item-title>{{ extractFileName(file) }}</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action class="vertical-center">
                  <v-btn icon color="primary" @click="downloadFile(file)">
                    <v-icon>mdi-download</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="dialogView = false">關閉</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="檢測或人時數量統計表"
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
        :search="search"
      >
        <template v-slot:top>
          <v-toolbar
            flat
          >
            <v-toolbar-title>數量統計表</v-toolbar-title>
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
              v-model="dialogSample"
              max-width="500px"
              v-if="authRoleVaild('upload')"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="info"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  上傳數據資料
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
                    @click="closeSample"
                  >
                    關閉
                  </v-btn>
                  <v-btn
                    color="success"
                    text
                    @click="postSample"
                  >
                    提交
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <div class="text-right">
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="secondary"
                    x-small
                    dark
                    class="mx-2"
                    fab
                    @click="viewItem(item)"
                    v-bind="attrs"
                    v-on="on"
                  >
                  <v-icon dark>mdi-eye</v-icon>
                </v-btn>
              </template>
              <span>檢視</span>
            </v-tooltip>
            <v-tooltip  v-if="item.status == 1" top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="blue lighten-1"
                    x-small
                    dark
                    class="mx-2"
                    fab
                    @click="editItem(item)"
                    v-bind="attrs"
                    v-on="on"
                  >
                  <v-icon dark>mdi-file-document-edit-outline</v-icon>
                </v-btn>
              </template>
              <span>下載報告</span>
            </v-tooltip>
          </div>
        </template>
       </v-data-table>
    </material-card>
  </v-container>
</template>
<script>
import { mapGetters } from 'vuex'
import VueDocPreview  from 'vue-doc-preview'

const SAMPLE_PDF_PATH = 'normal_pt'
const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
export default {
  name: 'Taipower_upload_tp',

  middleware: 'auth',

  components: {
    VueDocPreview
  },

  metaInfo () {
    return { title: this.$t('report_title') }
  },

  created () {
    this.initialize()
  },

  data: () => ({
    active: [],
    open: [],
    search: '',
    sampleItems: [],
    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    dataItems: [],
    editedItem: {},
    editedIndex: -1,
    excelFile: null,
    headers: [
      {
        text: '報告編號',
        align: 'start',
        value: 'id',
      },
      { text: '工程名稱', value: 'name' },
      { text: '更新日期', value: 'updated_at' },
      { text: 'Actions', value: 'actions', sortable: false },
    ],
    dialogSample: false,
    dialogView: false,
  }),

  computed: {
    formTitle () {
      return '檢測或人時數量統計資料'
    },
    ...mapGetters({
      user: 'auth/user'
    }),
  },
  watch: {

  },

  methods: {
    initialize () {
      this.listDatas()
    //   this.activeSample()
    },
    listDatas() {
      this.loading = true
      let co = SAMPLE_PDF_PATH.split('_');
      let url = `api/taipower/data/list`
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data)
            this.dataItems = res.data.data.map((_, idx) => ({
                name: res.data.data[idx].name,
                export_paths: JSON.parse(res.data.data[idx].export_paths),
                updated_at: res.data.data[idx].updated_at,
                created_at: res.data.data[idx].created_at,
                id: res.data.data[idx].id,
              }))
            this.loading = false
          })
          .catch(err => {
            console.log(err)
            this.loading = false
          })
    },
    postSample() {
      let that = this
      let url = `api/taipower/upload/imhqst/`
      let formData = new FormData();
      formData.append("ex_file", this.excelFile);
      this.$axios
        .fetchUpload(url, formData)
          .then(res => {
            // console.log(res.data)
            let _res = res.data.data
            
            this.closeSample()
            this.listDatas()
          })
          .catch(err => {
            console.log(err)
          })
    },
    viewItem(item) {
      this.dialogView = true

      this.editedIndex = -2
      this.editedItem = Object.assign({}, item)
    },
    extractFileName(path) {
      return path.split('/').pop();
    },
    downloadFile(filePath) {
      window.open(filePath);
    },
    authRoleVaild (key) {
      return true
    },
    closeSample () {
      this.dialogSample = false
    },
  },
}

</script>
<style lang="scss" scope>
.v-file-input__text {
  cursor: pointer;
}
tr .v-text-field__details {
  display: none !important;
}
.vertical-center {
  display: flex;
  align-items: center;
  height: 100%;
}
</style>