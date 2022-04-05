function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  { 
    path: '/', 
    name: 'home',
    redirect: { path: 'dashboard' },
    component: page('home'),
    children: [
      {
        path: '/dashboard',
        name: '統計分析',
        component: page('home/Dashboard.vue')
      },
      {
        path: '/account',
        name: '帳號管理',
        component: page('home/Account.vue')
      },
      {
        path: '/authorization',
        name: '權限管理',
        component: page('home/Authorization.vue')
      },
      {
        path: '/change_password',
        name: '修改密碼',
        component: page('auth/password/Change.vue')
      },
      {
        path: '/profile',
        name: '個人中心',
        component: page('auth/Profile.vue')
      },
      // {
      //   path: 'ss',
      //   name: '相似問題選單連結',
      //   component: page('home/manager_qa_ss.vue')
      // },
      // {
      //   path: 'history',
      //   name: '歷史對話紀錄',
      //   component: page('home/chat_history.vue')
      // },
      // {
      //   path: 'agent',
      //   name: '客服中心',
      //   component: page('home/r_agent.vue')
      // },
    ] 
  },
  { 
    path: '/import', 
    name: 'import',
    redirect: { path: 'dialength' },
    component: page('home'),
    children: [
      {
        path: '/dialength',
        name: '射源表',
        component: page('import/DiameterLength.vue')
      },
      {
        path: '/rtness',
        name: 'RT鋼板厚度係數表',
        component: page('import/RTthickness.vue')
      },
      {
        path: '/rtprness',
        name: 'RTPR管厚度及OD表',
        component: page('import/RTPRthickness.vue')
      },
    ]
  },
  { 
    path: '/dispatch', 
    name: 'dispath',
    redirect: { path: 'dispatch' },
    component: page('home'),
    children: [
      {
        path: '/dispatch',
        name: '派單',
        component: page('dispatch/Dispatch.vue')
      },
    ]
  },
  { 
    path: '/report', 
    name: 'report',
    redirect: { path: 'dispatch' },
    component: page('home'),
    children: [
      {
        path: '/pt_chi',
        name: '液滲檢測',
        component: page('report/chi/PT.vue')
      },
      {
        path: '/rt_chi',
        name: '射線檢測',
        component: page('report/chi/RT.vue')
      },
      {
        path: '/mt_chi',
        name: '磁粒檢測',
        component: page('report/chi/MT.vue')
      },
      {
        path: '/ut_chi',
        name: '超音波測厚檢測',
        component: page('report/chi/UT.vue')
      },
      {
        path: '/it_chi',
        name: '漏電檢測',
        component: page('report/chi/IT.vue')
      },
      {
        path: '/rtoil_fpg',
        name: '油料處檢測',
        component: page('report/fpg/RT_oil.vue')
      },
      {
        path: '/rtolll_fpg',
        name: '烯烴三廠檢測',
        component: page('report/fpg/RT_olll.vue')
      },
      {
        path: '/pt_nor',
        name: '液滲檢測',
        component: page('report/normal/PT.vue')
      },
      {
        path: '/rt_nor',
        name: '射線檢測',
        component: page('report/normal/RT.vue')
      },
      {
        path: '/mt_nor',
        name: '磁粒檢測',
        component: page('report/normal/MT.vue')
      },
    ]
  },
  {
    path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ]
  },

  { path: '*', component: page('errors/404.vue') }
]
