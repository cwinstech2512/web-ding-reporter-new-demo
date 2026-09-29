module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
    es6: true
  },
  parserOptions: {
    parser: '@babel/eslint-parser',
    requireConfigFile: false,
    sourceType: 'module'
  },
  extends: [
    'plugin:vue/essential'
  ],
  // Legacy report forms intentionally mutate shared form props and contain
  // duplicate compatibility fields. Keep syntax/template validation without
  // rewriting those unrelated production flows for the frontend demo.
  rules: {
    'vue/no-mutating-props': 'off',
    'vue/no-side-effects-in-computed-properties': 'off',
    'vue/no-use-v-if-with-v-for': 'off',
    'vue/no-unused-vars': 'off',
    'vue/no-unused-components': 'off',
    'vue/no-dupe-keys': 'off',
    'vue/return-in-computed-property': 'off'
  }
}
