<template>
  <div>
    <!-- row 1 -->
    <v-row
      class="mb-4 d-flex"
      justify="center"
      no-gutters
    >
      <v-col style="flex: 1;" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <div class="text-subtitle-2">
            顧客/地址
          </div>
          <div class="text-caption">
            Customer / Adress
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            v-model="editedItem.customer"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
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
            檢測地點／類別
          </div>
          <div class="text-caption">
            Exam. Place/Type
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
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
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
      <v-col cols="1" class="d-flex align-stretch">
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
      <v-col cols="3" class="d-flex align-stretch">
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
                ref="exam_date"
                v-model="editedItem.exam_date"
                label="Regular"
                prepend-icon="mdi-calendar"
                readonly
                v-bind="attrs"
                v-on="on"
                :rules="[
                  (v) => !!v || '請填寫此欄位',
                ]"
                required
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
      <v-col style="flex: 1;" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <div class="text-subtitle-2">
            檢測規範
          </div>
          <div class="text-caption">
            Applicable Code
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
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
                  v-model="editedItem.applicable_code"
                  v-if="!app_applicable_code"
                  label="Regular"
                  :readonly="readonlyVisible"
                  :rules="[
                    (v) => !!v || '請填寫此欄位',
                  ]"
                ></v-text-field>
                <v-select
                  v-model="editedItem.applicable_code"
                  v-if="app_applicable_code"
                  :items="app_applicable_code"
                  label="請選擇..."
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
      <v-col style="flex: 1;" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <div class="text-subtitle-2">
            接受標準
          </div>
          <div class="text-caption">
            Criteria
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            v-model="editedItem.criteria"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
      <v-col cols="1" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <div class="text-subtitle-2">
            程序書編號
          </div>
          <div class="text-caption">
            Procedure No./Rev.
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            v-model="editedItem.procedure_no"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
    </v-row>
    <!-- row 3 -->
    <v-row
      class="mb-4 d-flex"
      justify="center"
      no-gutters
    >
      <v-col cols="1" class="d-flex align-stretch">
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
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            v-model="editedItem.job_name"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
      <v-col cols="1" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <div class="text-subtitle-2">
            區域/品名
          </div>
          <div class="text-caption">
            Region/Specimen Name
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            v-model="editedItem.region"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
      <v-col cols="1" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <div class="text-subtitle-2">
            合約編號
          </div>
          <div class="text-caption">
            Contract No
          </div>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            v-model="editedItem.contract_no"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col cols="1" class="d-flex align-stretch">
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
      <v-col cols="3" class="d-flex align-stretch">
        <v-card
          class="pa-2 align-stretch"
          style="flex: 1;"
          outlined
          tile
        >
          <v-text-field
            ref="material"
            v-model="editedItem.material"
            label="Regular"
            :readonly="readonlyVisible"
            :rules="[
              (v) => !!v || '請填寫此欄位',
            ]"
          ></v-text-field>
        </v-card>
      </v-col>
      <v-col style="flex: 8;" class="d-flex align-stretch">
      </v-col>
    </v-row>
  </div>
</template>

