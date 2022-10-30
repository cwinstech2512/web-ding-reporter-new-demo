// Pathify
import { make } from 'vuex-pathify'

// Data
const state = {
  drawer: null,
  drawerImage: true,
  mini: false,
  items: [
    {
      title: 'Dashboard',
      icon: 'mdi-view-dashboard',
      to: '/dashboard',
    },
    {
      title: '系統管理',
      icon: 'mdi-account-cog',
      items: [
        {
          title: '帳號管理',
          icon: 'mdi-account',
          to: '/account/',
        },
        {
          title: '權限管理',
          icon: 'mdi-account',
          to: '/authorization/',
        },
        {
          title: '設定',
          icon: 'mdi-cog',
          to: '/setting/',
        }
      ]
    },
    {
      title: '參照匯入',
      icon: 'mdi-file-import',
      items: [
        {
          title: '射源表',
          icon: 'mdi-microsoft-excel',
          to: '/dialength/',
        },
        {
          title: 'RT鋼板厚度係數表',
          icon: 'mdi-microsoft-excel',
          to: '/rtness/',
        },
        {
          title: 'RTPR管厚度及OD表',
          icon: 'mdi-microsoft-excel',
          to: '/rtprness/',
        }
      ]
    },
    {
      title: '派單',
      icon: 'mdi-book-plus-multiple-outline',
      to: '/dispatch/',
    },
    {
      title: '奇美檢測報告',
      icon: 'mdi-alpha-c-box-outline',
      items: [
        {
          title: '液滲檢測(PT)',
          icon: 'mdi-alpha-p',
          to: '/pt_chi/',
        },
        // {
        //   title: '射線檢測(RT)',
        //   icon: 'mdi-alpha-r',
        //   to: '/rt_chi/',
        // },
        // {
        //   title: '磁粒檢測(MT)',
        //   icon: 'mdi-alpha-m',
        //   to: '/mt_chi/',
        // },
        // {
        //   title: '超音波測厚檢測(UT)',
        //   icon: 'mdi-alpha-u',
        //   to: '/ut_chi/',
        // },
        // {
        //   title: '漏電檢測(IT)',
        //   icon: 'mdi-alpha-i',
        //   to: '/it_chi/',
        // }
      ]
    },
    // {
    //   title: '海基檢測報告',
    //   icon: 'mdi-alpha-s-box-outline',
    //   items: [
    //     {
    //       title: '磁粒檢測(MT)',
    //       icon: 'mdi-alpha-m',
    //       to: '/mt_sdm/',
    //     },
    //     {
    //       title: '檢測報告(VT)',
    //       icon: 'mdi-alpha-v',
    //       to: '/vt_sdm/',
    //     },
    //     {
    //       title: '檢測報告(UT)',
    //       icon: 'mdi-alpha-u',
    //       to: '/ut_sdm/',
    //     }
    //   ]
    // },
    {
      title: '一般檢測報告',
      icon: 'mdi-alpha-n-box-outline',
      items: [
        {
          title: '液滲檢測(PT)',
          icon: 'mdi-alpha-p',
          to: '/pt_nor/',
        },
        // {
        //   title: '射線檢測(RT)',
        //   icon: 'mdi-alpha-p',
        //   to: '/rt_nor/',
        // },
        // {
        //   title: '磁粒檢測(MT)',
        //   icon: 'mdi-alpha-m',
        //   to: '/mt_nor/',
        // },
        // {
        //   title: '檢測報告(UT)',
        //   icon: 'mdi-alpha-m',
        //   to: '/ut_nor/',
        // },
      ]
    },
    // {
    //   title: '台塑檢測報告',
    //   icon: 'mdi-alpha-f-box-outline',
    //   items: [
    //     {
    //       title: '烯烴三廠檢測(RT)',
    //       icon: 'mdi-alpha-r',
    //       to: '/rtolll_fpg/',
    //     },
    //     {
    //       title: '油料處檢測(RT)',
    //       icon: 'mdi-alpha-r',
    //       to: '/rtoil_fpg/',
    //     },
    //     {
    //       title: '煉製三廠(RT)',
    //       icon: 'mdi-alpha-r',
    //       to: '/rthree_fpg/',
    //     },
    //     {
    //       title: '超音波測厚檢測(UT)',
    //       icon: 'mdi-alpha-u',
    //       to: '/ut_fpg/',
    //     },
    //     {
    //       title: '漏電檢測(IT)',
    //       icon: 'mdi-alpha-i',
    //       to: '/it_fpg/',
    //     }
    //   ]
    // },
    {
      title: 'Icons',
      icon: 'mdi-chart-bubble',
      to: '/components/icons/',
    },
    {
      title: 'Google Maps',
      icon: 'mdi-map-marker',
      to: '/maps/google/',
    },
    {
      title: 'Notifications',
      icon: 'mdi-bell',
      to: '/components/notifications/',
    },
  ],
}

const mutations = make.mutations(state)

const actions = {
  ...make.actions(state),
  init: async ({ dispatch }) => {
    //
  },
}

const getters = {}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}
