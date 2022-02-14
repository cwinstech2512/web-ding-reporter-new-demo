<template>
  <v-container
    id="account-view"
    fluid
    tag="section"
  >
    <material-card
      icon="mdi-clipboard-text"
      icon-small
      title="修改密碼"
      color="accent"
    >
    <v-container>
      <form>
        <v-text-field
          v-if="false"
          ref="old_password"
          v-model="old_password"
          :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
          :type="show1 ? 'text' : 'password'"
          @click:append="show1 = !show1"
          label="原始密碼"
          required
          :rules="[
            (v) => !!v || '請填寫此欄位',
          ]"
          outlined
        ></v-text-field>
        <v-text-field
          ref="new_password"
          v-model="new_password"
          :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
          :type="show1 ? 'text' : 'password'"
          @click:append="show1 = !show1"
          label="新密碼"
          required
          :rules="[
            (v) => !!v || '請填寫此欄位',
          ]"
          outlined
        ></v-text-field>
        <v-text-field
          ref="renew_password"
          v-model="renew_password"
          :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
          :type="show1 ? 'text' : 'password'"
          @click:append="show1 = !show1"
          label="確認新密碼"
          required
          :rules="[
            (v) => !!v || '請填寫此欄位',
          ]"
          outlined
        ></v-text-field>

        <v-btn
          class="mr-4"
          @click="submit"
        >
          確認更改
        </v-btn>
      </form>
    </v-container>

    </material-card>

  </v-container>
</template>
<script>
  // Utilities
  import { get } from 'vuex-pathify'
  import Vue from 'vue'
  import { mapGetters } from 'vuex'

  export default {
    name: 'ChangeView',

    middleware: 'auth',

    metaInfo () {
      return { title: this.$t('change_password') }
    },

    created () {
      this.initialize()
    },

    computed: {
      ...mapGetters({
        user: 'auth/user'
      }),
    },

    data: () => ({
      show1: false,
      old_password: null,
      new_password: null,
      renew_password: null,

    }),
    methods: {
      initialize () {

      },

      submit () {
        let that = this
        if(this.new_password !== this.renew_password) {
          that.$swal.fire({
            title: "錯誤!",
            text: "兩次密碼不相同！",
            type: "error",
            confirmButtonClass: "btn btn-danger"
          });
          return
        }
        let url = `api/account/` + this.user.id
        this.$swal.fire({
          title: '確定要修改密碼？',
          text: "修改密碼成功後將登出系統，並使用新密碼重新登入。",
          showCancelButton: true,
          confirmButtonText: '確認',
          showLoaderOnConfirm: true,
          cancelButtonText: '取消',
          preConfirm: () => {
            return this.$axios
              .fetchPut(url, {password: this.new_password})
              .then(res => {
                return res.data
              })
              .catch(err => {
                console.log(err)
              })
          },
          allowOutsideClick: () => !this.$swal.isLoading()
        }).then((result) => {
          try {
            if(result.value.code == 1000) {
              that.$swal.fire({
                title: "成功!",
                text: "系統將自動登出，請使用新密碼重新登入。",
                type: "success",
                confirmButtonClass: "btn btn-success"
              });
              this.logout()
            } else {
              that.$swal.fire({
                title: "錯誤!",
                text: "數據更新錯誤",
                type: "error",
                confirmButtonClass: "btn btn-danger"
              });
              this.closeView()
            }
          } catch {

          }
        })
      },

      async logout () {
        // Log out the user.
        await this.$store.dispatch('auth/logout')

        // Redirect to login.
        this.$router.push({ name: 'login' })
      },
    }
  }
</script>