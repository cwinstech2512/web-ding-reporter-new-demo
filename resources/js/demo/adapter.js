import {
  demoAccounts,
  demoDispatches,
  demoImportDetails,
  demoImportMasters,
  demoMenus,
  demoRoles,
  demoUser
} from './data'

function normalizeUrl (url = '') {
  return url.replace(/^https?:\/\/[^/]+\//, '').replace(/^\.\//, '').replace(/^\//, '').split('?')[0]
}

function makeResponse (config, data, status = 200) {
  return {
    data,
    status,
    statusText: status === 200 ? 'OK' : 'Error',
    headers: { 'x-frontend-demo': 'true' },
    config,
    request: null
  }
}

function dispatchSeries () {
  const today = new Date()
  const dately = []
  for (let offset = 0; offset < 7; offset += 1) {
    const day = new Date(today)
    day.setDate(today.getDate() - offset)
    dately.push({ day: day.getDate(), count: [8, 6, 11, 9, 13, 7, 10][offset] })
  }

  return {
    month: Array.from({ length: 12 }, (_, month) => ({ month: month + 1, count: [32, 28, 41, 36, 48, 44, 57, 51, 63, 59, 68, 72][month] })),
    dately
  }
}

function reportRecord (command) {
  const dispatch = { ...demoDispatches[0], function_path: `normal.${command}` }
  const report = {
    id: 1,
    dispatch_id: dispatch.id,
    report_version: dispatch.report_version,
    original_no: dispatch.original_no.replace('-PT-', `-${command.toUpperCase()}-`),
    job_name: dispatch.job_name,
    job_no: dispatch.serial_no,
    customer: dispatch.customer,
    exam_date: dispatch.exam_date,
    exam_place: dispatch.exam_place,
    applicable_code: dispatch.applicable_code,
    criteria: dispatch.criteria,
    procedure_no: dispatch.procedure_no,
    material: dispatch.material,
    inspector: dispatch.inspector,
    items: [],
    thickness_objs: [],
    updated_user: dispatch.updated_user,
    updated_at: dispatch.updated_at
  }
  dispatch[`report_${command}`] = report
  return dispatch
}

function importPayload (url) {
  const match = url.match(/^api\/import\/data\/(dialength|rt_ness|rtpr_ness)(?:\/(\d+))?$/)
  if (!match) return null
  return { data: match[2] ? demoImportDetails[match[1]] : demoImportMasters[match[1]] }
}

export default function createDemoAdapter (config) {
  const url = normalizeUrl(config.url)
  const method = (config.method || 'get').toLowerCase()
  let payload = { data: [], code: 200, message: 'Frontend demo response' }

  if (url === 'api/user') payload = demoUser
  if (url === 'api/login') payload = { token: 'frontend-demo-token' }
  if (url === 'api/analytics') {
    payload = {
      data: [
        { color: '#FD9A13', icon: 'mdi-sofa-single', title: '已派遣', value: 14, status: 0, actionIcon: 'mdi-clock-outline', actionText: '剛剛更新', updated_date: new Date().toISOString() },
        { color: 'blue darken-1', icon: 'mdi-file-word-outline', title: '報告製作中', value: 9, status: 2, actionIcon: 'mdi-clock-outline', actionText: '剛剛更新', updated_date: new Date().toISOString() },
        { color: 'red darken-1', icon: 'mdi-briefcase-search-outline', title: '審核中', value: 4, status: 3, actionIcon: 'mdi-clock-outline', actionText: '剛剛更新', updated_date: new Date().toISOString() },
        { color: 'green darken-1', icon: 'mdi-checkbox-marked-circle-outline', title: '通過審核', value: 58, status: 4, actionIcon: 'mdi-clock-outline', actionText: '剛剛更新', updated_date: new Date().toISOString() }
      ]
    }
  }
  if (url === 'api/analytics/dispatch') payload = { data: dispatchSeries() }
  if (url === 'api/roles') payload = method === 'get' ? { roles: demoRoles } : { role: demoRoles[1] }
  if (url === 'api/roles_all') payload = { roles: demoRoles }
  if (url === 'api/menu') payload = { menu: demoMenus }
  if (url === 'api/account') payload = method === 'get' ? { users: demoAccounts } : { user: demoAccounts[0] }
  if (url.indexOf('api/transport') === 0) {
    payload = { data: [{ id: 1, name: '展示車輛', num_no: 'DEMO-001' }] }
  }
  if (url === 'api/dispatch/data/list') payload = { data: demoDispatches }
  if (/^api\/dispatch\/data\/first\/\d+$/.test(url)) payload = { data: demoDispatches[0] }

  const reportMatch = url.match(/^api\/dispatch\/[^/]+\/(pt|rt|mt|ut|it|vt)\/?(\d+)?$/)
  if (reportMatch) {
    const record = reportRecord(reportMatch[1])
    payload = { data: reportMatch[2] ? record : [record] }
  }

  const imported = importPayload(url)
  if (imported) payload = imported
  if (url === 'api/data/rtpr_ness/lately') {
    payload = { data: { ...demoImportMasters.rtpr_ness[0], slave: demoImportDetails.rtpr_ness } }
  }
  if (url === 'api/data/rt_ness/lately') {
    payload = { data: { ...demoImportMasters.rt_ness[0], slave: demoImportDetails.rt_ness } }
  }
  if (url === 'api/import/dialength/search') {
    payload = { data: { ...demoImportMasters.dialength[0], slave: demoImportDetails.dialength } }
  }
  if (/^api\/sample\//.test(url)) payload = { data: [] }
  if (/^api\/(dispatch|dispatch_copy|dispatch_preview|dispatch_verify|dispatch_verify_back|dispatch_double_verify|dispatch_download)\/?/.test(url) && method !== 'get') {
    payload = { code: 1000, message: '展示模式：資料已在本機模擬完成' }
  }
  if (/^api\/import\/(rtness|rtprness|dialength)$/.test(url)) {
    payload = { code: 1000, message: '展示模式：匯入已完成', data: [] }
  }
  if (url === 'api/taipower/data/list' || url === 'api/taipower/data/daily_list') {
    payload = {
      data: [{
        id: 1,
        name: '展示檢測統計表',
        report_type: url.endsWith('daily_list') ? 'DAILY' : 'IMHQST',
        report_date: '2026-09-30',
        export_paths: '[]',
        upload_path: '',
        created_at: '2026-09-30 08:00:00',
        updated_at: '2026-09-30 09:00:00'
      }]
    }
  }
  if (/^api\/taipower\/(upload|remove)/.test(url)) {
    payload = { code: 1000, message: '展示模式：操作已完成', data: [] }
  }

  return new Promise(resolve => {
    setTimeout(() => resolve(makeResponse(config, payload)), 120)
  })
}
