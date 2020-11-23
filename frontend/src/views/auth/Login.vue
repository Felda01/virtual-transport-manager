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
          <md-input v-model="email" type="email"></md-input>
        </md-field>
        <md-field class="md-form-group" slot="inputs">
          <md-icon>lock_outline</md-icon>
          <label>{{ $t('user.password') }}...</label>
          <md-input v-model="password"></md-input>
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
  import { SIGN_IN_USER_MUTATION } from '@/graphql/mutations/auth';

  export default {
    title () {
      return this.$t('pages.login');
    },
    components: {
      LoginCard
    },
    data() {
      return {
        email: null,
        password: null
      };
    },
    methods: {
      login() {
        const { email, password } = this.$data
        let self = this;
        axios.get('https://virtual-transport-manager.ddev.site/sanctum/csrf-cookie').then(response => {
          self.$apollo.mutate({
            mutation: SIGN_IN_USER_MUTATION,
            variables: {
              email,
              password
            },
            client: 'apolloClientDefault'
          }).then(result => {
            console.log(result);
          }).catch(error => {
            console.log(error);
          })
        });
      }

    }
  };
</script>
