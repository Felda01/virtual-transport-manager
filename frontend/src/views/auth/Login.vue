<template>
  <div class="md-layout text-center">
    <div
      class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100"
    >
      <ValidationObserver ref="form" v-slot="{ handleSubmit }">
        <form @submit.prevent="handleSubmit(login)">
          <login-card header-color="green">
            <h2 slot="title" class="title">{{ $t('login.logIn') }}</h2>

            <ValidationProvider name="email" rules="required" v-slot="{ passed, failed, errors }" slot="inputs">
              <md-field class="md-form-group" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                <md-icon>email</md-icon>
                <label>{{ $t('user.property.email') }}...</label>
                <md-input v-model="form.email" type="email"></md-input>
                <span class="md-error" v-show="failed">{{ errors[0] }}</span>

                <slide-y-down-transition>
                  <md-icon class="error" v-show="failed">close</md-icon>
                </slide-y-down-transition>
                <slide-y-down-transition>
                  <md-icon class="success" v-show="passed">done</md-icon>
                </slide-y-down-transition>
              </md-field>
            </ValidationProvider>

            <ValidationProvider name="password" slot="inputs" rules="required" v-slot="{ passed, failed, errors }">
              <md-field class="md-form-group" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                <md-icon>lock_outline</md-icon>
                <label>{{ $t('user.property.password') }}...</label>
                <md-input v-model="form.password" type="password"></md-input>
                <span class="md-error" v-show="failed">{{ errors[0] }}</span>

                <slide-y-down-transition>
                  <md-icon class="error" v-show="failed">close</md-icon>
                </slide-y-down-transition>
                <slide-y-down-transition>
                  <md-icon class="success" v-show="passed">done</md-icon>
                </slide-y-down-transition>
              </md-field>
            </ValidationProvider>
            <md-button slot="footer" class="md-simple md-success md-lg" @click="login">
              <md-progress-spinner v-if="loading" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner> {{ $t('login.submitBtn') }}
            </md-button>
          </login-card>
    </div>
  </div>
</template>

<script>
  import { LoginCard } from "@/components";
  import axios from 'axios';
  import { SlideYDownTransition } from "vue2-transitions";
  import { extend } from "vee-validate";
  import { required, email, min } from "vee-validate/dist/rules";

  extend("email", email);
  extend("required", required);
  extend("min", min);

  export default {
    title () {
      return this.$t('pages.login');
    },
    components: {
      LoginCard,
      SlideYDownTransition
    },
    data() {
      return {
        form: {
          email: null,
          password: null
        },
        loading: false
      };
    },
    methods: {
      login() {
        let self = this;
        this.loading = true;
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
            this.$refs.form.setErrors(error.response.data.errors);
            this.loading = false;
          });
      }
    }
  };
</script>
