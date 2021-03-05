<template>
  <div class="md-layout text-center">
    <div class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100">
      <ValidationObserver ref="form" v-slot="{ handleSubmit }">
        <form @submit.prevent="handleSubmit(login)">
          <login-card header-color="green">
            <h2 slot="title" class="title">{{ $t('login.logIn') }}</h2>
            <template slot="inputs">
              <ValidationProvider name="email" rules="required|email" v-slot="{ passed, failed, errors }">
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

              <ValidationProvider name="password" rules="required" v-slot="{ passed, failed, errors }">
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

              <div class="d-flex justify-space-between">
                <router-link :to="{ name: 'register' }" class="small">
                  {{ $t('login.register') }}
                </router-link>
                <router-link :to="{ name: 'forgotPassword' }" class="small">
                  {{ $t('login.forgot_password') }}
                </router-link>
              </div>

            </template>
            <md-button slot="footer" class="md-simple md-success md-lg" @click="login">
              <md-progress-spinner v-if="loading" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner> {{ $t('login.submitBtn') }}
            </md-button>
          </login-card>
        </form>
      </ValidationObserver>
    </div>
  </div>
</template>

<script>
  import { LoginCard } from "@/components";
  import axios from 'axios';
  import { SlideYDownTransition } from "vue2-transitions";
  import { extend } from "vee-validate";
  import { required, email, min } from "vee-validate/dist/rules";
  import i18n from "../../lang";

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
        axios.post(process.env.VUE_APP_LARAVEL_ENDPOINT + '/api/login', self.form)
          .then(response => {
            let payload = {
              expiresIn: response.data.expiresIn
            };

            this.$store.dispatch('setToken', payload).then(() => {
              if (this.$route.query.redirect) {
                this.$router.push(this.$route.query.redirect).catch(() => {});
              } else {
                this.$router.push({ name: 'dashboard'}).catch(() => {});
              }
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
