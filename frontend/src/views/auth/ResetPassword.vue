<template>
    <div class="md-layout text-center">
        <div class="md-layout-item md-size-50 md-medium-size-50 md-small-size-70 md-xsmall-size-100">
            <ValidationObserver ref="form">
                <form>
                    <login-card header-color="green">
                        <h3 slot="title" class="title">{{ $t('resetPassword.title') }}</h3>
                        <template slot="inputs">
                            <div class="alert alert-success mb-4" v-if="success">
                                {{ success }} <router-link :to="{ name: 'login' }" class="small">{{ $t('pages.login') }}</router-link>
                            </div>

                            <template v-else>
                                <ValidationProvider name="email" rules="required|email" v-slot="{ passed, failed, errors }">
                                    <md-field class="md-form-group" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                        <md-icon>email</md-icon>
                                        <label>{{ $t('user.property.email') }}</label>
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

                                <ValidationProvider name="password" rules="required|min:8" v-slot="{ passed, failed, errors }">
                                    <md-field class="md-form-group" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                        <md-icon>lock_outline</md-icon>
                                        <label>{{ $t('user.property.password') }}</label>
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

                                <ValidationProvider name="password_confirmation" rules="required|password:@password" v-slot="{ passed, failed, errors }">
                                    <md-field class="md-form-group" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                        <md-icon>lock_outline</md-icon>
                                        <label>{{ $t('user.property.password_confirmation') }}</label>
                                        <md-input v-model="form.password_confirmation" type="password"></md-input>
                                        <span class="md-error" v-show="failed">{{ errors[0] }}</span>

                                        <slide-y-down-transition>
                                            <md-icon class="error" v-show="failed">close</md-icon>
                                        </slide-y-down-transition>
                                        <slide-y-down-transition>
                                            <md-icon class="success" v-show="passed">done</md-icon>
                                        </slide-y-down-transition>
                                    </md-field>
                                </ValidationProvider>
                            </template>
                        </template>
                        <template slot="footer">
                            <md-button class="md-simple md-success md-lg" :to="{ name: 'login' }" v-if="success">
                                {{ $t('pages.login') }}
                            </md-button>
                            <md-button  class="md-simple md-success md-lg" @click="resetPasswordClick" v-else>
                                <md-progress-spinner v-if="loading" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner> {{ $t('resetPassword.submitBtn') }}
                            </md-button>
                        </template>
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
    import {required, email, min} from "vee-validate/dist/rules";
    import i18n from '../../lang';

    extend("email", email);
    extend("min", min);
    extend("required", required);

    extend('password', {
        params: ['target', 'other'],
        validate(value, { target }) {
            return value === target;
        },
        message: (_, values) =>  {
            return i18n.t('validation.password', { attribute: values.target });
        }
    });

    export default {
        title () {
            return this.$t('pages.resetPassword');
        },
        name: "ResetPassword",
        components: {
            LoginCard,
            SlideYDownTransition
        },
        data() {
            return {
                form: {
                    email: this.$route.params.email,
                    password: null,
                    password_confirmation: null,
                    token: this.$route.params.token
                },
                loading: false,
                success: ''
            };
        },
        methods: {
            resetPasswordClick() {
                this.$refs.form.validate().then(valid => {
                    if (valid) {
                        this.resetPassword();
                    }
                });
            },
            resetPassword() {
                let self = this;
                this.loading = true;
                axios.post(process.env.VUE_APP_LARAVEL_ENDPOINT + '/api/reset-password', self.form)
                    .then(response => {
                        this.success = response.data.status;
                        this.loading = false;
                    })
                    .catch(error => {
                        this.$refs.form.setErrors(error.response.data.errors);
                        this.loading = false;
                    });
            }
        }
    }
</script>

<style scoped>

</style>
