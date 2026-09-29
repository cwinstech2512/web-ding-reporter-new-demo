export const frontendDemo = process.env.MIX_FRONTEND_DEMO === 'true'

const menuPaths = [
  '/dashboard', '/account/', '/authorization/', '/setting/',
  '/dialength/', '/rtness/', '/rtprness/', '/dispatch/',
  '/pt_chi/', '/pt_nor/', '/mt_nor/', '/ut_nor/', '/tp_IMHQST/',
  '/components/icons/', '/maps/google/', '/components/notifications/'
]

const routeKeys = [
  'dashboard', 'account', 'authorization', 'setting', 'dialength', 'rtness',
  'rtprness', 'dispatch', 'pt_chi', 'pt_nor', 'mt_nor', 'ut_nor', 'tp_IMHQST',
  'profile', 'change_password'
]

export const demoMenus = [...menuPaths, ...routeKeys].map((path, index) => ({
  id: index + 1,
  title: path,
  menu_info: { path }
}))

export const demoUser = {
  id: 1,
  name: '前端展示帳號',
  email: 'demo@example.com',
  role: 'admin',
  menu: {
    menus: demoMenus
  }
}

export const demoRoles = [
  { id: 1, name: '系統管理員', slug: 'admin', menus: demoMenus },
  { id: 2, name: '報告人員', slug: 'reporter', menus: demoMenus.filter(menu => !['/account/', '/authorization/'].includes(menu.title)) }
]

export const demoAccounts = [
  { id: 1, name: '前端展示帳號', email: 'demo@example.com', role: 1 },
  { id: 2, name: '檢測報告人員', email: 'reporter@example.com', role: 2 }
]

export const demoDispatches = [{
  id: 1,
  serial_no: '20260930-0001',
  original_no: 'XID-PT-20260930-0001',
  customer: '展示客戶股份有限公司',
  concat_name: '王先生',
  concat_phone: '02-2345-6789',
  job_name: '管線焊道非破壞檢測',
  exam_place: '展示廠區 A 棟',
  exam_date: '2026-09-30',
  e_exam_date: '2026-10-02',
  status: 1,
  transport: 1,
  function_path: 'normal.pt,normal.mt,normal.ut',
  report_version: 'A',
  applicable_code: 'CNS 12618',
  criteria: '依程序書驗收',
  procedure_no: 'NDT-DEMO-001',
  material: 'SUS304',
  delegator: '展示委託人',
  inspector: '前端展示帳號',
  updated_user: '前端展示帳號',
  updated_at: '2026-09-30 09:00:00',
  created_at: '2026-09-29 14:30:00'
}]

export const demoImportMasters = {
  dialength: [{ id: 1, identify: 'IR-192', description: '射源距離與曝光時間展示表', file_name: 'demo-dia-length.xlsx', updated_user: '前端展示帳號', updated_at: '2026-09-30 08:30:00' }],
  rt_ness: [{ id: 1, description: 'RT 鋼板厚度係數展示表', file_name: 'demo-rt-thickness.xlsx', updated_user: '前端展示帳號', updated_at: '2026-09-30 08:35:00' }],
  rtpr_ness: [{ id: 1, description: 'RTPR 管厚度及 OD 展示表', file_name: 'demo-rtpr.xlsx', updated_user: '前端展示帳號', updated_at: '2026-09-30 08:40:00' }]
}

export const demoImportDetails = {
  dialength: [{ id: 1, dia_length_master_id: 1, data_value: '12.5', use_date: '2026-09-30' }],
  rt_ness: [{ id: 1, rt_ness_master_id: 1, thickness: '10', numness: '1.25' }],
  rtpr_ness: [
    { id: 1, rtpr_ness_master_id: 1, nominal_size: '2', od: '60.3', sch40: '3.91', sch80: '5.54', std: '3.91' },
    { id: 2, rtpr_ness_master_id: 1, nominal_size: '4', od: '114.3', sch40: '6.02', sch80: '8.56', std: '6.02' }
  ]
}
