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
      title="監工/人時日報"
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
            <v-toolbar-title>監工/人時日報統計表</v-toolbar-title>
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
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="text-h5">是否確認要刪除資料?</v-card-title>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="closeDelete">取消</v-btn>
                  <v-btn color="error" text @click="deleteItemConfirm" :loading="loadingDelete">確定</v-btn>
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
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
                    <!-- TP 標籤和檔案上傳 -->
                    <v-row>
                      <v-col cols="12" sm="2">
                        <v-text-field
                          label="TP 標籤"
                          v-model="tpLabel"
                          outlined
                          dense
                          readonly
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="10">
                        <v-file-input
                          :label="`上傳${tpLabel || 'TP'}檔案`"
                          outlined
                          dense
                          show-size
                          ref="tpFiles"
                          v-model="tpFile"
                          required
                        ></v-file-input>
                      </v-col>
                    </v-row>
                    <!-- JK 標籤和檔案上傳 -->
                    <v-row>
                      <v-col cols="12" sm="2">
                        <v-text-field
                          label="JK 標籤"
                          v-model="jkLabel"
                          outlined
                          dense
                          readonly
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="10">
                        <v-file-input
                          :label="`上傳${jkLabel || 'JK'}檔案`"
                          outlined
                          dense
                          show-size
                          ref="jkFiles"
                          v-model="jkFile"
                          required
                        ></v-file-input>
                      </v-col>
                    </v-row>
                    <v-row>
                      <!-- 下拉選單 -->
                      <v-col cols="12" sm="10">
                        <v-select :items="areasItems" label="選擇區域" prepend-icon="mdi-form-dropdown" v-model="selectedArea"></v-select>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" sm="10">
                        <v-menu ref="menuDatePicker" v-model="menuDatePicker" :close-on-content-click="false" transition="scale-transition" offset-y min-width="290px">
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field v-model="reportDate" label="選擇監工日期" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on" required></v-text-field>
                          </template>
                          <v-date-picker v-model="reportDate" @input="menuDatePicker = false"></v-date-picker>
                        </v-menu>
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
                    :loading="loadingPost"
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
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="error"
                    x-small
                    dark
                    class="mx-2"
                    fab
                    @click="deleteItem(item)"
                    v-bind="attrs"
                    v-on="on"
                  >
                  <v-icon dark>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>刪除</span>
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
    deleteId: -1,
    dialogDelete: false,
    loadingDelete: false,
    active: [],
    open: [],
    search: '',
    sampleItems: [],
    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    dataItems: [],
    reportTypeItems: ['TP', 'JK'],
    areasItems:[
      '北區',
      '中區',
      '南區',
    ],
    menuDatePicker: false,
    reportDate: new Date().toISOString().substr(0, 10), // 預設今天日期,
    selectedReportType: '',
    selectedArea: '',
    editedItem: {},
    editedIndex: -1,
    tpFile: null,
    jkFile: null,
    jkLabel: "JK",
    tpLabel: "TP",
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
    loadingPost: false
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
      let that = this
      this.loading = true
      let co = SAMPLE_PDF_PATH.split('_');
      let url = `api/taipower/data/daily_list`
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data)
            this.dataItems = res.data.data.map((_, idx) => ({
                name: res.data.data[idx].report_type + '-' + res.data.data[idx].name,
                export_paths: JSON.parse(res.data.data[idx].export_paths),
                report_date: res.data.data[idx].report_date,
                report_type: res.data.data[idx].report_type,
                updated_at: that.formatDate(res.data.data[idx].updated_at),
                created_at: that.formatDate(res.data.data[idx].created_at),
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

      if (!this.tpFile || !this.jkFile || !this.tpFile.name.includes(this.tpLabel) || !this.jkFile.name.includes(this.jkLabel)) {
        that.$swal.fire({
          title: "錯誤!",
          text: `請確保已上傳TP和JK檔案，且檔名分別包含"${this.tpLabel}"和"${this.jkLabel}"。`,
          type: "warning",
          confirmButtonClass: "btn btn-warning"
        });
        return; // 終止函數執行
      }
      that.loadingPost = true
      let url = `api/taipower/upload/daily/`
      let formData = new FormData();
      formData.append("jkFile", this.jkFile);
      formData.append("tpFile", this.tpFile);
      formData.append("report_date", this.reportDate);
      formData.append("report_area", this.selectedArea);
      this.$axios
        .fetchUpload(url, formData)
          .then(res => {
            // console.log(res.data)
            let _res = res.data.data
            
            this.closeSample()
            this.listDatas()

            that.loadingPost = false
          })
          .catch(err => {
            console.log(err)
          })
    },
    deleteItem(item) {
      this.dialogDelete = true
      this.deleteId = item.id
    },
    deleteItemConfirm() {
      this.dialogDelete = true
      let that = this
      let url = 'api/taipower/remove/' + this.deleteId
      this.$axios
        .fetchDelete(url, {})
          .then(res => {
            that.loadingDelete = false
            that.closeDelete()
            that.listDatas()
            let result = res.data
            if(result.code == 1000) {
                that.$swal.fire({
                  title: "成功!",
                  text: result.message,
                  type: "success",
                  confirmButtonClass: "btn btn-success"
                });
            } else {
                that.$swal.fire({
                  title: "錯誤!",
                  text: result.message,
                  type: "error",
                  confirmButtonClass: "btn btn-danger"
                });
            }
          })
          .catch(err => {
            that.loadingDelete = false
            that.close()
            that.$swal.fire({
              title: "錯誤!",
              text: "系統錯誤，請稍後重新嘗試",
              type: "error",
              confirmButtonClass: "btn btn-danger"
            });
          })
    },
    closeDelete () {
      this.dialogDelete = false
      this.deleteId = -1
    },
    viewItem(item) {
      this.dialogView = true

      this.editedIndex = -2
      this.editedItem = Object.assign({}, item)
    },
    formatDate(dateTimeString) {
      const date = new Date(dateTimeString);
      return date.getFullYear() + '-' +
            (date.getMonth() + 1).toString().padStart(2, '0') + '-' +
            date.getDate().toString().padStart(2, '0') + ' ' +
            date.getHours().toString().padStart(2, '0') + ':' +
            date.getMinutes().toString().padStart(2, '0') + ':' +
            date.getSeconds().toString().padStart(2, '0');
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