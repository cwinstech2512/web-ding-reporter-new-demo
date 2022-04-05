<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="射線檢測(RT)"
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
            <v-toolbar-title>射線檢測(RT)</v-toolbar-title>
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
              v-if="authRoleVaild('upload')"
            ></v-divider>
            <v-dialog
              v-model="dialogNew"
              max-width="800px"
              v-if="authRoleVaild('upload')"
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
            <ThicknessChoice
              @saveMThinckness="saveMThinckness"
              @toggleDialog="toggleDialog"
              :channalType="'chimei_rt'"
              :thicknesGroup_Data="thicknesGroup_Data"
              :thicknesGroupModel="thicknesGroupModel"
              :dialog="dialogThicknessChoice" 
              :listRTPT_Datas="listRTPT_Datas"
              :listThickness_Datas="listThickness_Datas">
            </ThicknessChoice>
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
                  <v-toolbar-title>{{ editedItem.original_no }} 的列表</v-toolbar-title>
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
                          檢測人員
                        </div>
                        <div class="text-caption">
                          Inspected By
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="editedItem.obsert"
                                :readonly="readonlyVisible"
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-card-title>
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
                          工單編號
                        </div>
                        <div class="text-caption">
                          Job No.
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
                        <v-card-title>
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
                        </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>

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
                          照射方法
                        </div>
                        <div class="text-caption">
                          Radiographic Tech.
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12">
                              <v-select
                                v-model="editedItem.radiographic_tech"
                                :items="listRadiographicTech_Datas"
                                label="請選擇"
                                item-text="text"
                                item-value="value"
                                dense
                                multiple
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
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
                        <v-text-field
                          v-model="editedItem.material"
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
                          層數
                        </div>
                        <div class="text-caption">
                          Thicknes
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
                      <v-card-title>
                        <v-row>
                          <v-col cols="12" md="12">
                            <v-select
                              v-model="editedItem.layers"
                              :items="listLayer_Datas"
                              label="層數"
                              item-text="text"
                              item-value="value"
                              dense
                              single-line
                              :disabled="readonlyVisible"
                            ></v-select>
                          </v-col>
                        </v-row>
                      </v-card-title>
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
                          射源/廠牌型號<br>序號
                        </div>
                        <div class="text-caption">
                          Radiation/Brand<br>SN
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
                          v-model="editedItem.sn"
                          label="Regular"
                          :readonly="readonlyVisible"
                        ></v-text-field>
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
                          底片種類
                        </div>
                        <div class="text-caption">
                          Film Type
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12">
                              <v-text-field
                                v-model="editedItem.film_type"
                                label="Regular"
                                :readonly="true"
                              ></v-text-field>
                            </v-col>
                          </v-row>
                      </v-card-title>
                        <!-- <v-text-field
                          v-model="editedItem.film_type"
                          label="Regular"
                          :readonly="readonlyVisible"
                        ></v-text-field> -->
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
                          射源強度
                        </div>
                        <div class="text-caption">
                          Energy/Strength
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12" md="6">
                              <v-select
                                v-model="selectDL"
                                :items="listDL_Datas"
                                label="請選擇編號"
                                item-text="identify"
                                item-value="identify"
                                dense
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="editedItem.energy_strength"
                                label="射源強度"
                                :readonly="true"
                                dense
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-card-title>
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
                          底片廠牌
                        </div>
                        <div class="text-caption">
                          Film Brand
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12">
                              <v-select
                                v-model="editedItem.film_brand"
                                :items="listFilmBrand_Datas"
                                label="請選擇(Regular)"
                                item-text="text"
                                item-value="value"
                                dense
                                :readonly="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 5 -->
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
                          焦點尺寸
                        </div>
                        <div class="text-caption">
                          Focal Spot
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
                          v-model="editedItem.focal_spot"
                          label="γ–Ray"
                          :readonly="true"
                          suffix="mm"
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
                          像質計編號
                        </div>
                        <div class="text-caption">
                          IQI No.
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12">
                              <v-select
                                v-model="editedItem.iqi_no"
                                :items="listIqiNo_Datas"
                                label="ASTM"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 6 -->
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
                          射源試件距離
                        </div>
                        <div class="text-caption">
                          S. O. D
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
                          v-model="editedItem.sod"
                          label="Regular"
                          :readonly="true"
                          suffix="mm"
                        ></v-text-field>
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
                          幾何模糊度
                        </div>
                        <div class="text-caption">
                          Geometric Unsharpness
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
                          v-model="editedItem.geometric_unsharpness"
                          label="Regular"
                          :readonly="true"
                          suffix="mm"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 7 -->
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
                          曝光時間
                        </div>
                        <div class="text-caption">
                          Exposure Time
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
                          label="Regular"
                          :readonly="true"
                          suffix="Min.Sec."
                          v-model="editedItem.exposure_time"
                        ></v-text-field>
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
                          像質計位置
                        </div>
                        <div class="text-caption">
                          IQI/Location Mark
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
                        <v-select
                          v-model="editedItem.iqi_location_mark"
                          :items="listiQiMark_Datas"
                          label="像質計位置"
                          item-text="text"
                          item-value="value"
                          dense
                          single-line
                          :disabled="readonlyVisible"
                        ></v-select>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 9 -->
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
                          底片判讀方式
                        </div>
                        <div class="text-caption">
                          Interpret
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
                      <v-card-title>
                          <v-row>
                            <v-col cols="12">
                              <v-select
                                v-model="editedItem.interpret"
                                :items="listInterpret_Datas"
                                label="請選擇"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                                :disabled="readonlyVisible"
                              ></v-select>
                              <v-select
                                v-if="editedItem.interpret === 0 || editedItem.interpret === 1"
                                v-model="editedItem.sin_dou"
                                :items="listSinDou_Datas[editedItem.interpret]"
                                label="請選擇"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                      </v-card-title>
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
                          底片處理
                        </div>
                        <div class="text-caption">
                          Film Processing
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="editedItem.film_processing_temp"
                                label="溫度"
                                :readonly="true"
                                suffix="°C"
                              ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="editedItem.film_processing_min"
                                label="顯影時間"
                                :readonly="true"
                                suffix="min"
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 8 -->
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
                          底片大小及數量
                        </div>
                        <div class="text-caption">
                          Film Size & Q’ty
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
                        <v-card-title>
                          <v-row>
                            <v-col cols="12" md="6">
                              <v-select
                                v-model="editedItem.film_size"
                                :items="listFileSize_Datas"
                                label="大小"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                                multiple
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                              <v-select
                                v-model="editedItem.file_qty"
                                :items="listQty_Datas"
                                label="數量"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
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
                      <v-card-text>
                        <v-slide-group
                          v-model="thicknesGroupModel"
                          class="pa-4"
                          mandatory
                          show-arrows
                        >
                          <v-slide-item
                            v-for="n in thicknesGroup_Data.length + 1"
                            :key="n"
                            v-slot="{ active, toggle }"
                          >
                            <v-card
                              v-if="n != thicknesGroup_Data.length + 1"
                              :color="active ? 'blue darken-3' : 'grey lighten-1'"
                              class="ma-4"
                              height="100"
                              @click="toggle"
                            >
                              <v-app-bar
                                flat
                                color="rgba(0, 0, 0, 0)"
                                height="12"
                              >
                                <v-spacer></v-spacer>

                                <v-btn
                                  color="white"
                                  icon
                                  height="10"
                                  width="10"
                                  @click="deleteThickness(thicknesGroup_Data[n-1])"
                                >
                                  <v-icon size="8" color="white">mdi-close-thick</v-icon>
                                </v-btn>
                              </v-app-bar>
                              <v-card-text
                                :class="active ? 'white--text' : ''"
                              >
                                {{thicknesGroup_Data[n-1].selectThickness.thickness.toUpperCase()}}
                              <v-divider class="mx-2"></v-divider>
                                {{thicknesGroup_Data[n-1].selectNominalSize.nominal_size}}
                              </v-card-text>
                            </v-card>
                            <v-card
                              v-else
                              :color="active ? 'deep-purple accent-4' : 'grey lighten-1'"
                              class="ma-4"
                              height="100"
                              width="60"
                              @click="addDialogThickness"
                            >
                              <v-row
                                class="fill-height"
                                align="center"
                                justify="center"
                              >
                                <v-icon
                                  :class="active ? 'white--text' : ''"
                                  size="24"
                                  v-text="'mdi-plus-circle-multiple-outline'"
                                ></v-icon>
                              </v-row>
                            </v-card>
                          </v-slide-item>
                        </v-slide-group>
                        <!-- <v-chip-group
                          v-model="selection"
                          mandatory
                          active-class="deep-purple accent-4 white--text"
                          column
                        >
                          <v-chip>2"<v-divider class="mx-2"></v-divider>std</v-chip>

                          <v-chip>2 1/2"</v-chip>

                          <v-chip>3"</v-chip>
                        </v-chip-group> -->
                      </v-card-text>
                      <v-simple-table>
                        <template v-slot:default>
                          <thead>
                            <!--  表头 -->
                            <tr>
                              <th class="text-left">項次</th>
                              <th class="text-left">底片<br>或銲道編號<br>Film or Weld No.</th>
                              <th class="text-left">母材厚度<br>(mm)<br>Thickness</th>
                              <th class="text-left">銲工編號<br>Welder No.</th>
                              <th class="text-left">底片靈敏度<br>Sensitivity</th>
                              <th class="text-left">缺陷代號<br>Defect Code</th>
                              <th class="text-left">判定結果<br>Result</th>
                              <th class="text-left">備註<br>Remark</th>
                            </tr>
                          </thead>
                          <draggable v-if="editedItem.items" v-model="editedItem.items[thicknesGroupModelChoice]" tag="tbody" :sort="!readonlyVisible">
                            <!-- 表格内容 -->
                            <tr v-for="(item, index) in editedItem.items[thicknesGroupModelChoice]" :key="index">
                              <td><v-text-field
                                :disabled="true"
                                outlined
                                dense
                                :value="(index+1)"
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.film_or_weld_no"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.thickness"
                                :readonly="true"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.welder_no"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.sensitivity"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.defect_code"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td>
                                <v-select
                                  v-model="item.result"
                                  :items="listResult_Datas"
                                  item-text="text"
                                  item-value="value"
                                  dense
                                  outlined
                                  single-line
                                  :readonly="readonlyVisible"
                                ></v-select>
                              </td>
                              <td><v-text-field
                                v-model="item.remark"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <template>
                                <!-- 按钮区域 -->
                              <td>
                                <template v-if="!readonlyVisible">
                                  <!-- 修改界面显示取消 -->
                                  <v-btn rounded color="success" dark small v-if="editedItem.items && index == editedItem.items[thicknesGroupModelChoice].length - 1" @click="insertEditItems(thicknesGroupModelChoice)">插入下一行</v-btn>
                                  <!-- 修改界面显示取消 -->
                                  <v-btn rounded color="error" dark small v-else @click="deleteEditItems(item)">刪除</v-btn>
                                </template>
                              </td>
                            </template>
                            </tr>
                          </draggable>
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
                      :dark="!(!authRoleVaild('vaild') || editedDispatchStatus != 3)"
                      color="red darken-1"
                      :disabled="!authRoleVaild('vaild') || editedDispatchStatus != 3"
                      v-if="!(!authRoleVaild('vaild') || editedDispatchStatus != 3)"
                      @click="saveVerifyBack"
                    >
                      <v-icon left>
                        mdi-account-remove-outline
                      </v-icon>
                      退回審核
                    </v-btn>
                    <v-btn
                      tile
                      :dark="!(!authRoleVaild('vaild') || editedDispatchStatus != 3)"
                      color="green darken-1"
                      :disabled="!authRoleVaild('vaild') || editedDispatchStatus != 3"
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
import ThicknessChoice from '../../../components/ThicknessChoice.vue'
import draggable from "vuedraggable"
import moment from 'moment'
const SAMPLE_PDF_PATH = 'chimei_rt'
const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
export default {
  name: 'RTchiView',

  middleware: 'auth',

  components: {
    PdfViewer,
    DataTableStatus,
    draggable,
    ThicknessChoice
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
    dialogNew: false,
    dialogView: false,
    dialogDelete: false,
    dialogSample: false,
    dialogThicknessChoice: false,
    loading: false,
    loadingSave: false,
    thicknesGroup_Data: [],
    thicknesGroupModel: -1,
    thicknesGroupModelChoice: 0,
    dataItems: [],
    selectItem: [],
    objDL_Data: {
      data_value: null
    },
    mThincknessNomalSize: null,
    listDL_Datas: [],
    listRTPT_Datas: [],
    listThickness_Datas: [],
    listFilmBrand_Datas: [
      {text: 'Agfa D4', value: '1'},
      {text: 'Agfa D5', value: '1.5'},
      {text: 'Agfa D7', value: '3'},
    ],
    listLayer_Datas: [
      {text: '1層', value: 1},
      {text: '2層', value: 2},
    ],
    listFileSize_Datas: [
      {text: '4 1/2 in.×8 1/2 in.', value: '0'},
      {text: '3 1/3 in.× 6 in.', value: '1'},
    ],
    listQty_Datas:[
      {text: '單 Single', value: '0'},
      {text: '雙 Double', value: '1'},
    ],
    listIqiNo_Datas:[
      {text: '1A', value: 0},
      {text: '1B', value: 1},
    ],
    listInterpret_Datas:[
      {text: '單壁判讀Single View', value: 0},
      {text: '雙壁判讀Double View', value: 1},
    ],
    listRadiographicTech_Datas:[
      {text: '單層壁Single Wall Exposure', value: 0},
      {text: '雙層壁Double Wall Exposure', value: 1},
    ],
    listiQiMark_Datas:[
      {text: '射源側 Source Side', value: 0},
      {text: '底片側 Film Side', value: 1},
    ],
    listResult_Datas:[
      {text: '合格', value: 0},
      {text: '不合格', value: 1},
    ],
    listSinDou_Datas: [
      [
        {text: '單張照相', value: 0},
        {text: '中心照相', value: 1},
      ],
      [
        {text: '管徑2 1/2以下', value: 0},
        {text: '管徑3以上', value: 1},
      ],
    ],
    selectThickness: {},
    selectNominalSize: {},
    selectDL: null,
    excelFile: null,
    pdfUrl: '',
    headers: [
      {
        text: '報告編號',
        align: 'start',
        value: 'original_no',
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
    editedDispatchStatus: false,
    exportFinalPath: null,
    editedItem: {
      original_no: '',
      id: 0,
      dispatch_id: '',
      job_name: '',
      job_no: '',
      exam_date: new Date,
      exam_place: '',
      material: '',
      thicknes: '',
      layers: '',
      nominal_size: '',
      radiographic_tech: '',
      identify: '',
      film_type: '', sn: '', film_brand: '', film_size: '', focal_spot: '', file_qty: '', sod: '', geometric_unsharpness: '', 
      exposure_time: '', iqi_location_mark: '', iqi_no: '', interpret: 0, film_processing_temp: '', film_processing_min: '',
      energy_strength: '', sin_dou: 0, thickness_objs: [],
      obsert: '',
      items:[
        [
          {
            object_no: '',
            film_or_weld_no: '',
            thickness: '',
            welder_no: '',
            sensitivity: '',
            defect_code: '',
            result: '',
            remark: '',
          }
        ]
      ],
    },
    defaultItem: {
      original_no: '',
      id: 0,
      dispatch_id: '',
      job_name: '',
      job_no: '',
      exam_date: new Date,
      exam_place: '',
      material: '',
      thicknes: '',
      layers: '',
      nominal_size: '',
      radiographic_tech: '',
      identify: '',
      film_type: '', sn: '', film_brand: '', film_size: '', focal_spot: '', file_qty: '', sod: '', geometric_unsharpness: '', 
      exposure_time: '', iqi_location_mark: '', iqi_no: '', interpret: 0, film_processing_temp: '25', film_processing_min: '5',
      energy_strength: '', sin_dou: 0, thickness_objs: [],
      obsert: '',
      items:[
        [
          {
            object_no: '',
            film_or_weld_no: '',
            thickness: '',
            welder_no: '',
            sensitivity: '',
            defect_code: '',
            result: '',
            remark: '',
          }
        ]
      ],
    },
  }),

  computed: {
    formTitle () {
      return '射線檢測報告樣板'
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

    // mThincknessNomalSize() {
    //   if(Object.keys(this.selectThickness).length !== 0 && Object.keys(this.selectNominalSize).length !== 0) {
    //     console.log(this.selectNominalSize)
    //     return /*this.selectNominalSize.nominal_size + ', ' + this.selectThickness.thickness + ', ' + */this.selectNominalSize[this.selectThickness.thickness]
    //   }
    // },
  },

  watch: {
    thicknesGroupModel (val, oldVal) {
      // if(val == this.thicknesGroup_Data.length) {
      //   this.dialogThicknessChoice = true
      if(val === oldVal) {
        return
      }
      if(val >= this.thicknesGroup_Data.length) return
      this.thicknesGroupModelChoice = val
    },
    selectDL (val, oldVal) {
      if (val === oldVal) return
      this.getDL_Data()
    },
    mThincknessNomalSize(val, oldVal) {
      if (val === oldVal) return
      // this.roleCalcFOG()
      // this.editedItem.items.forEach(element => {
      //   element.thickness = val
      // })
      // this.roleCalcSod()
      // this.roleCalcExTime()
    },
    'editedItem.radiographic_tech'(val, oldVal) {
      if (val === oldVal) return
      // this.roleCalcSod()
      // this.roleCalcExTime()
    },
    'editedItem.sin_dou'(val, oldVal) {
      if (val === oldVal) return
      // this.roleCalcSod()
      // this.roleCalcExTime()
    },
    'editedItem.film_brand'(val, oldVal) {
      // console.log(val, oldVal)
      if (val === oldVal) return
      // this.roleCalcSod()
      // this.roleCalcExTime()
      if(val == '1' || val == '1.5')
        this.editedItem.film_type = 'Class I'
      else
        this.editedItem.film_type = 'Class II'
    }
  },

  methods: {
    initialize () {
      this.listDatas()
      this.activeSample()
      this.getListDL_Data()
      this.getPT_Data()
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
                id: res.data.data[idx].id,
                report_rt: res.data.data[idx].report_rt,
                original_no: res.data.data[idx].report_rt? res.data.data[idx].report_rt.original_no : ''
              }))
            this.loading = false
          })
          .catch(err => {
            console.log(err)
            this.loading = false
          })
    },
    
    authRoleVaild (key) {
      let co = SAMPLE_PDF_PATH.split('_');
      return this.user.menu.menus.find(e => Object.values(e.menu_info).find((obj) => {
        // console.log((co[0] + '_'+ key))
        return obj == ('/' + co[0] + '_'+ key + '/')
        }))
    },

    itemData() {
      let co = SAMPLE_PDF_PATH.split('_');
      let url = `api/dispatch/${co[0]}/${co[1]}/` + this.editedItem.id
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            this.exportFinalPath = res.data.data.export_final_path
            if(res.data.data.report_rt) {
              this.editedItem = Object.assign({}, res.data.data.report_rt)
              this.editedItem.film_size = this.editedItem.film_size?.split(',')
              this.editedItem.radiographic_tech = this.editedItem.radiographic_tech?.split(',')
              // if(!res.data.data.report_rt.items || res.data.data.report_rt.items.length < 1) {
              //   this.editedItem.items = Object.assign([], this.defaultItem.items)
              // }
            } else {
              let tmp = Object.assign({}, this.editedItem)
              this.editedItem = JSON.parse(JSON.stringify(this.defaultItem))
              this.editedItem.items = null
              this.editedItem.exam_date = tmp.exam_date
              this.editedItem.exam_place = tmp.exam_place
              this.editedItem.dispatch_id = tmp.id
            }

            if(!this.editedItem.film_processing_temp || this.editedItem.film_processing_temp.length == 0) this.editedItem.film_processing_temp = this.defaultItem.film_processing_temp
            if(!this.editedItem.film_processing_min || this.editedItem.film_processing_min.length == 0) this.editedItem.film_processing_min = this.defaultItem.film_processing_min
              
            this.selectThickness = {thickness: this.editedItem.thicknes, value: this.editedItem.thicknes}
            this.getRTPT_Data()
            this.getDL_Data()
            this.selectDL = this.editedItem.identify
            this.thicknesGroup_Data = this.editedItem.thickness_objs? this.editedItem.thickness_objs : []
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
      var choiceIndex = this.thicknesGroupModelChoice
      if(choiceIndex < 0) return
      if(this.editedItem.items && this.editedItem.items[choiceIndex] && this.editedItem.items[choiceIndex].length > 0) {
        let _ = this.editedItem.items
        let tmpDefault = Object.assign({}, _[choiceIndex][0])
        let newArr = Object.assign([], _)
        tmpDefault.object_no = newArr[choiceIndex].length + 1
        console.log(tmpDefault)
        newArr[choiceIndex].push(tmpDefault)
        newArr[choiceIndex].forEach((element, index) => {
          newArr[choiceIndex][index].object_no = (index+1)
        })
        this.editedItem.items = newArr
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

    getPT_Data() {
      let that = this
      let url = 'api/data/rt_ness/lately'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            // console.log(res.data.data)
            that.listRT_Datas = res.data.data.slave
          })
          .catch(err => {
            console.log(err)
          })
    },

    getDL_Data() {
      if(!this.selectDL) {
        return
      }
      const dl = this.listDL_Datas.find(element => element.identify == this.selectDL)
      this.editedItem.focal_spot = Math.round((dl.diagonal * 25.4 + Number.EPSILON) * 10) / 10
      this.editedItem.identify = this.selectDL
      let that = this
      let url = 'api/import/dialength/search'
      let params = {date: moment().format('Y-M-D'), select_id: dl.id}
      this.$axios
        .fetchPost(url, params)
          .then(res => {
            if(res.data.data) {
              that.objDL_Data = Object.assign({}, res.data.data)
              that.editedItem.energy_strength = Math.round((that.objDL_Data.data_value) * 10) / 10
            } else {
              that.$swal.fire({
                title: "錯誤!",
                text: " 請更新數據！",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
            }
          })
          .catch(err => {
            console.log(err)
          })
    },

    getListDL_Data() {
      let that = this
      let url = 'api/import/dialength/search'
      let params = {}
      this.$axios
        .fetchPost(url, params)
          .then(res => {
            if(res.data.data) {
              that.listDL_Datas = res.data.data
            } else {
              that.$swal.fire({
                title: "錯誤!",
                text: " 請更新數據！",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
            }
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
      this.listDatas()
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
                text: result.value.data.message,
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
              that.closeView()
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "儲存數據錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              that.closeView()
          }
          
      })
    },

    save () {
      let params = this.editedItem
      console.log(params)
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch/${co[0]}/${co[1]}` + (this.editedItem.id === -1? '' : ('/'+this.editedItem.id))
      this.$swal.fire({
        title: '確定要儲存數據?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          params.nominal_size = that.selectNominalSize.nominal_size
          params.thicknes = that.selectThickness.thickness
          params.film_size = that.editedItem.film_size.join(',')
          params.radiographic_tech = that.editedItem.radiographic_tech.join(',')
          params.thickness_objs = that.thicknesGroup_Data
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
              that.closeView()
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "儲存數據錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              that.closeView()
          }
          
      })
    },

    saveSample () {
      this.closeSample()
    },

    formateExTime(time) {
      var min = 0
      var sec = 0
      if(time) {
        if(time > 1) {
          min = parseInt(time)
        }
        // console.log(time)
        sec = Math.round((time-min) * 60)
      }
      
      return min + " 分 " + sec + " 秒"
    },

    roleCalcFOG(obj = null) {
      if(obj == null) {
        if(this.editedItem.radiographic_tech !== '') {
          //單壁照相
          if(this.editedItem.radiographic_tech == 0) {
            if(this.mThincknessNomalSize && this.mThincknessNomalSize != 0.0) {
              this.editedItem.geometric_unsharpness = Math.round((2.9 * this.mThincknessNomalSize / 330) * 100) / 100
            }
          }
          //雙壁照相
          else if(this.editedItem.radiographic_tech == 1) {
            if(this.editedItem.sod && this.selectNominalSize) {
              if(this.editedItem.sin_dou == 0) {
                this.editedItem.geometric_unsharpness = Math.round((3 * this.selectNominalSize.od_out / this.mThincknessNomalSize) * 100) / 100
              } else if(this.editedItem.sin_dou == 1) {
                this.editedItem.geometric_unsharpness = Math.round((3 * this.selectNominalSize.od_out / this.mThincknessNomalSize) * 100) / 100
              }
            }
          }
        }
      } else {
        if(obj.radiographic_tech !== '') {
          //單壁照相
          if(obj.radiographic_tech == 0) {
            if(obj.mThincknessNomalSize && obj.mThincknessNomalSize != 0.0) {
              this.editedItem.geometric_unsharpness += (";" + Math.round((2.9 * this.mThincknessNomalSize / 330) * 100) / 100)
            }
          }
          //雙壁照相
          else if(obj.radiographic_tech == 1) {
            if(obj.selectNominalSize) {
              if(obj.selectNominalSize.id < 11) {
                this.editedItem.geometric_unsharpness += (";" +Math.round((3 * obj.selectNominalSize.od_out / obj.mThincknessNomalSize) * 100) / 100)
              } else if(obj.selectNominalSize.id >= 11) {
                this.editedItem.geometric_unsharpness += (";" +Math.round((3 * obj.selectNominalSize.od_out / obj.mThincknessNomalSize) * 100) / 100)
              }
            }
          }
        }
      }
    },

    roleCalcSod(obj = null) {
      if(obj === null) {
        if(this.selectNominalSize) {
          if(this.mThincknessNomalSize && this.mThincknessNomalSize != 0.0) {
            if(this.editedItem.radiographic_tech == 0) {
              this.editedItem.sod = 330
            } else if(this.editedItem.radiographic_tech == 1) {
              if(this.editedItem.sin_dou == 0) {
                this.editedItem.sod = Math.round((this.mThincknessNomalSize * 8) * 100) / 100
              } else if(this.editedItem.sin_dou == 1) {
                this.editedItem.sod = Math.round((this.selectNominalSize.od_out - this.mThincknessNomalSize ) * 100) / 100
              }
            }
          }
        }
      } else {
        if(obj.selectNominalSize) {
          if(obj.mThincknessNomalSize && obj.mThincknessNomalSize != 0.0) {
            var sod = 330
            if(obj.radiographic_tech == 0) {
              this.editedItem.sod += (";" + 330)
            } else if(obj.radiographic_tech == 1) {
              if(obj.selectNominalSize.id < 11) {
                sod = Math.round((obj.mThincknessNomalSize * 8) * 100) / 100
                this.editedItem.sod += (";" + sod)
              } else if(obj.selectNominalSize.id >= 11) {
                sod = Math.round((obj.selectNominalSize.od_out - obj.mThincknessNomalSize ) * 100) / 100
                this.editedItem.sod += (";" + sod)
              }
            }
            return sod
          }
        }
      }
      return 0.0
    },

    roleCalcExTime(obj = null, sod = 0.0) {
      // console.log(this.editedItem.layers)
      if(obj == null) {
        if(!this.mThincknessNomalSize || this.editedItem.layers == '') {
          return
        }
        // console.log(this.listRT_Datas)
        const rt = this.listRT_Datas.find(element => element.thickness == Math.round(this.mThincknessNomalSize * this.editedItem.layers))
        // console.log(rt)
        if(this.editedItem.radiographic_tech !== '' && rt) {
          //單壁照相
          if(this.editedItem.radiographic_tech == 0) {
            if(this.mThincknessNomalSize && this.mThincknessNomalSize != 0.0 && rt) {
              var a = ((this.editedItem.sod + this.mThincknessNomalSize) * (this.editedItem.sod + this.mThincknessNomalSize) / this.editedItem.energy_strength)
              var b = (a * rt.numness * this.editedItem.film_brand) / (25.4*25.4)
              // console.log(b)
              this.editedItem.exposure_time = b
            }
          } else if(this.editedItem.radiographic_tech == 1) {
            if(this.editedItem.sin_dou == 0) {
              var a = this.editedItem.sod * 9 / 8 * this.editedItem.sod * 9 / 8 / this.editedItem.energy_strength * this.mThincknessNomalSize * this.editedItem.film_brand
              var b = a / 25.4 / 25.4
              this.editedItem.exposure_time = b
            } else if(this.editedItem.sin_dou == 1) {
              if(this.selectNominalSize) {
                var a = this.selectNominalSize.od_out * this.selectNominalSize.od_out / this.editedItem.energy_strength * this.mThincknessNomalSize * this.editedItem.film_brand
                var b = a / 25.4 / 25.4
                this.editedItem.exposure_time = b
              }
            }
          }
        }
      } else {
        if(!obj.mThincknessNomalSize || this.editedItem.layers == '') {
          return
        }
        // console.log(this.listRT_Datas)
        const rt = this.listRT_Datas.find(element => element.thickness == Math.round(obj.mThincknessNomalSize * this.editedItem.layers))
        // console.log(rt)
        if(obj.radiographic_tech !== '' && rt) {
          //單壁照相
          if(obj.radiographic_tech == 0) {
            if(obj.mThincknessNomalSize && obj.mThincknessNomalSize != 0.0 && rt) {
              var a = ((sod + obj.mThincknessNomalSize) * (sod + obj.mThincknessNomalSize) / this.editedItem.energy_strength)
              var b = (a * rt.numness * this.editedItem.film_brand) / (25.4*25.4)
              // console.log(b)
              this.editedItem.exposure_time += (";" + this.formateExTime(b))
            }
          } else if(obj.radiographic_tech == 1) {
            if(this.editedItem.sin_dou == 0) {
              var a = sod * 9 / 8 * sod * 9 / 8 / this.editedItem.energy_strength * obj.mThincknessNomalSize * this.editedItem.film_brand
              var b = a / 25.4 / 25.4
              this.editedItem.exposure_time += (";" + this.formateExTime(b))
            } else if(this.editedItem.sin_dou == 1) {
              if(obj.selectNominalSize) {
                var a = obj.selectNominalSize.od_out * obj.selectNominalSize.od_out / this.editedItem.energy_strength * obj.mThincknessNomalSize * this.editedItem.film_brand
                var b = a / 25.4 / 25.4
                this.editedItem.exposure_time += (";" + this.formateExTime(b))
              }
            }
          }
        }
      }
    },

    saveVerifyBack () {
      if(!this.editedItem.dispatch_id) return 
      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch_verify_back/${co[0]}/${co[1]}/` + this.editedItem.dispatch_id
      this.$swal.fire({
        title: '確定要退回審核?',
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
        } catch {

        }
      })
    },

    addDialogThickness () {
      this.dialogThicknessChoice = true
      this.thicknesGroupModel = this.thicknesGroup_Data.length
      this.thicknesGroupModelChoice = this.thicknesGroup_Data.length
    },

    deleteThickness (item) {
      let x = this.thicknesGroup_Data.indexOf(item)
      this.thicknesGroup_Data.splice(x, 1);
      this.editedItem.items.splice(x, 1)
    },

    saveMThinckness (data) {
      // this.mThincknessNomalSize = data[0].mThincknessNomalSize
      this.thicknesGroup_Data = data

      // console.log("saveMThinckness result", data)

      if(this.thicknesGroup_Data.length > 0) {
        this.editedItem.geometric_unsharpness = ''
        this.editedItem.sod = ''
        this.editedItem.exposure_time = ''
        // 多選項尺寸使用
        this.thicknesGroup_Data.forEach((element, index) => {
          // console.log("element", element)
          // console.log("item default start", this.defaultItem.items[0][0])
          this.roleCalcFOG(element)
          this.roleCalcExTime(element, this.roleCalcSod(element))
          if(!this.editedItem.items) {
            this.editedItem.items = []
          }
          if(!this.editedItem.items[index] || this.editedItem.items[index].length == 0) {
            this.editedItem.items[index] = []
            this.editedItem.items[index].push(Object.assign({}, this.defaultItem.items[0][0]))
            // console.log("item empty end", this.editedItem.items[index])
          }
          // console.log("item not empty end", this.editedItem.items[index])
          var thickness = element.mThincknessNomalSize
          // console.log("thickness", thickness)
          this.editedItem.items[index].forEach((element1, i) => {
            // console.log("start", this.editedItem.items[index][i])
            // console.log("items", element1)
            this.editedItem.items[index][i].thickness = thickness
            // console.log("end", this.editedItem.items[index][i])
          })
        })
        this.editedItem.geometric_unsharpness = this.editedItem.geometric_unsharpness.slice(1);
        this.editedItem.sod = this.editedItem.sod.slice(1);
        this.editedItem.exposure_time = this.editedItem.exposure_time.slice(1);
        this.thicknesGroupModelChoice = 0
        this.thicknesGroupModel = 0
        console.log(this.thicknesGroupModelChoice, this.editedItem.items)
      }
    },

    toggleDialog () {
      this.dialogThicknessChoice = !this.dialogThicknessChoice
    }
  }
}
</script>

<style lang="scss" scope>
.v-file-input__text {
  cursor: pointer;
}
tr .v-text-field__details {
  display: none !important;
}
</style>
