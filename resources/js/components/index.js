import Vue from 'vue'

const requireComponent = require.context('~/components', true, /\.vue$/)
const fruits = ["./App.vue", "./Footer.vue", "./Loading.vue", "./LocaleDropdown.vue", "./LoginWithGithub.vue", "./Menu.vue", "./Navbar.vue", "./PdfViewer.vue"]

for (const file of requireComponent.keys()) {
  if(fruits.includes(file)) continue
  const componentConfig = requireComponent(file)

  Vue.component(
    componentConfig.default.name,
    componentConfig.default || componentConfig,
  )
}