<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="派單"
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
              v-model="dialogView"
              fullscreen
              hide-overlay
              persistent
              transition="dialog-bottom-transition"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                  @click="createView"
                >
                  建立派單
                </v-btn>
              </template>
              <v-card>
                <v-toolbar
                  dark
                  color="primary"
                >
                  <v-btn
                    icon
                    dark
                    @click="closeView"
                  >
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-toolbar-title>{{ '派工單' }}</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-toolbar-items>
                    <v-btn
                      dark
                      text
                      v-if="editedIndex !== -2"
                      @click="save"
                      :loading="loadingSave"
                    >
                      儲存
                    </v-btn>
                  </v-toolbar-items>
                </v-toolbar>

                <v-card-title>
                  <span class="text-h5">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col
                        cols="6"
                        v-if="editedItem.serial_no"
                      >
                        <v-text-field
                          v-model="editedItem.serial_no"
                          label="委託單編號"
                          outlined
                          prepend-icon="mdi-counter"
                          :readonly="true"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                        <v-text-field
                          v-model="editedItem.customer"
                          label="業主(Customer)"
                          outlined
                          prepend-icon="mdi-crown-circle-outline"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                        <v-text-field
                          v-model="editedItem.job_name"
                          label="工程名稱(Job Name)"
                          outlined
                          prepend-icon="mdi-city-variant"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                      <v-menu
                        v-model="examDatePickShow"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                        >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="editedItem.exam_date"
                                label="起始檢測日期(Exam. Date)"
                                prepend-icon="mdi-calendar-start"
                                readonly
                                outlined
                                v-bind="attrs"
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="editedItem.exam_date"
                            @input="examDatePickShow = false"
                            :readonly="readonlyVisible"
                        ></v-date-picker>
                      </v-menu>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                      <v-menu
                        v-model="examDateEPickShow"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                        >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="editedItem.e_exam_date"
                                label="結束檢測日期(Exam. Date)"
                                prepend-icon="mdi-calendar-end"
                                readonly
                                outlined
                                v-bind="attrs"
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="editedItem.e_exam_date"
                            @input="examDateEPickShow = false"
                            :readonly="readonlyVisible"
                        ></v-date-picker>
                      </v-menu>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                        <v-text-field
                          v-model="editedItem.exam_place"
                          label="檢測地點(Exam. At)"
                          outlined
                          prepend-icon="mdi-map-marker"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                         <v-select
                          v-model="editedItem.function_path"
                          :items="examFunctionPath"
                          multiple
                          label="檢測分類"
                          item-text="text"
                          item-value="value"
                          outlined
                          prepend-icon="mdi-distribute-horizontal-center"
                          :readonly="readonlyVisible"
                        ></v-select>
                      </v-col>
                      <v-col
                        cols="6"
                      >
                         <v-select
                          v-model="editedItem.transport"
                          :items="listTransportation_Datas"
                          label="交通工具"
                          item-text="show_text"
                          item-value="id"
                          outlined
                          prepend-icon="mdi-car-wrench"
                          :readonly="readonlyVisible"
                        ></v-select>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <!-- <v-card-actions>
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
                    v-if="editedIndex !== -2"
                    @click="save"
                    :loading="loadingSave"
                  >
                    儲存
                  </v-btn>
                </v-card-actions> -->
              </v-card>
            </v-dialog>
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
          </v-toolbar>
        </template>
        <template v-slot:[`item.status`]="{ item }">
          <DataTableStatus :item="item"></DataTableStatus>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-btn
              color="secondary"
              small
              dark
              class="mx-1"
              @click="viewItem(item)"
            >
            <v-icon small>mdi-eye</v-icon>
          </v-btn>
          <v-btn
              color="green"
              small
              dark
              class="mx-1"
              @click="editItem(item)"
            >
            <v-icon small>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
              color="error"
              small
              dark
              class="mx-1"
              @click="deleteItem(item)"
            >
            <v-icon small>mdi-delete</v-icon>
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
import DataTableStatus from '../../components/DataTableStatus.vue'

