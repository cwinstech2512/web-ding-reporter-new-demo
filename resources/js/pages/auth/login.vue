<template>
  <v-form ref="form" @submit.prevent="validate" @keydown="form.onKeydown($event)">
    <v-text-field
      label="帳號"
      prepend-icon="mdi-account"
      v-model="form.username"
      required
      :error-messages="errorMessages.email"
    ></v-text-field>

    <v-text-field
      label="密碼"
      prepend-icon="mdi-lock"
      type="password"
      v-model="form.password"
      required
    ></v-text-field>

    <v-checkbox
      v-model="form.remember"
      label="記住密碼"
    ></v-checkbox>

    <div class="text-center">
      <v-btn
        :loading="loading"
        color="primary"
        large
        type="submit"
        text
        rounded
      >
        {{ '登入' }}</v-btn
      >
    </div>
  </v-form>
</template>

<script>
import { mapActions } from "vuex";
import Form from 'vform'
import Cookies from 'js-cookie'

export default {
  middleware: 'guest',
  layout: 'auth',

  metaInfo () {
    return { title: this.$t('login') }
  },

  data() {
    return {
      form: {
        username: null,
        password: null,
        remember: false,
      },
      errorMessages: {},
      loading: false,
      copyForm: new Form,
    };
  },
  methods: {
    ...mapActions({
      login: "auth/login",
    }),
    async validate() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        // this.copyForm = new Form(this.$refs.form)
        // // this.copyForm.email += '@ehsnchat.com.tw' 
        // // Submit the form.
        // const { data } = await this.$refs.form.post('/api/login')

        // // Save the token.
        // this.$store.dispatch('auth/saveToken', {
        //   token: data.token,
        //   remember: this.remember
        // })

        // // Fetch the user.
        // await this.$store.dispatch('auth/fetchUser')

        // // Redirect home.
        // this.redirect()

        try {
          await this.login(this.form);
          this.redirect()
        } catch (e) {
          if (e.errors) {
            this.errorMessages = e.errors;
            return;
          }

          this.errorMessages = { email: [e.message] };
        } finally {
          this.loading = false;
        }
      }
    },
    redirect () {
      const intendedUrl = Cookies.get('intended_url')

      if (intendedUrl) {
        Cookies.remove('intended_url')
        this.$router.push({ path: intendedUrl })
      } else {
        this.$router.push({ name: 'home' })
      }
    }
  },
};
</script>
