<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="漏電檢測"
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
              v-model="dialogSample"
              max-width="500px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="info"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  上傳樣板
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
                          label="請選擇Word"
                          outlined
                          dense
                          show-size
                          ref="files"
                          v-model="excelFile"
                        ></v-file-input>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col
                        cols="12"
                      >
                        <v-treeview
                          :active.sync="active"
                          :items="logItems"
                          :load-children="fetchSampleLog"
                          :open.sync="open"
                          activatable
                          color="warning"
                          open-on-click
                          transition
                        >
                          <template v-slot:prepend="{ item }">
                            <v-icon v-if="!item.children">
                              mdi-account
                            </v-icon>
                          </template>
                        </v-treeview>
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
                    儲存
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-divider
              class="mx-4"
              inset
              vertical
            ></v-divider>
            <v-dialog
              v-model="dialogNew"
              max-width="800px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  預覽樣本
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
                        <pdf-viewer :pdf_url="pdfUrl"></pdf-viewer>
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
                    關閉
                  </v-btn>
                  <!-- <v-btn
                    color="success"
                    text
                    @click="save"
                  >
                    儲存
                  </v-btn> -->
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog
              v-model="dialogView"
              width="600px"
              fullscreen
              hide-overlay
              persistent
              transition="dialog-bottom-transition"
            >
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
                  <v-toolbar-title>{{ wordTitle }}</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-toolbar-items>
                    <v-btn
                      dark
                      text
                      @click="save"
                    >
                      儲存
                    </v-btn>
                  </v-toolbar-items>
                </v-toolbar>
                <v-container class="grey lighten-5">
                  <!-- row 1 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 2;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測地點／類別
                        </div>
                        <div class="text-caption">
                          Exam. Place/Type
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          v-model="editedItem.exam_place"
                          label="Regular"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 2; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測日期
                        </div>
                        <div class="text-caption">
                          Exam. Date
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <!-- <v-text-field
                          v-model="editedItem.exam_date"
                          label="Regular"
                          :readonly="readonlyVisible"
                        ></v-text-field> -->
                        <v-menu
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                          v-model="menuDatePicker"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="editedItem.exam_date"
                              label="Regular"
                              prepend-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="editedItem.exam_date"
                            @input="menuDatePicker = false"
                          ></v-date-picker>
                        </v-menu>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 2 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 2;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          工單編號
                        </div>
                        <div class="text-caption">
                          Job No
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <!-- <v-card-text> -->
                          <v-row>
                            <v-col cols="12">
                              <v-text-field
                                ref="job_no"
                                v-model="editedItem.job_no"
                                label="Regular"
                                :readonly="readonlyVisible"
                                :rules="[
                                  (v) => !!v || '請填寫此欄位',
                                ]"
                                required
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        <!-- </v-card-text> -->
                      </v-card>
                    </v-col>
                    <v-col style="flex: 2; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          監工
                        </div>
                        <div class="text-caption">
                          Obsert
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                      <!-- <v-card-text> -->
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              ref="obsert"
                              v-model="editedItem.obsert"
                              label="Regular"
                              :readonly="readonlyVisible"
                              :rules="[
                                (v) => !!v || '請填寫此欄位',
                              ]"
                              required
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      <!-- </v-card-text> -->
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 2 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 2;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          工程名稱
                        </div>
                        <div class="text-caption">
                          Project Name 
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <!-- <v-card-text> -->
                          <v-row>
                            <v-col cols="12">
                              <v-text-field
                                ref="job_name"
                                v-model="editedItem.job_name"
                                label="Regular"
                                :readonly="true"
                                :rules="[
                                  (v) => !!v || '請填寫此欄位',
                                ]"
                                required
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        <!-- </v-card-text> -->
                      </v-card>
                    </v-col>
                    <v-col style="flex: 2; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          包覆厚度
                        </div>
                        <div class="text-caption">
                          Thickness
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                      <!-- <v-card-text> -->
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              ref="thickness"
                              v-model="editedItem.thickness"
                              label="Regular"
                              :readonly="readonlyVisible"
                              :rules="[
                                (v) => !!v || '請填寫此欄位',
                              ]"
                              required
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      <!-- </v-card-text> -->
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 2 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 2;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          材質
                        </div>
                        <div class="text-caption">
                          Material 
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              v-model="editedItem.material"
                              label="Regular"
                              :readonly="readonlyVisible"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 2; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          管線編號
                        </div>
                        <div class="text-caption">
                          Pipeline No.
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                      <!-- <v-card-title> -->
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              v-model="editedItem.pipeline_no"
                              label="Regular"
                              :readonly="readonlyVisible"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      <!-- </v-card-title> -->
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 3 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 2; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測儀器
                        </div>
                        <div class="text-caption">
                          Equipement
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              v-model="editedItem.equipement"
                              label="Regular"
                              :readonly="readonlyVisible"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 2;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測方式
                        </div>
                        <div class="text-caption">
                          Testing Method
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              v-model="editedItem.testing_method"
                              label="Regular"
                              :readonly="readonlyVisible"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-col>
                  </v-row>

                  <!-- row 4 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 2; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測電壓
                        </div>
                        <div class="text-caption">
                          Voltage
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              v-model="editedItem.voltage"
                              label="Regular"
                              :readonly="readonlyVisible"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 2;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          電擊形式
                        </div>
                        <div class="text-caption">
                          Type
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 5;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              v-model="editedItem.type"
                              label="Regular"
                              :readonly="readonlyVisible"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-card>
                    </v-col>
                  </v-row>

                  <!-- 表格區 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters>
                    <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                      <v-simple-table id="tab">
                        <template v-slot:default>
                          <thead>
                            <!--  表头 -->
                            <tr>
                              <th class="text-left">項次</th>
                              <th class="text-left">檢測長度<br>Exam. Length.</th>
                              <th class="text-left">缺陷位置<br>Defect Location</th>
                              <th class="text-left">缺陷類別<br>Type of Defect</th>
                              <th class="text-left">缺陷長度<br>Defect Length</th>
                              <th class="text-left">判定結果<br>Result</th>
                              <th class="text-left">備註<br>Remark</th>
                            </tr>
                          </thead>
                          <tbody>
                            <!-- 表格内容 -->
                            <tr v-for="(item, index) in editedItem.items" :key="index">
                              <td><v-text-field
                                :disabled="true"
                                autofocus
                                :value="(index+1)"
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.exam_length"
                                :disabled="readonlyVisible"
                                autofocus
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.defect_location"
                                :readonly="readonlyVisible"
                                autofocus
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.type_of_defect"
                                :readonly="readonlyVisible"
                                autofocus
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.defect_length"
                                :readonly="readonlyVisible"
                                autofocus
                              ></v-text-field></td>
                              <td><v-select
                                  v-model="item.result"
                                  :items="listResult_Datas"
                                  item-text="text"
                                  item-value="value"
                                  dense
                                  single-line
                                  :readonly="readonlyVisible"
                                ></v-select></td>
                              <td><v-text-field
                                v-model="item.remark"
                                :readonly="readonlyVisible"
                                autofocus
                              ></v-text-field></td>
                              <template>
                                <!-- 按钮区域 -->
                              <td>
                                <template v-if="!readonlyVisible">
                                  <!-- 修改界面显示取消 -->
                                  <v-btn rounded color="success" dark small v-if="editedItem.items && index == editedItem.items.length - 1" @click="insertEditItems()">插入下一行</v-btn>
                                  <!-- 修改界面显示取消 -->
                                  <v-btn rounded color="error" dark small v-else @click="deleteEditItems(item)">刪除</v-btn>
                                </template>
                              </td>
                            </template>
                            </tr>
                          </tbody>
                        </template>
                      </v-simple-table>
                    </v-card>
                  </v-row>
                  <v-row
                    align="center"
                    justify="space-around"
                    class="mb-4"
                  >
                    <v-btn
                      tile
                      :dark="exportFinalPath != null"
                      :disabled="exportFinalPath == null"
                      color="blue darken-1"
                      @click="saveExport"
                    >
                      <v-icon left>
                        mdi-file-word-outline
                      </v-icon>
                      報告匯出
                    </v-btn>
                    <v-btn
                      tile
                      :dark="!(editedDispatchStatus != 2)"
                      color="red darken-1"
                      :disabled="editedDispatchStatus != 2"
                      @click="saveVerify"
                    >
                      <v-icon left>
                        mdi-send-lock
                      </v-icon>
                      提交審核
                    </v-btn>
                    <v-btn
                      tile
                      :dark="!(editedDispatchStatus != 3)"
                      color="green darken-1"
                      :disabled="editedDispatchStatus != 3"
                      @click="saveDoubleVerify"
                    >
                      <v-icon left>
                        mdi-checkbox-marked-circle-outline
                      </v-icon>
                      通過審核
                    </v-btn>
                  </v-row>
                </v-container>
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
            <v-tooltip  v-if="item.status == 0 || item.status == 1 || item.status == 2" top>
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
              <span>製作報告</span>
            </v-tooltip>
            <v-tooltip v-if="item.status == 3" top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="orange darken-4"
                    x-small
                    dark
                    class="mx-2"
                    fab
                    @click="editItem(item)"
                    v-bind="attrs"
                    v-on="on"
                  >
                  <v-icon dark>mdi-card-search-outline</v-icon>
                </v-btn>
              </template>
              <span>審核報告</span>
            </v-tooltip>
          </div>
        </template>
        <template v-slot:no-data>
          <v-btn
            color="primary"
            @click="initialize"
          >
            重新整理
          </v-btn>
        </template>
        <template v-slot:[`item.status`]="{ item }">
          <DataTableStatus :item="item"></DataTableStatus>
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
import PdfViewer from '../../../components/PdfViewer.vue'
import DataTableStatus from '../../../components/DataTableStatus.vue'
const SAMPLE_PDF_PATH = 'chimei_it'
const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
export default {
  name: 'ITchiView',

  middleware: 'auth',

  components: {
    PdfViewer,
    DataTableStatus
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
    sampleItems: [],
    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    dialogNew: false,
    dialogView: false,
    dialogDelete: false,
    dialogSample: false,
    loading: false,
    loadingSave: false,
    dataItems: [],
    selectItem: [],
    listRTPT_Datas: [],
    listThickness_Datas: [],
    selectThickness: {},
    selectNominalSize: {},
    editedIndex: -1,
    editedDispatchStatus: false,
    exportFinalPath: null,
    excelFile: null,
    pdfUrl: '',
    listResult_Datas:[
      {text: '合格', value: 0},
      {text: '不合格', value: 1},
    ],
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
    menuDatePicker: false,
    editedIndex: -1,
    editedItem: {
      id: 0,
      job_name: '',
      job_no: '',
      exam_date: new Date,
      exam_place: '',
      exam_type: '',
      material: '',
      pipeline_no: '',
      equipement: '',
      testing_method: '',
      thickness: '',
      voltage: '',
      obsert: '',
      type: '',
      items:[
        {
          exam_length: '',
          defect_location: '',
          type_of_defect: '',
          defect_length: '',
          result: '',
          remark: '',
        }
      ],
    },
    defaultItem: {
      id: 0,
      job_name: '',
      job_no: '',
      exam_date: new Date,
      exam_place: '',
      exam_type: '',
      material: '',
      pipeline_no: '',
      equipement: '',
      testing_method: '',
      thickness: '',
      voltage: '',
      obsert: '',
      type: '',
      items:[
        {
          exam_length: '',
          defect_location: '',
          type_of_defect: '',
          defect_length: '',
          result: '',
          remark: '',
        }
      ],
    },
  }),

  computed: {
    formTitle () {
      return '漏電檢測報告樣板'
    },
    wordTitle () {
      return '漏電檢測報告書'
    },
    ...mapGetters({
      user: 'auth/user'
    }),
    logItems() {
      return [
        {
          name: '歷史紀錄',
          children: this.sampleItems,
        },
      ]
    },
    readonlyVisible () {
      return this.editedIndex == -2
    },

    mThincknessNomalSize() {
      if(Object.keys(this.selectThickness).length !== 0 && Object.keys(this.selectNominalSize).length !== 0) {
        console.log(this.selectNominalSize)
        return /*this.selectNominalSize.nominal_size + ', ' + this.selectThickness.thickness + ', ' + */this.selectNominalSize[this.selectThickness.thickness]
      }
    },
  },

  methods: {
    initialize () {
      this.listDatas()
      this.activeSample()
      // this.getRTPT_Data()
    },
    listDatas() {
      this.loading = true
      let co = SAMPLE_PDF_PATH.split('_');
      let url = `api/dispatch/${co[0]}/${co[1]}/`
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data)
            this.dataItems = res.data.data.map((_, idx) => ({
                job_name: res.data.data[idx].job_name,
                customer: res.data.data[idx].customer,
                exam_date: res.data.data[idx].exam_date,
                exam_place: res.data.data[idx].exam_place,
                status: res.data.data[idx].status,
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
      let co = SAMPLE_PDF_PATH.split('_');
      let url = `api/dispatch/${co[0]}/${co[1]}/` + this.editedItem.id
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            this.exportFinalPath = res.data.data.export_final_path
            if(res.data.data.report_it) {
              this.editedItem = Object.assign({}, res.data.data.report_it)
              // this.selectThickness = {thickness: this.editedItem.thicknes, value: this.editedItem.thicknes}
            } else {
              let tmp = Object.assign({}, this.editedItem)
              this.editedItem = Object.assign({}, this.defaultItem)
              this.editedItem.exam_date = tmp.exam_date
              this.editedItem.exam_place = tmp.exam_place
              this.editedItem.job_name = tmp.job_name
              this.editedItem.dispatch_id = tmp.id
            }
            this.getRTPT_Data()
            console.log(this.editedItem)
          })
          .catch(err => {
            console.log(err)
          })
    },

    postSample() {
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/sample/${co[0]}/${co[1]}/`
      let formData = new FormData();
      formData.append("ex_file", this.excelFile);
      this.$axios
        .fetchUpload(url, formData)
          .then(res => {
            // console.log(res.data)
            let _res = res.data.data
            if(Array.isArray(_res)) {
              if(_res[0]) {
                that.pdfUrl = this.$base_url + '/' + _res[0].full_path
              }
            } else {
              if(_res.full_path) {
                that.pdfUrl = this.$base_url + '/' + _res.full_path
              }
            }
            
            this.closeSample()
          })
          .catch(err => {
            console.log(err)
          })
    },

    activeSample() {
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/sample/${co[0]}/${co[1]}`
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data)
            if(res.data.data.full_path) {
              that.pdfUrl = that.$base_url + '/' + res.data.data.full_path
            }
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

    insertEditItems() {
      if(this.editedItem.items && this.editedItem.items.length > 0) {
        let tmpDefault = Object.assign({}, this.defaultItem.items[0])
        tmpDefault.object_no = this.editedItem.items.length + 1
        console.log(tmpDefault)
        this.editedItem.items.push(tmpDefault)
        this.editedItem.items.forEach((element, index) => {
          this.editedItem.items[index].object_no = (index+1)
        })
      }
    },

    getRTPT_Data() {
      let that = this
      let url = 'api/data/rtpr_ness/lately'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            that.listRTPT_Datas = res.data.data.slave
            res.data.data.slave.forEach((element) => {
              if(element.nominal_size == that.editedItem.nominal_size) {
                that.selectNominalSize = element
              }
              Object.entries(element).forEach(([key, value]) => {
                // console.log(key, value)
                if(key.startsWith('sch') || key.startsWith('std')) {
                  let obj = {thickness: key, value: key}
                  that.listThickness_Datas.push(obj)
                }
              })
              
              return
            })
          })
          .catch(err => {
            console.log(err)
          })
    },

    deleteEditItems (item) {
      let x = this.editedItem.items.indexOf(item)
      this.editedItem.items.splice(x, 1);
      this.editedItem.items.forEach((element, index) => {
        this.editedItem.items[index].object_no = (index+1)
      })
    },

    editItem(item) {
      this.dialogView = true

      this.editedIndex = this.dataItems.indexOf(item)
      this.editedDispatchStatus = item.status
      this.editedItem = Object.assign({}, item)
      this.itemData()
    },

    viewItem(item) {
      this.dialogView = true

      this.editedDispatchStatus = item.status
      this.editedIndex = -2
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

    async fetchSampleLog(item) {
      await pause(1500)
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/sample/${co[0]}/${co[1]}?q=all`
      let params = {}
      return this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data)
            let _res = res.data.data
            if(Array.isArray(_res)) {
              if(_res[0]) {
                // console.log(_res)
                let m = _res.map((_, idx) => ({
                  name: _res[idx].created_user + "(" +_res[idx].created_at+ ")",
                  id: _res[idx].id
                }))
                // console.log(m) 
                item.children.push(...m)
              }
            } 
          })
          .catch(err => {
            console.log(err)
          })
    },

    closeSample () {
      this.dialogSample = false
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

    saveExport() {
      if(!this.editedItem.dispatch_id) return 
      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch_download/${co[0]}/${co[1]}/` + this.editedItem.dispatch_id
      this.$swal.fire({
        title: '確定要匯出報告?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          return this.$axios
            .fetchPost(url, {})
            .then(res => {
              return res.data
            })
            .catch(err => {
              console.log(err)
            })
        },
        allowOutsideClick: () => !this.$swal.isLoading()
      }).then((result) => {
        try {
          if(result.value.code == 1000) {
            window.open(result.value.data, '_blank')
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: result.value.message,
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
          }
        } catch {

        }
      })
    },

    saveDoubleVerify() {
      if(!this.editedItem.dispatch_id) return 
      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch_double_verify/${co[0]}/${co[1]}/` + this.editedItem.dispatch_id
      this.$swal.fire({
        title: '確定要通過審核?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          params.nominal_size = that.selectNominalSize.nominal_size
          params.thicknes = that.selectThickness.thickness
          return this.$axios
            .fetchPut(url, params)
            .then(res => {
              return res.data.data
            })
            .catch(err => {
              console.log(err)
            })
        },
        allowOutsideClick: () => !this.$swal.isLoading()
      }).then((result) => {
        try {
          if(result.value.code == 1000) {
              that.$swal.fire({
                title: "成功!",
                text: "儲存數據成功",
                type: "success",
                confirmButtonClass: "btn btn-success"
              });
              this.close()
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "儲存數據錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              this.close()
          }
        } catch {

        }
      })
    },

    saveVerify() {
      console.log(this.editedItem)
      if(!this.editedItem.dispatch_id) return 
      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch_verify/${co[0]}/${co[1]}/` + this.editedItem.dispatch_id
      this.$swal.fire({
        title: '確定要提交審核?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          params.nominal_size = that.selectNominalSize.nominal_size
          params.thicknes = that.selectThickness.thickness
          return this.$axios
            .fetchPut(url, params)
            .then(res => {
              return res.data.data
            })
            .catch(err => {
              console.log(err)
            })
        },
        allowOutsideClick: () => !this.$swal.isLoading()
      }).then((result) => {
          if(result.value.code == 1000) {
              that.$swal.fire({
                title: "成功!",
                text: "儲存數據成功",
                type: "success",
                confirmButtonClass: "btn btn-success"
              });
              this.closeView()
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "儲存數據錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              this.closeView()
          }
          
      })
    },

    save () {
      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch/${co[0]}/${co[1]}` + (this.editedItem.id === -1? '' : ('/'+this.editedItem.id))
      // let url = `api/dispatch/${co[0]}/${co[1]}/` + this.editedItem.id
      this.$swal.fire({
        title: '確定要儲存數據?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          params.nominal_size = that.selectNominalSize.nominal_size
          params.thicknes = that.selectThickness.thickness
          return this.$axios
            .fetchPut(url, params)
            .then(res => {
              return res.data.data
            })
            .catch(err => {
              console.log(err)
            })
        },
        allowOutsideClick: () => !this.$swal.isLoading()
      }).then((result) => {
          if(result.value.code == 1000) {
              that.$swal.fire({
                title: "成功!",
                text: "儲存數據成功",
                type: "success",
                confirmButtonClass: "btn btn-success"
              });
              this.closeView()
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "儲存數據錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              this.closeView()
          }
          
      })
    },

    saveSample () {
      this.closeSample()
    },

    
  }
}
</script>

<style lang="scss" scope>
.v-file-input__text {
  cursor: pointer;
}
</style>
