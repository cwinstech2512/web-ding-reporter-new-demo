<template>
  <v-dialog
    v-model="dialog"
    max-width="500px"
    persistent
  >
    <v-card>
      <v-card-title class="text-h5">
          新增/編輯尺寸
      </v-card-title>
      <v-card-text>
        <v-container>
        <v-row>
          <v-col cols="12" md="10">
            <v-select
              v-model="thick_or_qu"
              :items="listThickOrQu_Datas"
              label="尺寸/面積/長度"
              item-text="text"
              item-value="value"
              dense
              outlined
            ></v-select>
          </v-col>
        </v-row>
        <v-row v-if="thick_or_qu == 0">
          <v-col cols="12" md="10">
            <v-select
              v-model="selectNominalSize"
              :items="listRTPT_Datas"
              label="尺寸"
              item-text="nominal_size"
              item-value="id"
              dense
              return-object
              outlined
            ></v-select>
          </v-col>
        </v-row>
        <v-row v-if="thick_or_qu == 0">
          <v-col cols="12" md="10">
            <v-select
              v-model="selectThickness"
              :items="listThickness_Datas"
              label="厚度"
              item-text="thickness"
              item-value="value"
              dense
              return-object
              outlined
            ></v-select>
          </v-col>
        </v-row>
        <v-row v-if="thick_or_qu == 1 || thick_or_qu == 2">
          <v-col cols="12" md="10">
            <v-text-field
              label="請輸入數值"
              outlined
              dense
              v-model="mAreaSize"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row v-if="thick_or_qu == 0">
          <v-col cols="12" md="10">
            <v-text-field
              label="查表結果"
              outlined
              dense
              v-model="mThincknessNomalSize"
              :disabled="true"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row v-if="channalType == 'chimei_rt' || channalType == 'normal_rt'">
          <v-col cols="12" md="10">
            <v-select
              v-model="radiographic_tech"
              :items="listRadiographicTech_Datas"
              label="照射方法"
              item-text="text"
              item-value="value"
              dense
              outlined
            ></v-select>
          </v-col>
        </v-row>
        <v-row v-if="channalType == 'chimei_rt' || channalType == 'normal_rt'">
          <v-col cols="12" md="10">
            <v-select
              v-model="layers"
              :items="listLayer_Datas"
              label="層數"
              item-text="text"
              item-value="value"
              dense
              outlined
            ></v-select>
          </v-col>
        </v-row>
        <v-row v-if="channalType == 'chimei_rt' || channalType == 'normal_rt'">
          <v-col cols="12" md="10">
            <v-select
              v-model="film_brand"
              :items="listFilmBrand_Datas"
              label="底片廠牌"
              item-text="text"
              item-value="value"
              dense
              outlined
            ></v-select>
          </v-col>
        </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="blue darken-1"
          text
          @click="toggleDialog"
        >
          關閉
        </v-btn>
        <v-btn
          color="green darken-1"
          text
          @click="saveMThinckness"
          :disabled="isDisableSave()"
        >
          儲存
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'ThicknessChoice',

    props: { 
      dialog: {
        type: Boolean,
        required: true
      },
      listRTPT_Datas: {
        type: Array,
        required: true
      },
      listThickness_Datas: {
        type: Array,
        required: true
      },
      thicknesGroup_Data: {
        type: Array,
        required: true
      },
      thicknesGroupModel: {
        type: Number,
        required: true
      },
      channalType: {
        type: String,
        required: true
      },
    },

    data: () => ({
      isSave: false,
      selectThickness: {},
      selectNominalSize: {},
      listRadiographicTech_Datas: [
        {text: '單層壁Single Wall Exposure', value: 0},
        {text: '雙層壁Double Wall Exposure', value: 1},
      ],
      listThickOrQu_Datas: [
        {text: '尺寸', value: 0},
        {text: '面積', value: 1},
        {text: '長度', value: 2},
      ],
      listLayer_Datas: [
        {text: '1層', value: 1},
        {text: '2層', value: 2},
      ],
      listFilmBrand_Datas: [
        {text: 'Agfa D4', value: '1'},
        {text: 'Agfa D5', value: '1.5'},
        {text: 'Agfa D7', value: '3'},
      ],
      radiographic_tech: null,
      layers: null,
      film_brand: null,
      thick_or_qu: 0,
      mAreaSize: 0,
    }),

    watch: {
      thicknesGroupModel (val, oldVal) {
        if (val === oldVal) return
      },
      thick_or_qu (val, oldVal) {
        if (val === oldVal) return
        this.mAreaSize = 0
        if(this.mThincknessNomalSize) this.mThincknessNomalSize = null
        this.selectNominalSize = {}
        this.selectThickness = {}
      },
      // selectNominalSize (val, oldVal) {
      //   if (val === oldVal) return
      //   this.$emit('changeSelectNominalSize', val)
      // },
    },

    computed: {
      mThincknessNomalSize() {
        if(Object.keys(this.selectThickness).length !== 0 && Object.keys(this.selectNominalSize).length !== 0) {
          this.isSave = true
          return /*this.selectNominalSize.nominal_size + ', ' + this.selectThickness.thickness + ', ' + */this.selectNominalSize[this.selectThickness.thickness]
        } else {
          return 0.00
        }
      },
    },

    methods: {
      isDisableSave () {
        if(this.mAreaSize || this.mThincknessNomalSize) return false
        else return true
      },
      saveMThinckness () {
        if(!this.isDisableSave()) {
          var obj = {
            selectThickness: this.selectThickness,
            selectNominalSize: this.selectNominalSize,
            mThincknessNomalSize: this.mThincknessNomalSize,
            radiographic_tech: this.radiographic_tech,
            thicknesGroupModel: this.thicknesGroupModel,
            thickOrQu: this.thick_or_qu,
            mAreaSize: this.mAreaSize,
            layers: this.layers,
            film_brand: this.film_brand,
          }
          if(this.thicknesGroupModel >= this.thicknesGroup_Data.length) {
            this.thicknesGroup_Data.push(obj)
          } else {
            this.thicknesGroup_Data[this.thicknesGroupModel] = obj
          }
          this.$emit('saveMThinckness', this.thicknesGroup_Data)
          this.$emit('toggleDialog')
        }
      },
      toggleDialog () {
        this.$emit('toggleDialog')
      }
    }
  }
</script>