<script>
  export default {
    name: 'BasicElements',

    props: ['editedItem', 'readonlyVisible', 'useFunctionView', 'programBook'],

    data: () => ({
      menuDatePicker: false,
      select_applicable_code: null,
      select_criteria: null,
      app_applicable_code: [],
      app_criteria: [],
      applicable_code: {
        pt: {
          asme02:   [
            {text: 'ASME  BPVC Sec. VIII Div.1&ASME Sec. V Article 6', value: 'ASME  BPVC Sec. VIII Div.1&ASME Sec. V Article 6'},
            {text: 'ASME BPVC Sec.I &ASME Sec. V Article 6', value: 'ASME BPVC Sec.I &ASME Sec. V Article 6'},
            {text: 'ASME  B31.1 &ASME Sec. V Article 6', value: 'ASME  B31.1 &ASME Sec. V Article 6'},
          ],
          cns11398: [
            {text: 'CNS 11398', value: 'CNS 11398'},
          ]
        },
        rt: {
          asme02:   [
            {text: 'ASME BPVC Sec. VIII Div.1&ASME Sec. V Article 2', value: 'ASME BPVC Sec. VIII Div.1&ASME Sec. V Article 2'},
            {text: 'ASME BPVC Sec. VIII Div.1&ASME Sec. V Article 2 ', value: 'ASME BPVC Sec. VIII Div.1&ASME Sec. V Article 2'},
            {text: 'ASME BPVC Sec. VIII Div.2 ', value: 'ASME BPVC Sec. VIII Div.2 '},
            {text: 'ASME BPVC Sec. IX', value: 'ASME BPVC Sec. IX'},
            {text: 'ASME  BPVC Sec. I', value: 'ASME  BPVC Sec. I'},
            {text: 'ASME  B31.1', value: 'ASME  B31.1'},
            {text: 'ASME  B31.3', value: 'ASME  B31.3'},
            {text: 'API 5L', value: 'API 5L'},
          ],
          awsd1: [
            {text: 'AWS D1.1/D1.1M', value: 'AWS D1.1/D1.1M'},
            {text: 'API 650', value: 'API 650'},
          ],
          cns11226: [
            {text: 'CNS 11226', value: 'CNS 11226'},
          ],
          cns12619: [
            {text: 'CNS12619', value: 'CNS12619'},
          ],
          cns13020: [
            {text: 'CNS13020', value: 'CNS13020'},
          ],
        },
        mt: {
          asme02:   [
            {text: 'ASME BPVC Sec.I&ASME Sec. V Article 7', value: 'ASME BPVC Sec.I&ASME Sec. V Article 7'},
            {text: 'ASME  BPVC Sec. VIII Div.1&ASME Sec. V Article 7', value: 'ASME  BPVC Sec. VIII Div.1&ASME Sec. V Article 7'},
            {text: 'ASME  BPVC Sec. VIII Div.2', value: 'ASME  BPVC Sec. VIII Div.2'},
          ],
          awsd1: [
            {text: 'AWS D1.1/D1.1M ', value: 'AWS D1.1/D1.1M '},
          ],
          cns13341: [
            {text: 'CNS 13341 (1)', value: 'CNS 13341(1)'},
            {text: 'CNS 13341 (2)', value: 'CNS 13341(2)'},
          ],
          cns11378: [
            {text: 'CNS 11378', value: 'CNS 11378'},
          ],
        },
      },
      criteria: {
        pt: {
          asme02:  [
            {text: 'ASME  BPVC Sec. VIII Div.1 Appendix8', value: 'ASME  BPVC Sec. VIII Div.1 Appendix8'},
            {text: 'ASME BPVC Sec.I A-270', value: 'ASME BPVC Sec.I A-270'},
            {text: 'ASME B31.1 Para-136.4.4', value: 'ASME B31.1 Para-136.4.4'},
          ],
          cns11398: [
            {text: 'CNS11398  二級', value: 'CNS11398  二級'},
          ],
        },
        rt: {
          asme02:  [
            {text: 'ASME BPVC Sec. VIII Div.1 UW-51(b)', value: 'ASME BPVC Sec. VIII Div.1 UW-51(b)'},
            {text: 'ASME BPVC Sec. VIII Div.1 UW-52(c)', value: 'ASME BPVC Sec. VIII Div.1 UW-52(c)'},
            {text: 'ASME BPVC Sec. VIII Div.2   7.5.3', value: 'ASME BPVC Sec. VIII Div.2   7.5.3'},
            {text: 'ASME BPVC Sec. IX QW191.1.2', value: 'ASME BPVC Sec. IX QW191.1.2'},
            {text: 'ASME  BPVC Sec. I PW-51.3', value: 'ASME  BPVC Sec. I PW-51.3'},
            {text: 'ASME B31.1 Para-136.4.5', value: 'ASME B31.1 Para-136.4.5'},
            {text: 'ASME  B31.3 Para-341.3.2', value: 'ASME  B31.3 Para-341.3.2'},
            {text: 'API 5L E4.5', value: 'API 5L E4.5'},
          ],
          awsd1: [
            {text: 'AWS D1.1/D1.1M 靜態力', value: 'AWS D1.1/D1.1M 靜態力'},
            {text: 'API 650 8.1.5 ASME Sec.VIII Div.1 UW-51(b)', value: 'API 650 8.1.5 ASME Sec.VIII Div.1 UW-51(b)'},
          ],
          cns11226: [
            {text: 'CNS 11226 二級', value: 'CNS 11226 二級'},
          ],
          cns12619: [
            {text: 'CNS 12619 二級', value: 'CNS 12619 二級'},
          ],
          cns13020: [
            {text: 'CNS 13020 動態力', value: 'CNS 13020 動態力'},
          ],
        },
        mt: {
          asme02:   [
            {text: 'ASME BPVC Sec.I A-260', value: 'ASME BPVC Sec.I A-260'},
            {text: 'ASME  BPVC Sec. VIII Div.1 Appendix6', value: 'ASME  BPVC Sec. VIII Div.1 Appendix6'},
            {text: 'ASME  BPVC Sec. VIII Div.2  7.5.6', value: 'ASME  BPVC Sec. VIII Div.2  7.5.6'},
          ],
          awsd1: [
            {text: 'AWS D1.1/D1.1M 靜態力', value: 'AWS D1.1/D1.1M 靜態力'},
          ],
          cns13341: [
            {text: 'CNS 13341 二級', value: 'CNS 13341 二級'},
            {text: 'CNS 13341 靜態力', value: 'CNS 13341 靜態力'},
          ],
          cns11378: [
            {text: 'CNS 11378  二級', value: 'CNS 11378  二級'},
          ],
        },
      },
      procedure: {
        pt: {
          asme02:  [
            'XID-PT-ASME/Rev.2',
            'XID-PT-ASME/Rev.2',
            'XID-PT-ASME/Rev.2',
          ],
          cns11398: [
            'XID-PT-CNS11398/Rev.0',
          ],
        },
        rt: {
          asme02:  [
            'XID-PT-ASME/Rev.2',
            'XID-PT-ASME/Rev.2',
            'XID-PT-ASME/Rev.2',
          ],
          awsd1: [
            'XID-RT-AWS D1.1/D1.1M/Rev.2',
            'XID-RT-AWS D1.1/D1.1M/Rev.2',
          ],
          cns11226: [
            'XID-RT-CNS11226/Rev.2',
          ],
          cns12619: [
            'XID-RT-CNS12619/Rev.1',
          ],
          cns13020: [
            'XID-RT-CNS13020/Rev.1',
          ],
        },
        mt: {
          asme02:   [
            'XID-MT-ASME/Rev.1',
            'XID-MT-ASME/Rev.1',
            'XID-MT-ASME/Rev.1',
          ],
          awsd1: [
            'XID-MT- AWSD1.1/D1.1M/Rev.1',
          ],
          cns13341: [
            'XID-MT-CNS13341/Rev.1',
            'XID-MT-CNS13341/Rev.1',
          ],
          cns11378: [
            'XID-MT-CNS11378/Rev.1',
          ],
        },
      }
    }),

    watch: {
      'editedItem.applicable_code': function (val, oldVal) {
        if(!this.useFunctionView || !this.programBook || val === oldVal) {
          return
        }
        let co = this.useFunctionView.split('_');
        if (this.applicable_code[co[1]][this.editedItem.report_version]) {
          const found = this.applicable_code[co[1]][this.editedItem.report_version].findIndex(element => element.value == val);
          this.editedItem.criteria = this.criteria[co[1]][this.editedItem.report_version][found].text
          this.editedItem.procedure_no = this.procedure[co[1]][this.editedItem.report_version][found]
        }
      },
      useFunctionView (val, oldVal) {
        console.log(val);
        if(!this.programBook || val === oldVal) {
          return
        }
        let co = this.useFunctionView.split('_');
        this.app_applicable_code = this.applicable_code[co[1]][val]
      },
      'editedItem.report_version' (val, oldVal) {
        if(!this.useFunctionView || val === oldVal) {
          return
        }
        let co = this.useFunctionView.split('_');
        this.app_applicable_code = this.applicable_code[co[1]][val]
      },
    },

    methods: {
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
          case 4:
            return "green"
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
    }
  }
</script>
