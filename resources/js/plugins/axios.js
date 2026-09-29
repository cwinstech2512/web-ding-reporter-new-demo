import axios from 'axios'
import store from '~/store'
import router from '~/router'
import Swal from 'sweetalert2'
import i18n from '~/plugins/i18n'
import createDemoAdapter from '~/demo/adapter'

axios.defaults.timeout = 1000 * 60 // 响应时间
axios.defaults.headers.post['Content-Type'] = 'application/json' // 配置请求头
const frontendDemo = process.env.MIX_FRONTEND_DEMO === 'true'
axios.defaults.baseURL = frontendDemo
  ? './'
  : ((process.env.MIX_APP_ENV === 'local') ? 'http://127.0.0.1:8000/' : process.env.MIX_APP_URL + '/')

if (frontendDemo) {
  axios.defaults.adapter = createDemoAdapter
  console.info('[Frontend Demo] API calls are served locally and never reach the Laravel backend.')
}

// Request interceptor
axios.interceptors.request.use(request => {
  const token = store.getters['auth/token']
  if (token) {
    request.headers.common.Authorization = `Bearer ${token}`
  }

  const locale = store.getters['lang/locale']
  if (locale) {
    request.headers.common['Accept-Language'] = locale
  }

  // request.headers['X-Socket-Id'] = Echo.socketId()

  return request
})

// Response interceptor
axios.interceptors.response.use(response => response, error => {
  const { status } = error.response

  if (status >= 500) {
    Swal.fire({
      icon: 'error',
      title: i18n.t('error_alert_title'),
      text: i18n.t('error_alert_text'),
      reverseButtons: true,
      confirmButtonText: i18n.t('ok'),
      cancelButtonText: i18n.t('cancel')
    })
  }

  if (status === 401 && store.getters['auth/check']) {
    Swal.fire({
      icon: 'warning',
      title: i18n.t('token_expired_alert_title'),
      text: i18n.t('token_expired_alert_text'),
      reverseButtons: true,
      confirmButtonText: i18n.t('ok'),
      cancelButtonText: i18n.t('cancel')
    }).then(() => {
      store.commit('auth/LOGOUT')

      router.push({ name: 'login' })
    })
  }

  return Promise.reject(error)
})

// 返回一个Promise(发送post请求)
export function fetchPost (url, param) {
  return new Promise((resolve, reject) => {
    axios.post(url, {params: param})
      .then(response => {
        resolve(response)
      }, err => {
        reject(err)
      })
      .catch((error) => {
        reject(error)
      })
  })
}
/// /返回一个Promise(发送get请求)
export function fetchGet (url, param) {
  return new Promise((resolve, reject) => {
    axios.get(url, { params: param })
      .then(response => {
        resolve(response)
      }, err => {
        reject(err)
      })
      .catch((error) => {
        reject(error)
      })
  })
}
/// /返回一个Promise(发送 delete 请求)
export function fetchDelete (url, param) {
  return new Promise((resolve, reject) => {
    axios.delete(url, { params: param })
      .then(response => {
        resolve(response)
      }, err => {
        reject(err)
      })
      .catch((error) => {
        reject(error)
      })
  })
}

/// /返回一个Promise(发送 put 请求)
export function fetchPut (url, param) {
  return new Promise((resolve, reject) => {
    axios.put(url, { params: param })
      .then(response => {
        resolve(response)
      }, err => {
        reject(err)
      })
      .catch((error) => {
        reject(error)
      })
  })
}

// 返回一个Promise(发送post请求)
export function fetchUpload (url, params) {
  return new Promise((resolve, reject) => {
    axios.post(url, params, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(response => {
        resolve(response)
      }, err => {
        reject(err)
      })
      .catch((error) => {
        reject(error)
      })
  })
}
export default {
  fetchPost,
  fetchGet,
  fetchPut,
  fetchDelete,
  fetchUpload
}
