<template>
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
</template>
<script>
export default {
  name: 'DialogUploadSample',

  props: {
    formTitle: {
      type: String,
      default: '報告樣板'
    },
    dialogSample: {
      type: Boolean,
      default: false
    },
    sampleTypeCode: {
      type: String,
      default: ''
    }
  },

  computed: {
    logItems() {
      return [
        {
          name: '歷史紀錄',
          children: this.sampleItems,
        },
      ]
    },
  },

  data: () => ({
    excelFile: null,
    pdfUrl: '',
    sampleItems: [],
  }),

  methods: {
    closeSample () {
      this.dialogSample = false
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
  }
}
</script>