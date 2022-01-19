<template>
  <v-container
    id="dashboard-view"
    fluid
    tag="section"
  >
    <v-row>
      <v-col cols="12">
        <v-row>
          <v-col
            v-for="(chart, i) in charts"
            :key="`chart-${i}`"
            cols="12"
            md="6"
            lg="4"
          >
            <material-chart-card
              :color="chart.color"
              :data="chart.data"
              :options="chart.options"
              :responsive-options="chart.responsiveOptions"
              :title="chart.title"
              :type="chart.type"
              ref="chartlist_line"
            >
              <template #subtitle>
                <div class="font-weight-light text--secondary">
                  <div v-html="chart.subtitle" />
                </div>
              </template>

              <template #actions>
                <v-icon
                  class="mr-1"
                  small
                >
                  mdi-clock-outline
                </v-icon>

                <span
                  class="text-caption grey--text font-weight-light"
                  v-text="chart.time"
                />
              </template>
            </material-chart-card>
          </v-col>
        </v-row>
      </v-col>

      <v-col
        v-for="({ actionIcon, actionText, ...attrs }, i) in stats"
        :key="i"
        cols="12"
        md="6"
        lg="3"
      >
        <material-stat-card v-bind="attrs">
          <template #actions>
            <v-icon
              class="mr-2"
              small
              v-text="actionIcon"
            />
            <div class="text-truncate">
              {{ actionText }}
            </div>
          </template>
        </material-stat-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  // Utilities
  import { get } from 'vuex-pathify'
  import Vue from 'vue'
  import moment from 'moment'

  const lineSmooth = Vue.chartist.Interpolation.cardinal({
    tension: 0,
  })

  export default {
    name: 'DashboardView',

    middleware: 'auth',

    metaInfo () {
      return { title: this.$t('home') }
    },

    created () {
      this.initialize()
    },
    destroyed(){
      clearInterval(this.interval)
    },

    data: () => ({
      charts: [{
        type: 'Bar',
        color: 'primary',
        title: '月統計',
        subtitle: '統計每月製作報告數量',
        time: 'updated 10 minutes ago',
        data: {
          labels: [],
          series: [[]],
        },
        options: {
          axisX: {
            showGrid: false,
          },
          low: 0,
          high: 1000,
          chartPadding: {
            top: 0,
            right: 5,
            bottom: 0,
            left: 0,
          },
        },
        responsiveOptions: [
          ['screen and (max-width: 640px)', {
            seriesBarDistance: 5,
            axisX: {
              labelInterpolationFnc: function (value) {
                return ''
              },
            },
          }],
        ],
      }, {
        type: 'Line',
        color: 'success',
        title: '近七日統計',
        subtitle: '統計最近七日數量',
        time: 'updated 10 minutes ago',
        data: {
          labels: [],
          series: [[]],
        },
        options: {
          lineSmooth,
          low: 0,
          high: 5, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
          chartPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0,
          },
        },
      }, 
      // {
      //   type: 'Line',
      //   color: 'info',
      //   title: 'Completed Tasks',
      //   subtitle: 'Last Campaign Performance',
      //   time: 'campaign sent 26 minutes ago',
      //   data: {
      //     labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
      //     series: [
      //       [12, 17, 7, 17, 23, 18, 38],
      //     ],
      //   },
      //   options: {
      //     lineSmooth,
      //     low: 0,
      //     high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
      //     chartPadding: {
      //       top: 0,
      //       right: 0,
      //       bottom: 0,
      //       left: 0,
      //     },
      //   },
      // }
      ],
      labelMonthName: ['Ja', 'Fe', 'Ma', 'Ap', 'Mai', 'Ju', 'Jul', 'Au', 'Se', 'Oc', 'No', 'De'],
      stats: [],
      interval: null,
    }),

    computed: {
      sales: get('sales/sales'),
      totalSales () {
        return this.sales.reduce((acc, val) => acc + val.salesInM, 0)
      },
    },

    watch: {
    },

    methods: {
      initialize () {
        this.getDash()
        this.getDisp()
        this.interval=setInterval(() => {
          this.stats = this.stats.map(m => {
            m.actionText = 'updated' + moment(m.updated_date).fromNow();
            return m;
          })
        }, 1000)
        
        // this.renderChart(this.charts[0].data, this.charts[0].option)
      },
      getDash () {
        let _ = this
        let url = 'api/analytics'
        let params = {}
        this.$axios
          .fetchGet(url, params)
            .then(res => {
              console.log(res.data)
              this.stats = res.data.data.map((_, idx) => ({
                actionIcon: res.data.data[idx].actionIcon,
                actionText: res.data.data[idx].actionText,
                color: res.data.data[idx].color,
                icon: res.data.data[idx].icon,
                status: res.data.data[idx].status,
                title: res.data.data[idx].title,
                value: res.data.data[idx].value.toString(),
                updated_date: res.data.data[idx].updated_date,
              }))
              console.log(_.stats)
            })
            .catch(err => {
            })
      },
      getDisp () {
        let _ = this
        let url = 'api/analytics/dispatch'
        let params = {}
        this.$axios
          .fetchGet(url, params)
            .then(res => {
              console.log(res.data)
              if(res.data.data) {
                if(res.data.data.month) {
                  var objMonth = moment().add(1, 'months')
                  var resMon = res.data.data.month
                  _.labelMonthName.forEach(element => {
                    var nMon = objMonth.add(-1, 'months').month()
                    let a = resMon.find(e => (e.month - 1) == nMon)

                    _.charts[0].data.labels.push(_.labelMonthName[nMon]) 
                    _.charts[0].data.series[0].push(a? a.count : 0) 
                  })
                  _.charts[0].options.high = Math.max(..._.charts[0].data.series[0])
                }

                if(res.data.data.dately) {
                  var resDately = res.data.data.dately
                  var objDay = moment().add(1, 'days')

                  for(var i = 0; i < 7; i++) {
                    var nDay = objDay.add(-1, 'days').format('DD')
                    let a = resDately.find(e => (e.day) == parseInt(nDay))
                    _.charts[1].data.labels.push(nDay + '日')
                    _.charts[1].data.series[0].push(a? a.count : 0) 
                  }
                  _.charts[1].options.high = Math.max(..._.charts[0].data.series[0])
                }
                
              }
            })
            .catch(err => {
            })
      }
    }
  }
</script>
