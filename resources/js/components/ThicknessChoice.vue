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
        <v-row v-if="thick_or_qu == 0">
          <v-col cols="6" md="5">
            <v-text-field
              label="SOD"
              outlined
              dense
              v-model="sod"
              :disabled="true"
            ></v-text-field>
          </v-col>
          <v-col cols="6" md="5">
            <v-text-field
              label="幾何模糊度"
              outlined
              dense
              v-model="geometric_unsharpness"
              :disabled="true"
            ></v-text-field>
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
        <v-row v-if="channalType == 'chimei_rt' || channalType == 'normal_rt'">
          <v-col cols="12" md="10">
            <v-select
              v-model="interpret"
              :items="listInterpret_Datas"
              label="底片判讀方式"
              item-text="text"
              item-value="value"
              dense
              outlined
            ></v-select>
            <v-select
              v-if="interpret === 0 || interpret === 1"
              v-model="sin_dou"
              :items="listSinDou_Datas[interpret]"
              label="底片判讀方式"
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
              v-model="iqi_no"
              :items="listIqiNo_Datas"
              label="像質計編號"
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
              v-model="iqi_location_mark"
              :items="listiQiMark_Datas"
              label="像質計位置"
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
      listRT_Datas: {
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
      energy_strength: {
        type: Number
      },
    },

    data: () => ({
      isSave: false,
      selectThickness: {},
      selectNominalSize: {},
      listInterpret_Datas:[
        {text: '單壁判讀Single View', value: 0},
        {text: '雙壁判讀Double View', value: 1},
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
      listiQiMark_Datas:[
        {text: '射源側 Source Side', value: 0},
        {text: '底片側 Film Side', value: 1},
      ],
      listIqiNo_Datas:[
        {text: 'ASTM', value: 0},
        {text: '1A', value: 1},
        {text: '1B', value: 2},
        {text: 'CNS', value: 3},
        {text: 'F02', value: 4},
        {text: 'F04', value: 5},
        {text: 'S02', value: 6},
      ],
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
      sin_dou: 0,
      interpret: '',
      iqi_location_mark: '',
      iqi_no: '',
      sod: '',
      geometric_unsharpness: '',
      exposure_time: ''
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
      sin_dou (val, oldVal) {
        console.log('sin_dou', val)
        this.roleCalcSod()
        this.roleCalcExTime()
      },
      sod (val, oldVal) {
        console.log('sod', val)
        this.roleCalcFOG()
      }
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
      roleCalcFOG(obj = null) {
        if(obj == null) {
          if(this.radiographic_tech !== '') {
            //單壁照相
            if(this.radiographic_tech == 0) {
              if(this.mThincknessNomalSize && this.mThincknessNomalSize != 0.0) {
                this.geometric_unsharpness = Math.round((2.9 * this.mThincknessNomalSize / 330) * 100) / 100
              }
            }
            //雙壁照相
            else if(this.radiographic_tech == 1) {
              if(this.sod && this.selectNominalSize) {
                if(this.sin_dou == 0) {
                  this.geometric_unsharpness = Math.round((3 * this.selectNominalSize.od_out / this.mThincknessNomalSize) * 100) / 100
                } else if(this.sin_dou == 1) {
                  this.geometric_unsharpness = Math.round((3 * this.selectNominalSize.od_out / this.mThincknessNomalSize) * 100) / 100
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
              if(this.radiographic_tech == 0) {
                this.sod = 330
              } else if(this.radiographic_tech == 1) {
                if(this.sin_dou == 0) {
                  this.sod = Math.round((this.mThincknessNomalSize * 8) * 100) / 100
                } else if(this.sin_dou == 1) {
                  this.sod = Math.round((this.selectNominalSize.od_out - this.mThincknessNomalSize ) * 100) / 100
                }
              }
            }
          }
        } 
        return 0.0
      },
      roleCalcExTime(obj = null, sod = 0.0) {
        console.log(this.layers)
        if(obj == null) {
          if(!this.mThincknessNomalSize || this.layers == '') {
            return
          }
          // console.log(this.listRT_Datas)
          const rt = this.listRT_Datas.find(element => element.thickness == Math.round(this.mThincknessNomalSize * this.layers))
          // console.log(rt)
          if(this.radiographic_tech !== '' && rt) {
            //單壁照相
            if(this.radiographic_tech == 0) {
              if(this.mThincknessNomalSize && this.mThincknessNomalSize != 0.0 && rt) {
                var a = ((this.sod + this.mThincknessNomalSize) * (this.sod + this.mThincknessNomalSize) / this.energy_strength)
                var b = (a * rt.numness * this.film_brand) / (25.4*25.4)
                console.log(b)
                this.exposure_time = this.formateExTime(b)
              }
            } else if(this.radiographic_tech == 1) {
              if(this.sin_dou == 0) {
                var a = this.sod * 9 / 8 * this.sod * 9 / 8 / this.energy_strength * this.mThincknessNomalSize * this.film_brand
                var b = a / 25.4 / 25.4
                this.exposure_time = this.formateExTime(b)
              } else if(this.sin_dou == 1) {
                if(this.selectNominalSize) {
                  var a = this.selectNominalSize.od_out * this.selectNominalSize.od_out / this.energy_strength * this.mThincknessNomalSize * this.film_brand
                  var b = a / 25.4 / 25.4
                  this.exposure_time = this.formateExTime(b)
                }
              }
            }
          }
        }
        console.log("exposure_time", this.exposure_time)
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
            sod: this.sod,
            interpret: this.interpret,
            sin_dou: this.sin_dou,
            geometric_unsharpness: this.geometric_unsharpness,
            exposure_time: this.exposure_time
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