export default {
  name: 'DispatchView',

  middleware: 'auth',

  components: {
    DataTableStatus
  },

  metaInfo () {
    return { title: this.$t('dispath_title') }
  },

  created () {
    console.log("DispatchView")
    this.initialize()
  },

  computed: {
    formTitle () {
      return this.editedIndex > 0? '派單編輯' : '派工單'
    },
    readonlyVisible () {
      return this.editedIndex == -2
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
    loadingDelete: false,
    search: '',
    dataItems: [],
    selectItem: [],
    listTransportation_Datas:[],
    excelFile: null,
    headers: [
      {
        text: '業主',
        align: 'start',
        value: 'customer',
      },
      { text: '工程名稱', value: 'job_name' },
      { text: '檢測日期', value: 'exam_date' },
      { text: '檢測地點', value: 'exam_place' },
      { text: '狀態', value: 'status' },
      { text: '更新日期', value: 'updated_at' },
      { text: '操作者', value: 'updated_user' },
      { text: 'Actions', value: 'actions', sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      customer: '',
      serial_no: '',
      job_name: '',
      exam_date: '',
      e_exam_date: '',
      function_path: '',
      exam_place: '',
      transport: 0,
      status: 0,
      id: -1,
    },
    defaultItem: {
      customer: '',
      serial_no: '',
      job_name: '',
      exam_date: '',
      e_exam_date: '',
      function_path: '',
      exam_place: '',
      transport: 0,
      status: 0,
      id: -1,
    },
    examDatePickShow: false,
    examDateEPickShow: false,
    examFunctionPath: [
      {text: '奇美PT', value: 'chimei.pt'},
      {text: '奇美RT', value: 'chimei.rt'},
      {text: '奇美MT', value: 'chimei.mt'},
      {text: '奇美UT', value: 'chimei.ut'},
      {text: '奇美IT', value: 'chimei.it'},
      {text: '一般PT', value: 'normal.pt'},
      {text: '台塑油料廠', value: 'fpg.rtoil'},
      {text: '台塑烯烴三廠', value: 'fpg.rtolll'},
      {text: '台塑煉製三廠', value: 'fpg.rthree'}
    ]
  }),
  methods: {
    initialize () {
      this.transportGet()
      this.listDatas()
    },
    listDatas() {
      this.loading = true
      let url = 'api/dispatch/data/list'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            console.log(res.data)
            this.dataItems = res.data.data.map((_, idx) => ({
                job_name: res.data.data[idx].job_name,
                customer: res.data.data[idx].customer,
                exam_date: res.data.data[idx].exam_date,
                e_exam_date: res.data.data[idx].e_exam_date,
                exam_place: res.data.data[idx].exam_place,
                status: res.data.data[idx].status,
                updated_user: res.data.data[idx].updated_user,
                updated_at: res.data.data[idx].updated_at,
                transport: res.data.data[idx].transport,
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
      // let that = this
      let url = 'api/dispatch/data/first/' + this.editedItem.id
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data)
            this.editedItem = Object.assign({}, res.data.data)
            this.editedItem.function_path = res.data.data.function_path.split(',')
          })
          .catch(err => {
            console.log(err)
          })
    },

    transportGet() {
      let url = 'api/transport'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            this.listTransportation_Datas = res.data.data.map((_, idx) => ({
              id: res.data.data[idx].id,
              show_text: '(' + res.data.data[idx].name + ') ' + res.data.data[idx].num_no,
            }))
            console.log(this.listTransportation_Datas)
          })
          .catch(err => {
            console.log(err)
          })
    },

    editItem(item) {
      this.dialogView = true
      
      this.editedItem = Object.assign({}, item)
      this.editedIndex = this.editedItem.id
      this.itemData()
    },

    viewItem(item) {
      this.dialogView = true

      this.editedIndex = -2
      this.editedItem = Object.assign({}, item)
      this.itemData()
    },

    deleteItem(item) {
      this.dialogDelete = true

      this.editedItem = Object.assign({}, item)
      this.editedIndex = this.editedItem.id
    },

    deleteItemConfirm () {
      this.dialogDelete = true

      let that = this
      if (this.editedIndex > -1) {
        if(this.editedItem != this.defaultItem) {
          that.loadingDelete = true
          // let formData = new FormData();
          let url = 'api/dispatch/' + this.editedItem.id
          // formData.append("ex_file", this.editedItem);
          this.$axios
            .fetchDelete(url, {})
              .then(res => {
                that.loadingDelete = false
                that.closeDelete()
                that.listDatas()
                let result = res.data.data
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
        }
      }
    },

    createView() {
      this.dialogView = true
      this.$nextTick(() => {
        // this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -3
      })
    },

    close () {
      this.dialogView = false
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

    getStatusColor(status) {
      switch (status) {
        case 0:
          return "secondary"
          break;
        case 1:
          return "secondary"
          break;
        case 2:
          return "blue lighten-1"
          break;
        case 3:
          return "orange darken-4"
          break;
        case 3:
          return "orange darken-4"
          break;
        default:
          return "blue-grey lighten-1"
      }
    },

    getStatusText(status) {
      switch (status) {
        case 0:
          return "派單"
          break;
        case 1:
          return "派單"
          break;
        case 2:
          return "製作報告"
          break;
        case 3:
          return "審核"
          break;
        case 4:
          return "通過"
          break;
        default:
          return "未定義"
      }
    },

    save () {
      let that = this
      if (this.editedIndex > -1) {
        console.log(this.editedItem, this.defaultItem)
        if(this.editedItem != this.defaultItem) {
          that.loadingSave = true
          // let formData = new FormData();
          let url = 'api/dispatch/' + this.editedItem.id
          // formData.append("ex_file", this.editedItem);
          this.editedItem.function_path = this.editedItem.function_path.join(',')
          if(!this.editedItem.exam_date) {
            this.editedItem.exam_date = this.editedItem.e_exam_date
          }
          if(!this.editedItem.e_exam_date) {
            this.editedItem.e_exam_date = this.editedItem.exam_date
          }
          this.$axios
            .fetchPut(url, this.editedItem)
              .then(res => {
                that.loadingSave = false
                that.close()
                that.listDatas()
                let result = res.data.data
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
                that.loadingSave = false
                that.close()
                that.$swal.fire({
                  title: "錯誤!",
                  text: "系統錯誤，請稍後重新嘗試",
                  type: "error",
                  confirmButtonClass: "btn btn-danger"
                });
              })
        }
      } else {
        console.log(this.editedItem, this.defaultItem)
        if(this.editedItem != this.defaultItem) {
          that.loadingSave = true
          // let formData = new FormData();
          let url = 'api/dispatch'
          // formData.append("ex_file", this.editedItem);
          this.editedItem.function_path = this.editedItem.function_path.join(',')
          if(!this.editedItem.exam_date) {
            this.editedItem.exam_date = this.editedItem.e_exam_date
          }
          if(!this.editedItem.e_exam_date) {
            this.editedItem.e_exam_date = this.editedItem.exam_date
          }
          this.$axios
            .fetchPost(url, this.editedItem)
              .then(res => {
                that.loadingSave = false
                that.close()
                that.listDatas()
                let result = res.data.data
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
                that.loadingSave = false
                that.close()
                that.$swal.fire({
                  title: "錯誤!",
                  text: "系統錯誤，請稍後重新嘗試",
                  type: "error",
                  confirmButtonClass: "btn btn-danger"
                });
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
