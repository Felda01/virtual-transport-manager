<template>
    <div class="md-layout text-center">
        <div class="md-layout-item md-size-50 md-medium-size-50 md-small-size-70 md-xsmall-size-100">
            <ValidationObserver ref="form">
                <form>
                    <login-card header-color="green">
                        <h3 slot="title" class="title">{{ $t('forgotPassword.title') }}</h3>

                        <template slot="inputs">
                            <div class="alert alert-success mb-4" v-if="success">
                                {{ success }}
                            </div>
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
                        </template>
                        <md-button slot="footer" class="md-simple md-success md-lg" @click="sendResetLinkClick">
                            <md-progress-spinner v-if="loading" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner> {{ $t('forgotPassword.submitBtn') }}
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
    import { required, email } from "vee-validate/dist/rules";

    extend("email", email);
    extend("required", required);

    export default {
        title () {
            return this.$t('pages.forgotPassword');
        },
        name: "ForgotPassword",
        components: {
            LoginCard,
            SlideYDownTransition
        },
        data() {
            return {
                form: {
                    email: null,
                },
                loading: false,
                success: ''
            };
        },
        methods: {
            sendResetLinkClick() {
                this.$refs.form.validate().then(valid => {
                    if (valid) {
                        this.sendResetLink();
                    }
                });
            },
            sendResetLink() {
                let self = this;
                this.loading = true;
                axios.post(process.env.VUE_APP_LARAVEL_ENDPOINT + '/api/forgot-password', self.form)
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
