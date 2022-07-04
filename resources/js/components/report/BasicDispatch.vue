<template>
  <v-row>
    <v-col
      cols="6"
    >
      <v-row>
      
      <v-text-field
        v-model="editedItem.report_version"
        v-if="!listReportVersion_Datas[function_cate] || report_version_inputable"
        prepend-icon="mdi-distribute-horizontal-center"
        label="報告規則版本"
        outlined
        :readonly="readonlyVisible"
      ></v-text-field>
      <v-select
        ref="report_version"
        v-model="editedItem.report_version"
        v-else
        :items="listReportVersion_Datas[function_cate]"
        prepend-icon="mdi-distribute-horizontal-center"
        label="報告規則版本"
        item-text="text"
        item-value="value"
        outlined
        :readonly="readonlyVisible"
      ></v-select>
      <v-checkbox
        v-model="report_version_inputable"
        label="自行填寫"
      ></v-checkbox>
      </v-row>
    </v-col>
    <v-col
      cols="6"
    >
      <v-text-field
        v-model="editedItem.applicable_code"
        v-if="app_applicable_code.length == 0"
        prepend-icon="mdi-distribute-horizontal-center"
        label="檢測規範"
        outlined
        :readonly="readonlyVisible"
      ></v-text-field>
      <v-select
        v-model="editedItem.applicable_code"
        v-if="app_applicable_code.length > 0"
        prepend-icon="mdi-distribute-horizontal-center"
        :items="app_applicable_code"
        label="檢測規範"
        item-text="text"
        item-value="value"
        outlined
        :disabled="readonlyVisible"
      ></v-select>
    </v-col>
    <v-col
      cols="6"
    >
      <v-text-field
        v-model="editedItem.criteria"
        label="接受標準"
        :readonly="readonlyVisible"
        prepend-icon="mdi-distribute-horizontal-center"
        outlined
      ></v-text-field>
    </v-col>
    <v-col
      cols="6"
    >
      <v-text-field
        v-model="editedItem.procedure_no"
        label="程序書編號"
        :readonly="readonlyVisible"
        prepend-icon="mdi-distribute-horizontal-center"
        outlined
      ></v-text-field>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'BasicDisptch',
  props: ['editedItem', 'readonlyVisible'],
  data: () => ({
    select_applicable_code: null,
    select_criteria: null,
    app_applicable_code: [],
    app_criteria: [],
    function_cate: '',
    report_version_inputable: true,
    listReportVersion_Datas: {
      pt: [
        {text: 'CNS11398', value: 'cns11398'},
        {text: 'ASME-02', value: 'asme02'},
        {text: 'AWS D1.1', value: 'awsd1'},
        // {text: 'ISO 17637', value: 'iso17367'},
      ],
      rt: [
        {text: 'API 650-02', value: 'api650'},
        {text: 'ASME-02', value: 'asme02'},
        {text: 'AWS D1.1│D1.1M-02', value: 'awsd1'},
        {text: 'CNS11226-02', value: 'cns11226'},
        {text: 'CNS12619-02', value: 'cns12619'},
        {text: 'CNS13020-02', value: 'cns13020'},
        {text: 'JIS3104-02', value: 'jis3104'},
      ],
      mt: [
        {text: 'ASME-02', value: 'asme02'},
        {text: 'AWS D1.1│D1.1M-02', value: 'awsd1'},
        {text: 'CNS11378-02', value: 'cns11378'},
        {text: 'CNS13341-01', value: 'cns13341'},
      ],
      ut: [
        {text: 'CNS12618', value: 'cns12618'},
        {text: 'CNS14135', value: 'cns14135'},
        {text: 'CNS14136', value: 'cns14136'},
        {text: 'CNS11401', value: 'cns11401'},
        {text: 'ASME-02', value: 'asme02'},
        {text: 'ASME-SA435', value: 'asme435'},
        {text: 'ASME-SA578', value: 'asme578'},
        {text: 'AWS D1.1|D1.1M-02', value: 'awsd1'},
      ],
      vt: [
        {text: 'AWS D1.1|D1.1M-01', value: 'awsd1'},
      ]
    },
    applicable_code: {
      pt: {
        asme02:   [
          {text: 'ASME  BPVC Sec. VIII Div.1&ASME Sec. V Article 6', value: 'ASME  BPVC Sec. VIII Div.1&ASME Sec. V Article 6'},
          {text: 'ASME BPVC Sec.I &ASME Sec. V Article 6', value: 'ASME BPVC Sec.I &ASME Sec. V Article 6'},
          {text: 'ASME  B31.1 &ASME Sec. V Article 6', value: 'ASME  B31.1 &ASME Sec. V Article 6'},
        ],
        cns11398: [
          {text: 'CNS 11398', value: 'CNS 11398'},
        ],
        awsd1: [
          {text: 'AWS D1.1/D1.1M', value: 'AWS D1.1/D1.1M'},
        ],
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
      ut: {
        cns12618: [
          {text: 'CNS12618', value: 'CNS12618'},
        ],
        cns14135: [
          {text: 'CNS14135', value: 'CNS14135'},
        ],
        cns14136: [
          {text: 'CNS14136', value: 'CNS14136'},
        ],
        cns11401: [
          {text: 'CNS11401', value: 'CNS11401'},
        ],
        asme02: [
          {text: 'ASME BPVC Sec. VIII Div.1 & ASME Sec. V Article 4', value: 'ASME BPVC Sec. VIII Div.1 & ASME Sec. V Article 4'},
        ],
        asme435: [
          {text: 'ASME-SA435', value: 'ASME-SA435'},
        ],
        asme578: [
          {text: 'ASME-SA578', value: 'ASME-SA578'},
        ],
        awsd1: [
          {text: 'AWS D1.1-D1.1M 靜態力', value: 'AWS D1.1-D1.1M 靜態力'},
        ]
      },
      vt: {
        awsd1: [
          {text: 'AWS D1.1-D1.1M', value: 'AWS D1.1-D1.1M'},
        ]
      }
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
        awsd1: [
          {text: 'AWS D1.1/D1.1M', value: 'AWS D1.1/D1.1M'},
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
      ut: {
        cns12618: [
          {text: 'CNS12618 二級', value: 'CNS12618 二級'},
        ],
        cns14135: [
          {text: 'CNS14135 二級', value: 'CNS14135 二級'},
        ],
        cns14136: [
          {text: 'CNS14136 二級', value: 'CNS14136 二級'},
        ],
        cns11401: [
          {text: 'CNS11401 二級', value: 'CNS11401 二級'},
        ],
        asme02: [
          {text: 'ASME BPVC Sec. VIII Div.1 Appendix 12', value: 'ASME BPVC Sec. VIII Div.1 Appendix 12'},
        ],
        asme435: [
          {text: 'ASME-SA435', value: 'ASME-SA435'},
        ],
        asme578: [
          {text: 'ASME-SA578', value: 'ASME-SA578'},
        ],
        awsd1: [
          {text: 'AWS D1.1-D1.1M 靜態力', value: 'AWS D1.1-D1.1M 靜態力'},
        ]
      },
      vt: {
        awsd1: [
          {text: 'AWS D1.1-D1.1M 靜態力', value: 'AWS D1.1-D1.1M 靜態力'},
        ]
      }
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
        awsd1: [
          'XID-PT-AWS/Rev.0',
        ],
      },
      rt: {
        asme02:  [
          'XID-RT-ASME/Rev.2',
          'XID-RT-ASME/Rev.2',
          'XID-RT-ASME/Rev.2',
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
          'XID-MT-AWSD1.1/D1.1M/Rev.1',
        ],
        cns13341: [
          'XID-MT-CNS13341/Rev.1',
          'XID-MT-CNS13341/Rev.1',
        ],
        cns11378: [
          'XID-MT-CNS11378/Rev.1',
        ],
      },
       ut: {
        cns12618: [
          'XID-UT-CNS12618/Rev.0',
        ],
        cns14135: [
          'XID-UT-CNS14135/Rev.2',
        ],
        cns14136: [
          'XID-UT-CNS14136/Rev.1',
        ],
        cns11401: [
          'XID-UT-CNS11401/Rev.1',
        ],
        asme02: [
          'XID-UT-ASME/Rev.2',
        ],
        asme435: [
          'XID-UT-ASME-SA435/Rev.0',
        ],
        asme578: [
          'XID-UT-ASME-SA578/Rev.1',
        ],
        awsd1: [
          'XID-UT-AWSD1.1/D1.1M/Rev.2',
        ]
      },
      vt: {
        awsd1: [
          'XID-VT-AWSD1.1/D1.1M/Rev.1',
        ]
      }
    }
  }),

  watch: {
    'editedItem.function_path' (val, oldVal) {
      if(val) {
        this.function_cate = val.split('.')[1]
        // this.editedItem.report_version = ''
        // this.editedItem.applicable_code = ''
        // this.editedItem.criteria = ''
        // this.editedItem.procedure_no = ''
      }
    },
    'editedItem.report_version' (val, oldVal) {
      if(val === oldVal || !this.function_cate) {
        return
      }
      // let co = this.editedItem.function_path.split('_');
      if(this.applicable_code[this.function_cate])
        this.app_applicable_code = this.applicable_code[this.function_cate][val]? this.applicable_code[this.function_cate][val] : []
    },
    'editedItem.applicable_code' (val, oldVal) {
      console.log(val)
      if(!this.function_cate|| val === oldVal) {
        return
      }
      // let co = this.editedItem.function_path.split('_');
      if (this.applicable_code[this.function_cate] && this.applicable_code[this.function_cate][this.editedItem.report_version]) {
        const found = this.applicable_code[this.function_cate][this.editedItem.report_version].findIndex(element => element.value == val);
        this.editedItem.criteria = this.criteria[this.function_cate][this.editedItem.report_version][found].text
        this.editedItem.procedure_no = this.procedure[this.function_cate][this.editedItem.report_version][found]
      }
    },
    report_version_inputable (val, oldVal) {
      if(val === oldVal) {
        return
      }
      if(val) {
        this.editedItem.report_version = ''
      }
    }
  }
}
</script>