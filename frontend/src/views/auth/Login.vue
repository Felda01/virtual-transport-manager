<template>
  <div class="md-layout text-center">
    <div
      class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100"
    >
      <login-card header-color="green">
        <h2 slot="title" class="title">{{ $t('login.logIn') }}</h2>
        <md-field class="md-form-group" slot="inputs">
          <md-icon>email</md-icon>
          <label>{{ $t('user.email') }}...</label>
          <md-input v-model="form.email" type="email"></md-input>
        </md-field>
        <md-field class="md-form-group" slot="inputs">
          <md-icon>lock_outline</md-icon>
          <label>{{ $t('user.password') }}...</label>
          <md-input v-model="form.password"></md-input>
        </md-field>
        <md-button slot="footer" class="md-simple md-success md-lg" @click="login">
          {{ $t('login.submitBtn') }}
        </md-button>
      </login-card>
    </div>
  </div>
</template>

<script>
  import { LoginCard } from "@/components";
  import axios from 'axios';
  import Cookies from 'js-cookie'

  export default {
    title () {
      return this.$t('pages.login');
    },
    components: {
      LoginCard
    },
    data() {
      return {
        form: {
          email: null,
          password: null
        }
      };
    },
    methods: {
      login() {
        let self = this;
        axios.post('https://virtual-transport-manager.ddev.site/api/login', self.form)
          .then(response => {
            let payload = {
              token: response.data.token,
              expiresIn: response.data.expiresIn
            };

            this.$store.dispatch('setToken', payload).then(() => {
              this.$router.push(this.$route.query.redirect || { name: this.$t('pages.adminDashboard') });
            });
          })
          .catch(error => {
            console.log(error);
          });
      }
    }
  };
</script>
