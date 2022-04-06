<template>
  <v-dialog
    v-model="dialog"
    max-width="500px"
    persistent
  >
    <v-card>
      <v-card-title class="text-h5">
          複製派工單/報告至...
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" md="10">
              <v-text-field
                label="欲複製委託單號"
                outlined
                dense
                prepend-icon="mdi-counter"
                v-model="formCopyItem.serial_no"
                :disabled="true"
              ></v-text-field>
            </v-col>
          </v-row>
          <!-- <v-row>
            <v-col cols="12" md="10">
              <v-select
                ref="function_path"
                v-model="formCopyItem.function_path"
                :items="examFunctionPath"
                label="欲複製檢測分類"
                item-text="text"
                item-value="value"
                outlined
                dense
                prepend-icon="mdi-distribute-horizontal-center"
                :readonly="true"
                :disabled="true"
                :rules="[
                  (v) => !!v || '請填寫此欄位',
                ]"
              ></v-select>
            </v-col>
          </v-row> -->
          <v-row>
            <v-col cols="12" md="10">
              <v-select
                v-model="selectCopyFunctionPath"
                :items="examFunctionPath"
                label="複製到..."
                prepend-icon="mdi-distribute-horizontal-center"
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
          @click="saveDialog"
          :disabled="isDisableSave()"
        >
          開始複製
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'DispatchCopy',

    props: { 
      dialog: {
        type: Boolean,
        required: true
      },
      formCopyItem: {
        type: Object,
        required: true
      },
    //   listThickness_Datas: {
    //     type: Array,
    //     required: true
    //   },
    //   thicknesGroup_Data: {
    //     type: Array,
    //     required: true
    //   },
    //   thicknesGroupModel: {
    //     type: Number,
    //     required: true
    //   },
    //   channalType: {
    //     type: String,
    //     required: true
    //   },
    },

    data: () => ({
      isSave: false,
      selectCopyFunctionPath: {},
      examFunctionPath: [
        {text: '奇美PT', value: 'chimei.pt'},
        {text: '奇美RT', value: 'chimei.rt'},
        // {text: '奇美MT', value: 'chimei.mt'},
        // {text: '奇美UT', value: 'chimei.ut'},
        // {text: '奇美IT', value: 'chimei.it'},
        {text: '一般PT', value: 'normal.pt'},
        {text: '一般RT', value: 'normal.rt'},
        {text: '一般MT', value: 'normal.mt'},
        {text: '台塑油料廠', value: 'fpg.rtoil'},
        {text: '台塑烯烴三廠', value: 'fpg.rtolll'},
        {text: '台塑煉製三廠', value: 'fpg.rthree'}
      ],
    }),

    watch: {
      'formCopyItem.function_path': function (val, oldVal) {
        console.log(val, oldVal)
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
      isDisableSave () {
        if(this.selectCopyFunctionPath) return false
        else return true
      },
      saveDialog () {
        if(!this.isDisableSave()) {
          var obj = {
            selectCopyFunctionPath: this.selectCopyFunctionPath,
            fromDisptchId: this.formCopyItem.id,
          }
          this.$emit('saveDialog', obj)
          this.$emit('toggleDialog')
        }
      },
      toggleDialog () {
        this.$emit('toggleDialog')
      },
      
    }
  }
</script>