import store from '~/store'

export default async (to, from, next) => {
  if (!store.getters['auth/check'] && store.getters['auth/token']) {
    try {
      await store.dispatch('auth/fetchUser')
      let path = to.path
      if(path.length > 0) {
      	let key = path.substr(path.indexOf('/') + 1)
      	let user = store.getters['auth/user']
      	if(!user.menu.menus.find(e => Object.values(e.menu_info).find((obj) => {return obj == key}))) {
      		next({ name: 'home' })
      		return
      	}
      }
    } catch (e) { }
  }

  next()
}
