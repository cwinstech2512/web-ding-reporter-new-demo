<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="烯烴三廠檢測"
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
                  <v-toolbar-title>{{ editedItem.identify }} 的列表</v-toolbar-title>
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
                <v-container fluid class="grey lighten-5">
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
                          工程編號
                        </div>
                        <div class="text-caption">
                           Project NO
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
                                v-model="editedItem.job_no"
                                label="Regular"
                                :readonly="readonlyVisible"
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
                          迴路編號
                        </div>
                        <div class="text-caption">
                          Preparing  NO
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
                          ref="equlpment_no"
                          v-model="editedItem.equlpment_no"
                          label="Regular"
                          :readonly="readonlyVisible"
                          :rules="[
                            (v) => !!v || '請填寫此欄位',
                          ]"
                          required
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
                              ></v-select>
                            </v-col>
                            <!-- <v-col cols="12" md="6">
                              <v-select
                                v-model="editedItem.file_qty"
                                :items="listQty_Datas"
                                label="數量"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                              ></v-select>
                            </v-col> -->
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>

                  <!-- row 2 -->
                  <!-- <v-row
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
                          厚度
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
                          <v-col cols="12" md="4">
                            <v-select
                              v-model="selectNominalSize"
                              :items="listRTPT_Datas"
                              label="尺寸"
                              item-text="nominal_size"
                              item-value="id"
                              dense
                              return-object
                              single-line
                            ></v-select>
                          </v-col>
                          <v-col cols="12" md="4">
                            <v-select
                              v-model="selectThickness"
                              :items="listThickness_Datas"
                              label="厚度"
                              item-text="thickness"
                              item-value="value"
                              dense
                              return-object
                              single-line
                            ></v-select>
                          </v-col>
                          <v-col cols="12" md="4">
                            <div v-text="mThincknessNomalSize + 'mm'"></div>
                          </v-col>
                        </v-row>
                      </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row> -->
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
                          檢測儀器廠牌
                        </div>
                        <div class="text-caption">
                          Equipment  Brand
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
                          底片型號
                        </div>
                        <div class="text-caption">
                          Type/Brand Name
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
                                :readonly="readonlyVisible"
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
                          射源類別
                        </div>
                        <div class="text-caption">
                          RADIATION ENERGY
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
                                v-model="editedItem.radiation_energy"
                                :items="listRadiationEnergy_Datas"
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
                          :readonly="readonlyVisible"
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
                          底片黑度範圍
                        </div>
                        <div class="text-caption">
                          DENSITY
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
                                v-model="editedItem.sin_dou"
                                :items="listDensity_Datas"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
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
                          增感屏厚度
                        </div>
                        <div class="text-caption">
                          SCREEN THICK
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
                                v-model="editedItem.screen_thick"
                                :items="listScreenThick_Datas"
                                item-text="text"
                                item-value="value"
                                dense
                                single-line
                                :readonly="readonlyVisible"
                              ></v-select>
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
                              ></v-select>
                            </v-col>
                          </v-row>
                      </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 9 -->
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
                          審查人員
                        </div>
                        <div class="text-caption">
                          Approved By
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
                          ref="approved"
                          v-model="editedItem.approved"
                          label="審查人員"
                          :readonly="readonlyVisible"
                          :rules="[
                            (v) => !!v || '請填寫此欄位',
                          ]"
                          required
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
                        <v-text-field
                          ref="obsert"
                          v-model="editedItem.obsert"
                          label="檢測人員"
                          :readonly="readonlyVisible"
                          :rules="[
                            (v) => !!v || '請填寫此欄位',
                          ]"
                          required
                        ></v-text-field>
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
                      <v-simple-table dense id="tab">
                        <template v-slot:default>
                          <thead>
                            <!--  表头 -->
                            <tr>
                              <th width="2%" class="text-left">項次</th>
                              <th class="text-left">底片編號</th>
                              <th class="text-left">管線編號</th>
                              <th width="4%" class="text-left">管線尺寸</th>
                              <th class="text-left">檢測位置<br>簡述</th>
                              <th class="text-left">底片尺寸</th>
                              <th class="text-left">部件材質</th>
                              <th class="text-left">公稱厚度<br>(mm)</th>
                              <th class="text-left">剩餘厚度<br>(mm)</th>
                              <th class="text-left">腐蝕<br>(內,外,CUI)</th>
                              <th class="text-left">缺陷描述</th>
                              <th class="text-left">建議處理方式</th>
                              <th class="text-left">安裝日期</th>
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
                              <td>
                                <v-text-field
                                v-model="item.film_no"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.pipeline_no"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.pipeline_size"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.brief_descript"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.film_size"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.material"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.n_thickness"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.le_thickness"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td>
                                <v-select
                                  v-model="item.corrosion"
                                  :items="listCorrosion_Datas"
                                  item-text="text"
                                  item-value="value"
                                  dense
                                  single-line
                                  multiple
                                  :readonly="readonlyVisible"
                                ></v-select>
                              </td>
                              <td><v-text-field
                                v-model="item.defect_descript"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.suggested_handling"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <td>
                                <v-menu
                                  :close-on-content-click="false"
                                  :nudge-right="40"
                                  transition="scale-transition"
                                  offset-y
                                  min-width="auto"
                                  v-model="item.model_exce_date"
                                >
                                  <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                      ref="exam_date"
                                      v-model="item.exce_date"
                                      label="0000-00-00"
                                      readonly
                                      v-bind="attrs"
                                      v-on="on"
                                      single-line
                                      class="text-caption font-weight-thin"
                                    ></v-text-field>
                                  </template>
                                  <v-date-picker
                                    v-model="item.exce_date"
                                    @input="item.model_exce_date = false"
                                  ></v-date-picker>
                                </v-menu>
                              </td>
                               <td><v-text-field
                                v-model="item.remark"
                                :readonly="readonlyVisible"
                              ></v-text-field></td>
                              <template>
                                <!-- 按钮区域 -->
                              <td>
                                <!-- 修改界面显示取消 -->
                                <v-btn rounded color="success" dark small v-if="editedItem.items && index == editedItem.items.length - 1" @click="insertEditItems()">插入下一行</v-btn>
                                <!-- 修改界面显示取消 -->
                                <v-btn rounded color="error" dark small v-else @click="deleteEditItems(item)">刪除</v-btn>
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
                      :dark="!(!authRoleVaild('vaild') || editedDispatchStatus != 3)"
                      color="green darken-1"
                      :disabled="(!authRoleVaild('vaild') || editedDispatchStatus != 3)"
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
import moment from 'moment'
const SAMPLE_PDF_PATH = 'fpg_rthree'
const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
export default {
  name: 'RTOl3fpgView',

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
    search: '',
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
    objDL_Data: {
      data_value: null
    },
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
    listRadiationEnergy_Datas: [
      {text: 'Ir-192', value: 0},
      {text: 'CO-60', value: 1},
      {text: 'X-Ray', value: 2},
      {text: 'se-75', value: 3},
    ],
    listFileSize_Datas: [
      {text: '10inX12in', value: '0'},
      {text: '8inX10in', value: '1'},
    ],
    listQty_Datas:[
      {text: '單 Single', value: '0'},
      {text: '雙 Double', value: '1'},
    ],
    listDensity_Datas:[
      {text: '單片SINGLE1.8-4.0', value: 0},
      {text: '雙片DOUBLE', value: 1},
    ],
    listScreenThick_Datas:[
      {text: '前FRONT 鉛Pb-0.005', value: 0},
      {text: '後BACK  鉛Pb-0.010', value: 1},
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
      {text: '是', value: 0},
      {text: '否', value: 1},
    ],
    listCorrosion_Datas:[
      {text: '內', value: 0},
      {text: '外', value: 1},
      {text: 'CUI', value: 2},
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
    editedDispatchStatus: false,
    exportFinalPath: null,
    editedItem: {
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
      radiation_energy: '',
      film_type: '', sn: '', film_brand: '', film_size: '', focal_spot: '', file_qty: '', sod: '', geometric_unsharpness: '', 
      exposure_time: '', iqi_location_mark: '', iqi_no: '', interpret: 0, film_processing_temp: '', film_processing_min: '',
      energy_strength: '', sin_dou: 0,
      obsert: '',approved: '',
      items:[
        {
          object_no: '',
          film_no: '', //底片編號
          pipeline_no: '', //管線編號
          pipeline_size: '', //管線尺寸
          brief_descript: '', //檢測位置簡述
          n_thickness: '', //公稱厚度
          le_thickness: '', //剩餘厚度
          thinning_rate: '',//減薄率
          corrosion: '', //腐蝕
          defect_descript: '', //缺陷描述
          suggested_handling: '', //建議處理方式
          film_size: '', //底片尺寸
          material: '', // 部件材質
          exce_date: null, //安裝日期
          model_exce_date: false, //false true
          result: '',
          remark: '',
        }
      ],
    },
    defaultItem: {
      id: 0,
      dispatch_id: '',
      job_name: '',
      exam_date: new Date,
      exam_place: '',
      material: '',
      thicknes: '',
      layers: '',
      nominal_size: '',
      radiographic_tech: '',
      identify: '',
      radiation_energy: '',
      film_type: '', sn: '', film_brand: '', film_size: '', focal_spot: '', file_qty: '', sod: '', geometric_unsharpness: '', 
      exposure_time: '', iqi_location_mark: '', iqi_no: '', interpret: 0, film_processing_temp: '25', film_processing_min: '5',
      energy_strength: '', sin_dou: -1,
      obsert: '', approved: '',
      items:[
        {
          object_no: '',
          film_no: '', //底片編號
          pipeline_no: '', //管線編號
          pipeline_size: '', //管線尺寸
          brief_descript: '', //檢測位置簡述
          n_thickness: '', //公稱厚度
          le_thickness: '', //剩餘厚度
          thinning_rate: '',//減薄率
          corrosion: '', //腐蝕
          defect_descript: '', //缺陷描述
          suggested_handling: '', //建議處理方式
          film_size: '', //底片尺寸
          material: '', // 部件材質
          exce_date: null, //安裝日期
          model_exce_date: false, //false true
          result: '',
          remark: '',
        }
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

    mThincknessNomalSize() {
      if(Object.keys(this.selectThickness).length !== 0 && Object.keys(this.selectNominalSize).length !== 0) {
        console.log(this.selectNominalSize)
        return /*this.selectNominalSize.nominal_size + ', ' + this.selectThickness.thickness + ', ' + */this.selectNominalSize[this.selectThickness.thickness]
      }
    },
  },

  watch: {
    selectDL (val, oldVal) {
      if (val === oldVal) return
      this.getDL_Data()
    },
    mThincknessNomalSize(val, oldVal) {
      if (val === oldVal) return
      this.roleCalcFOG()
      this.editedItem.items.forEach(element => {
        element.thickness = val
      })
      this.roleCalcSod()
      this.roleCalcExTime()
    },
    'editedItem.radiographic_tech'(val, oldVal) {
      if (val === oldVal) return
      this.roleCalcSod()
      this.roleCalcExTime()
    },
    'editedItem.sin_dou'(val, oldVal) {
      if (val === oldVal) return
      this.roleCalcSod()
      this.roleCalcExTime()
    },
    'editedItem.film_brand'(val, oldVal) {
      // console.log(val, oldVal)
      // if (val === oldVal) return
      // this.roleCalcSod()
      // this.roleCalcExTime()
      // if(val == '1' || val == '1.5')
      //   this.editedItem.film_type = 'Class I'
      // else
      //   this.editedItem.film_type = 'Class II'
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
            if(res.data.data) {
              this.exportFinalPath = res.data.data.export_final_path
              if(res.data.data.report_rthree) {
                this.editedItem = Object.assign({}, res.data.data.report_rthree)
                this.editedItem.film_size = this.editedItem.film_size.split(',')
                if(!res.data.data.report_rthree.items) {
                  this.editedItem.items = Object.assign({}, this.defaultItem.items)
                }
              } else {
                let tmp = Object.assign({}, this.editedItem)
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedItem.exam_date = tmp.exam_date
                this.editedItem.exam_place = tmp.exam_place
                this.editedItem.dispatch_id = tmp.id
              this.editedItem.job_name = tmp.job_name
              }
            } else {
              let tmp = Object.assign({}, this.editedItem)
              this.editedItem = Object.assign({}, this.defaultItem)
              this.editedItem.exam_date = tmp.exam_date
              this.editedItem.exam_place = tmp.exam_place
              this.editedItem.dispatch_id = tmp.id
              this.editedItem.job_name = tmp.job_name
            }
            this.selectThickness = {thickness: this.editedItem.thicknes, value: this.editedItem.thicknes}
            this.getRTPT_Data()
            this.getDL_Data()
            this.selectDL = this.editedItem.identify
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

    authRoleVaild (key) {
      let co = SAMPLE_PDF_PATH.split('_');
      return this.user.menu.menus.find(e => Object.values(e.menu_info).find((obj) => {
        console.log((co[0] + '_'+ key))
        return obj == ('/' + co[0] + '_'+ key + '/')
        }))
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
        let tmpDefault = Object.assign({}, this.editedItem.items[this.editedItem.items.length-1])
        tmpDefault.object_no = this.editedItem.items.length + 1
        // console.log(tmpDefault)
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

    getPT_Data() {
      let that = this
      let url = 'api/data/rt_ness/lately'
      let params = {}
      this.$axios
        .fetchGet(url, params)
          .then(res => {
            console.log(res.data.data)
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
        console.log(time)
        sec = Math.round((time-min) * 60)
      }
      
      return min + " 分 " + sec + " 秒"
    },

    roleCalcFOG() {
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
    },

    roleCalcSod() {
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
    },

    roleCalcExTime() {
      console.log(this.editedItem.layers)
      if(!this.mThincknessNomalSize || this.editedItem.layers == '') {
        return
      }
      // console.log(this.listRT_Datas)
      const rt = this.listRT_Datas.find(element => element.thickness == Math.round(this.mThincknessNomalSize * this.editedItem.layers))
      console.log(rt)
      if(this.editedItem.radiographic_tech !== '' && rt) {
        //單壁照相
        if(this.editedItem.radiographic_tech == 0) {
          if(this.mThincknessNomalSize && this.mThincknessNomalSize != 0.0 && rt) {
            var a = ((this.editedItem.sod + this.mThincknessNomalSize) * (this.editedItem.sod + this.mThincknessNomalSize) / this.editedItem.energy_strength)
            var b = (a * rt.numness * this.editedItem.film_brand) / (25.4*25.4)
            console.log(b)
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
    }

    
  }
}
</script>

<style lang="scss" scope>
.v-file-input__text {
  cursor: pointer;
}
</style>
