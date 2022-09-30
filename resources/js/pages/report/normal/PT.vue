<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="一般液滲檢測(PT)"
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
            <v-toolbar-title>Normal (PT)</v-toolbar-title>
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
              :channalType="'chimei_pt'"
              :thicknesGroup_Data="thicknesGroup_Data"
              :thicknesGroupModel="thicknesGroupModel"
              :dialog="dialogThicknessChoice" 
              :listRTPT_Datas="listRTPT_Datas"
              :listThickness_Datas="listThickness_Datas">
            </ThicknessChoice>
             <v-dialog
              v-model="dialogPreview"
              width="600px"
              transition="dialog-bottom-transition">
              <VueDocPreview :value="docPreviewUrlPath" :type="'office'" />
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
                  <v-toolbar-title>{{ editedItem.original_no }} 的列表</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-toolbar-items>
                    <v-btn
                      dark
                      text
                      @click="preview"
                      :disabled="editedDispatchStatus < 2"
                    >
                      預覽
                    </v-btn>
                    <v-btn
                      dark
                      text
                      @click="save"
                      :disabled="editedDispatchStatus > 2"
                    >
                      儲存
                    </v-btn>
                  </v-toolbar-items>
                </v-toolbar>
                <v-container class="grey lighten-5">
                  <v-row
                    class="mb-4"
                    justify="center"
                    no-gutters>
                      <v-col
                        cols="2" class="d-flex align-stretch"
                      >
                        <v-card
                          class="pa-2 align-stretch"
                          style="flex: 1;"
                          outlined
                          tile
                        >
                          <div class="text-subtitle-2">
                            報告規則版本
                          </div>
                          <div class="text-caption">
                            Report Role
                          </div>
                        </v-card>
                      </v-col>
                      <v-col
                        cols="4" class="d-flex align-stretch"
                      >
                        <v-card
                          class="pa-4 align-stretch"
                          style="flex: 1;"
                          outlined
                          tile
                        >
                        <!-- <div class="text-subtitle-2">
                          <v-select
                            ref="report_version"
                            v-model="editedItem.report_version"
                            :items="listReportVersion_Datas"
                            item-text="text"
                            item-value="value"
                            hide-details
                            :readonly="true"
                          ></v-select>
                        </div> -->
                        <v-text-field
                          ref="report_version"
                          v-model="editedItem.report_version"
                          :readonly="true"
                        ></v-text-field>
                        </v-card>
                      </v-col>
                      <v-col
                        cols="2" class="d-flex align-stretch"
                      >
                      </v-col>
                      <v-col
                        cols="4" class="d-flex align-stretch"
                      >
                      </v-col>
                  </v-row>
                    
                  <!-- row 1 -->
                  <BasicElements 
                    :editedItem="editedItem"
                    :readonlyVisible="readonlyVisible"
                    :useFunctionView="'normal_pt'"
                    :programBook="editedItem.report_version"
                  />
                  <v-row
                    class="my-2 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-divider
                      inset
                    ></v-divider>
                  </v-row>
                  <!-- row 1 -->
                  <v-row
                    class="mb-2 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 1; " class="d-flex align-stretch ">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          施加滲透劑
                        </div>
                        <div class="text-caption">
                          Penetrant Application
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
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
                                ref="penetrant_application"
                                v-model="editedItem.penetrant_application"
                                :items="listPenetrantApplication_Datas"
                                label="請選擇(Regular)"
                                item-text="text"
                                item-value="value"
                                dense
                                multiple
                                required
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          滲透時間
                        </div>
                        <div class="text-caption">
                          Dwell Time
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="dwell_time"
                          v-model="editedItem.dwell_time"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測時表面溫度
                        </div>
                        <div class="text-caption">
                          Surface Temperature
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="surface_temperature"
                          v-model="editedItem.surface_temperature"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 2 -->
                  <v-row
                    class="mb-4 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 1; " class="d-flex align-stretch ">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          滲透劑去除
                        </div>
                        <div class="text-caption">
                          Removal
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
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
                                ref="removal"
                                v-model="editedItem.removal"
                                :items="listRemoval_Datas"
                                label="請選擇(Regular)"
                                item-text="text"
                                item-value="value"
                                dense
                                multiple
                                required
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          乾燥時間
                        </div>
                        <div class="text-caption">
                          Drying Time
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="drying_time"
                          v-model="editedItem.drying_time"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測時機
                        </div>
                        <div class="text-caption">
                          Exam. Time
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
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
                                ref="exam_time"
                                v-model="editedItem.exam_time"
                                :items="listExamTime_Datas"
                                label="請選擇(Regular)"
                                item-text="text"
                                item-value="value"
                                dense
                                multiple
                                required
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
                    <v-col style="flex: 1; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          檢測方法
                        </div>
                        <div class="text-caption">
                          Examination Method
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
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
                                ref="exam_method"
                                v-model="editedItem.exam_method"
                                :items="listExamMethod_Datas"
                                label="請選擇(Regular)"
                                item-text="text"
                                item-value="value"
                                dense
                                multiple
                                required
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          顯像時間
                        </div>
                        <div class="text-caption">
                          Developing Time
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="developing_time"
                          v-model="editedItem.developing_time"
                          label="Regular"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          後清理
                        </div>
                        <div class="text-caption">
                          Post Clean
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
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
                                ref="post_clean"
                                v-model="editedItem.post_clean"
                                :items="listPostClean_Datas"
                                label="請選擇(Regular)"
                                item-text="text"
                                item-value="value"
                                dense
                                multiple
                                required
                                :disabled="readonlyVisible"
                              ></v-select>
                            </v-col>
                          </v-row>
                        </v-card-title>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 4 -->
                  <v-row
                    class="mb-2 d-flex"
                    no-gutters
                  >
                    <v-col cols="1" style="flex: 1; " class="d-flex align-stretch ">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          施加顯像劑
                        </div>
                        <div class="text-caption">
                          Developing
                        </div>
                      </v-card>
                    </v-col>
                    <v-col cols="3" style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="developing"
                          v-model="editedItem.developing"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                  </v-row>
                  <!-- row 4 -->
                  <v-row
                    class="mb-2 d-flex"
                    justify="center"
                    no-gutters
                  >
                    <v-col style="flex: 1; " class="d-flex align-stretch ">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          清洗劑
                        </div>
                        <div class="text-caption">
                          Cleaner
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="cleaner"
                          v-model="editedItem.cleaner"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          滲透劑
                        </div>
                        <div class="text-caption">
                          Penetrant
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="penetrant"
                          v-model="editedItem.penetrant"
                          :readonly="readonlyVisible"
                        ></v-text-field>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 1; " class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <div class="text-subtitle-2">
                          顯像劑
                        </div>
                        <div class="text-caption">
                          Developer
                        </div>
                      </v-card>
                    </v-col>
                    <v-col style="flex: 3;" class="d-flex align-stretch">
                      <v-card
                        class="pa-2 align-stretch"
                        style="flex: 1;"
                        outlined
                        tile
                      >
                        <v-text-field
                          ref="developer"
                          v-model="editedItem.developer"
                          :readonly="readonlyVisible"
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
                                v-if="thicknesGroup_Data[n-1].thickOrQu == 0"
                              >
                                {{thicknesGroup_Data[n-1].selectThickness.thickness.toUpperCase()}}
                              <v-divider class="mx-2"></v-divider>
                                {{thicknesGroup_Data[n-1].selectNominalSize.nominal_size}}
                              </v-card-text>
                              <v-card-text
                                :class="active ? 'white--text' : ''"
                                v-elseif="thicknesGroup_Data[n-1].thickOrQu == 1"
                              >
                                {{'面積'}}
                              <v-divider class="mx-2"></v-divider>
                                {{thicknesGroup_Data[n-1].mAreaSize}}
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
                      <v-simple-table id="tab">
                        <template v-slot:default>
                          <thead>
                            <!--  表头 -->
                            <tr>
                              <th class="text-left">項次</th>
                              <th class="text-left">工件編號<br>Object No.</th>
                              <th class="text-left">銲道長度<br>Weld Length</th>
                              <th class="text-left">銲工編號<br>Welder No.</th>
                              <th class="text-left">缺陷位置<br>Defect Location</th>
                              <th class="text-left">缺陷類別<br>Type of Defect</th>
                              <th class="text-left">缺陷長度<br>Defect Length</th>
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
                              <td><v-text-field
                                v-model="item.object_no"
                                :disabled="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.weld_length"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td>
                                <v-text-field
                                v-model="item.welder_no"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.defect_location"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.type_of_defect"
                                :readonly="readonlyVisible"
                                outlined
                                dense
                              ></v-text-field></td>
                              <td><v-text-field
                                v-model="item.defect_length"
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
                                  outlined
                                  dense
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
                                  <div class="d-flex justify-space-around">
                                    <!-- 修改界面显示取消 -->
                                    <v-btn fab color="error" dark x-small @click="deleteEditItems(item)">
                                      <v-icon>mdi-minus</v-icon>
                                    </v-btn>
                                    <!-- 修改界面显示取消 -->
                                    <v-btn fab color="success" dark x-small @click="insertEditItems(index)">
                                      <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                  </div>
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
import BasicElements from '../../../components/report/BasicElements.vue'
import draggable from "vuedraggable"
import VueDocPreview  from 'vue-doc-preview'
const SAMPLE_PDF_PATH = 'normal_pt'
const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
export default {
  name: 'PTnorView',

  middleware: 'auth',

  components: {
    PdfViewer,
    DataTableStatus,
    draggable,
    ThicknessChoice,
    BasicElements,
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
    dialogNew: false,
    dialogView: false,
    dialogDelete: false,
    dialogSample: false,
    dialogPreview: false,
    dialogThicknessChoice: false,
    
    loading: false,
    loadingSave: false,
    thicknesGroup_Data: [],
    thicknesGroupModel: -1,
    thicknesGroupModelChoice: 0,
    mThincknessNomalSize: null,
    dataItems: [],
    selectItem: [],
    listRTPT_Datas: [],
    listThickness_Datas: [],
    selectThickness: {},
    selectNominalSize: {},
    excelFile: null,
    pdfUrl: '',
    docPreviewUrlPath: '',
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
      customer: '',
      original_no: '',
      report_version: 0,
      id: 0,
      dispatch_id: '',
      applicable_code: '',
      criteria: '',
      procedure_no: '',
      region: '',  //區域/品名
      contract_no: '', //合約編號
      job_name: '',
      job_no: '',
      exam_date: new Date,
      exam_place: '',
      exam_type: '',
      material: '',
      thicknes: '',
      nominal_size: '',
      surface_status: '',
      exam_time: '',
      exam_method: '',
      light_intensity: '',
      white_ligth_meter: '',
      surface_temp: '',
      penetrant_application: '', //施加滲透劑
      dwell_time: '', //滲透時間
      surface_temperature: '', //檢測時表面溫度
      removal: '', //滲透劑去除
      drying_time: '', //乾燥時間
      developing_time: '', //顯像時間
      developing: '', //施加顯像劑
      post_clean: '', //後清理
      cleaner: '', //清洗劑
      penetrant: '', //滲透劑
      developer: '', //顯像劑dd
      obsert: '',
      thickness_objs: [],
      items:[
        [
          {
            object_no: '',
            weld_length: '',
            welder_no: '',
            defect_location: '',
            type_of_defect: '',
            defect_length: '',
            result: '',
            remark: '',
          }
        ]
      ],
    },
    defaultItem: {
      customer: '',
      original_no: '',
      report_version: 0,
      id: 0,
      dispatch_id: '',
      job_name: '',
      job_no: '',
      applicable_code: '',
      criteria: '',
      procedure_no: '',
      region: '',  //區域/品名
      contract_no: '', //合約編號
      exam_date: new Date,
      exam_place: '',
      exam_type: '',
      material: '',
      thicknes: '',
      nominal_size: '',
      surface_status: '',
      exam_time: '',
      exam_method: '',
      light_intensity: '',
      white_ligth_meter: '',
      surface_temp: '',
      obsert: '',
      penetrant_application: '', //施加滲透劑
      dwell_time: '', //滲透時間
      surface_temperature: '', //檢測時表面溫度
      removal: '', //滲透劑去除
      drying_time: '', //乾燥時間
      developing_time: '', //顯像時間
      developing: '', //施加顯像劑
      post_clean: '', //後清理
      cleaner: '', //清洗劑
      penetrant: '', //滲透劑
      developer: '', //顯像劑dd
      thickness_objs: [],
      items:[
        [
          {
            object_no: '',
            weld_length: '',
            welder_no: '',
            defect_location: '',
            type_of_defect: '',
            defect_length: '',
            result: '',
            remark: '',
          }
        ]
      ],
    },
    listResult_Datas:[
      {text: '合格', value: 0},
      {text: '不合格', value: 1},
    ],
    listReportVersion_Datas: [
      {text: 'ASME-02', value: 'asme02'},
      {text: 'AWS D1.1│D1.1M-02', value: 'awsd1'},
      {text: 'CNS11398-02', value: 'cns11398'},
      {text: 'CNS13464-01', value: 'cns13464'},
    ],
    listSurfaceStatus_Datas:[
      {text: '銲接表面As-Welded', value: '0'},
      {text: '輥軋表面As-Rolled', value: '1'},
      {text: '研磨表面As-ground', value: '2'},
      {text: '機械加工As-machined', value: '3'},
      {text: '其它Other', value: '4'},
    ],
    listExamTime_Datas:[
      {text: '銲接後After Welded', value: '0'},
      {text: '銲前表面準備Prepared Surface', value: '1'},
      {text: '熱處理後 After HT', value: '2'},
      {text: '試壓後After Hydro. Test', value: '3'},
      {text: '其它Other', value: '4'},
    ],
    listExamMethod_Datas:[
      {text: '色比 Color Contrast', value: '0'},
      {text: '螢光 Fluorescent', value: '1'},
      {text: '溶劑去除 Solvent Removable', value: '2'},
      {text: '水洗 Water Washable', value: '3'},
    ],
    listRemoval_Datas:[
      {text: '清潔布Dry Cloths', value: '0'},
      {text: '擦拭紙Absorbent Paper', value: '1'},
    ],
    listPenetrantApplication_Datas:[
      {text: '噴霧Spraying', value: '0'},
      {text: '刷塗Brushing', value: '1'},
    ],
    listPostClean_Datas:[
      {text: '溶劑Solvent', value: '0'},
      {text: '水洗Water', value: '1'},
      {text: '其他Other', value: '2'},
    ],
  }),

  computed: {
    formTitle () {
      return '液滲檢測報告樣板'
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
    mThincknessNomalSize(val, oldVal) {
      if (val === oldVal) return
      // this.editedItem.items.forEach(element => {
      //   element.weld_length = val
      // })
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
                id: res.data.data[idx].id,
                report_pt: res.data.data[idx].report_pt,
                original_no: res.data.data[idx].report_pt? res.data.data[idx].report_pt.original_no : ''
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
              console.log(res.data.data)
            if(res.data.data) {
              this.exportFinalPath = res.data.data.export_final_path? res.data.data.export_final_path : ''
            }
            if(res.data.data.report_pt) {
              this.editedItem = Object.assign({}, res.data.data.report_pt)
              console.log(this.editedItem.report_version)
              // if(!res.data.data.report_pt.items || res.data.data.report_pt.items.length < 1) {
              //   this.editedItem.items = Object.assign([], this.defaultItem.items)
              // }
            } else {
              let tmp = Object.assign({}, this.editedItem)
              this.editedItem = JSON.parse(JSON.stringify(this.defaultItem))
              this.editedItem.items = null
              this.editedItem.exam_date = tmp.exam_date
              this.editedItem.exam_place = tmp.exam_place
              this.editedItem.job_name = tmp.job_name
              this.editedItem.dispatch_id = tmp.id
            }
            this.getRTPT_Data()
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
            if(res.data.data && res.data.data && res.data.data.full_path) {
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

    insertEditItems(index) {
      var choiceIndex = this.thicknesGroupModelChoice
      if(choiceIndex < 0) return
      if(this.editedItem.items && this.editedItem.items[choiceIndex] && this.editedItem.items[choiceIndex].length > 0) {
        let _ = this.editedItem.items
        let tmpDefault = Object.assign({}, _[choiceIndex][index])
        let newArr = Object.assign([], _)
        tmpDefault.object_no = newArr[choiceIndex].length + 1
        // console.log(tmpDefault)
        newArr[choiceIndex].splice(index+1, 0, tmpDefault)
        // newArr[choiceIndex].forEach((element, index) => {
        //   newArr[choiceIndex][index].object_no = (index+1)
        // })
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

    deleteEditItems (item) {
      let x = this.editedItem.items[this.thicknesGroupModelChoice].indexOf(item)
      this.editedItem.items[this.thicknesGroupModelChoice].splice(x, 1);
      this.editedItem.items[this.thicknesGroupModelChoice].forEach((element, index) => {
        this.editedItem.items[this.thicknesGroupModelChoice][index].object_no = (index+1)
      })
    },

    editItem(item) {
      this.dialogView = true

      this.editedIndex = this.dataItems.indexOf(item)
      this.editedDispatchStatus = item.status
      this.editedItem = Object.assign({}, item)
      console.log(this.editedItem)
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

    authRoleVaild (key) {
      let co = SAMPLE_PDF_PATH.split('_');
      return this.user.menu.menus.find(e => Object.values(e.menu_info).find((obj) => {
        // console.log((co[0] + '_'+ key))
        return obj == ('/' + co[0] + '_'+ key + '/')
        }))
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
      // console.log(this.editedItem)
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
    
    preview () {
      var formError = false
      Object.keys(this.editedItem).forEach(f => {
        if(this.$refs[f] && !this.$refs[f].validate(true)) {
          formError = true
          return
        }
      })

      if(formError) {
        return
      }

      var route_name = 'dispatch_preview'

      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/${route_name}/${co[0]}/${co[1]}` + (this.editedItem.id === -1? '' : ('/'+this.editedItem.dispatch_id))
      this.$swal.fire({
        title: '預覽報告前請確定儲存?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          params.nominal_size = that.selectNominalSize.nominal_size
          params.thicknes = that.selectThickness.thickness
          params.thickness_objs = that.thicknesGroup_Data

          // params.surface_status = that.editedItem.surface_status.join(',')
          // params.exam_time = that.editedItem.exam_time.join(',')
          // params.type_of_particle_olor = that.editedItem.type_of_particle_olor.join(',')
          // params.magnetizing_methon = that.editedItem.magnetizing_methon.join(',')
          return this.$axios
            .fetchPut(url, params)
            .then(res => {
              return res.data
            })
            .catch(err => {
              console.log(err)
            })
        },
        allowOutsideClick: () => !this.$swal.isLoading()
      }).then((result) => {
        if(result.isConfirmed) {
          if(result.value.code == 1000) {
            var file = result.value.data
            if(file.export_final_path) {
              this.docPreviewUrlPath = file.export_final_path
              this.dialogPreview = true
              //  window.open('https://docs.google.com/viewer?url=' + file.export_final_path, '_blank')
            }
            // that.$swal.fire({
            //   title: "成功!",
            //   text: "儲存數據成功",
            //   type: "success",
            //   confirmButtonClass: "btn btn-success"
            // });
            // this.closeView()
          } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "儲存數據錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              // this.closeView()
          }
        }
      })
    },

    save () {
      var formError = false
      Object.keys(this.editedItem).forEach(f => {
        if(this.$refs[f] && !this.$refs[f].validate(true)) {
          formError = true
          return
        }
      })

      if(formError) {
        return
      }
      let params = this.editedItem
      let co = SAMPLE_PDF_PATH.split('_');
      let that = this
      let url = `api/dispatch/${co[0]}/${co[1]}/` + this.editedItem.id
      this.$swal.fire({
        title: '確定要儲存數據?',
        showCancelButton: true,
        confirmButtonText: '確認',
        showLoaderOnConfirm: true,
        cancelButtonText: '取消',
        preConfirm: () => {
          params.nominal_size = that.selectNominalSize.nominal_size
          params.thicknes = that.selectThickness.thickness
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
        // this.editedItem.geometric_unsharpness = ''
        // this.editedItem.sod = ''
        // this.editedItem.exposure_time = ''
        // 多選項尺寸使用
        this.thicknesGroup_Data.forEach((element, index) => {
          console.log("element", element)
          // console.log("item default start", this.defaultItem.items[0][0])
          // this.roleCalcFOG(element)
          // this.roleCalcExTime(element, this.roleCalcSod(element))
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
          // 面積
          if(element.thickOrQu == 1 || element.thickOrQu == 2) {
            this.editedItem.items[index].forEach((element1, i) => {
              // console.log("start", this.editedItem.items[index][i])
              // console.log("items", element1)
              this.editedItem.items[index][i].weld_length = element.mAreaSize
              // console.log("end", this.editedItem.items[index][i])
            })
          } else {
            this.editedItem.items[index].forEach((element1, i) => {
              // console.log("start", this.editedItem.items[index][i])
              // console.log("items", element1)
              this.editedItem.items[index][i].weld_length = thickness
              // console.log("end", this.editedItem.items[index][i])
            })
          }
          
        })
        // this.editedItem.geometric_unsharpness = this.editedItem.geometric_unsharpness.slice(1);
        // this.editedItem.sod = this.editedItem.sod.slice(1);
        // this.editedItem.exposure_time = this.editedItem.exposure_time.slice(1);
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
