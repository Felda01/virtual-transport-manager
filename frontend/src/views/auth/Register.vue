<template>
    <div class="md-layout">
        <div class="md-layout-item">
            <signup-card>
                <h2 class="title text-center" slot="title">{{ $t('register.title') }}</h2>
                <div class="md-layout-item md-size-50 md-medium-size-50 md-small-size-100 ml-auto" slot="content-left">
                    <div class="info info-horizontal" v-for="item in contentLeft" :key="item.title">
                        <div :class="`icon ${item.colorIcon}`">
                            <md-icon>{{ item.icon }}</md-icon>
                        </div>
                        <div class="description">
                            <h4 class="info-title">{{ item.title }}</h4>
                            <p class="description">
                                {{ item.description }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="md-layout-item md-size-50 md-medium-size-50 md-small-size-100 mr-auto" slot="content-right">
                    <ValidationObserver ref="form">
                        <form @submit.prevent="registerClick" autocomplete="off">
                            <ValidationProvider name="company_name" rules="required" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ $t('user.property.company_name') }} *</label>
                                    <md-input v-model="form.company_name" type="text"></md-input>

                                    <span class="md-error" v-if="failed">{{ transformErrorMessage(errors[0], 'company_name') }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                            <ValidationProvider name="first_name" rules="required" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ $t('user.property.first_name') }} *</label>
                                    <md-input v-model="form.first_name" type="text"></md-input>

                                    <span class="md-error" v-if="failed">{{ transformErrorMessage(errors[0], 'first_name') }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                            <ValidationProvider name="last_name" rules="required" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ $t('user.property.last_name') }} *</label>
                                    <md-input v-model="form.last_name" type="text"></md-input>

                                    <span class="md-error" v-if="failed">{{ transformErrorMessage(errors[0], 'last_name') }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                            <ValidationProvider name="email" rules="required|email" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ $t('user.property.email') }} *</label>
                                    <md-input v-model="form.email" type="text"></md-input>

                                    <span class="md-error" v-if="failed">{{ transformErrorMessage(errors[0], 'email') }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                            <ValidationProvider name="password" rules="required|min:8" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ $t('user.property.password') }} *</label>
                                    <md-input v-model="form.password" type="password"></md-input>

                                    <span class="md-error" v-if="failed">{{ transformErrorMessage(errors[0], 'password') }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                            <div class="button-container">
                                <md-button @click="registerClick" class="md-success md-round mt-3" slot="footer">
                                    <md-progress-spinner v-if="loading" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner> {{ $t('register.submitBtn') }}
                                </md-button>
                            </div>
                        </form>
                    </ValidationObserver>
                </div>
            </signup-card>
        </div>
    </div>
</template>

<script>
    import { SignupCard } from "@/components";
    import { SlideYDownTransition } from "vue2-transitions";
    import axios from "axios";

    export default {
        title () {
            return this.$t('pages.register');
        },
        name: "Register",
        components: {
            SignupCard,
            SlideYDownTransition
        },
        data() {
            return {
                contentLeft: [
                    {
                        colorIcon: "icon-success",
                        icon: "timeline",
                        title: "Marketing",
                        description:
                            "We've created the marketing campaign of the website. It was a very interesting collaboration."
                    },

                    {
                        colorIcon: "icon-danger",
                        icon: "code",
                        title: "Fully Coded in HTML5",
                        description:
                            "We've developed the website with HTML5 and CSS3. The client has access to the code using GitHub."
                    },

                    {
                        colorIcon: "icon-info",
                        icon: "group",
                        title: "Built Audience",
                        description:
                            "There is also a Fully Customizable CMS Admin Dashboard for this product."
                    }
                ],
                form: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    company_name: ''
                },
                loading: false
            }
        },
        methods: {
            register() {
                let self = this;
                this.loading = true;
                axios.post(process.env.VUE_APP_LARAVEL_ENDPOINT + '/api/register', self.form)
                    .then(response => {
                        let payload = {
                            expiresIn: response.data.expiresIn
                        };

                        this.$store.dispatch('setToken', payload).then(() => {
                            let nextRoute = { name: 'dashboard' };

                            if (this.$route.query.redirect) {
                                nextRoute = this.$route.query.redirect;
                            }
                            this.$router.push(nextRoute);
                        });
                    })
                    .catch(error => {
                        this.$refs.form.setErrors(error.response.data.errors);
                        this.loading = false;
                    });
            },
            registerClick() {
                this.$refs.form.validate().then(valid => {
                    if (valid) {
                        this.register();
                    }
                });
            },
            transformErrorMessage(message, key) {
                return message.replace(key, this.$t('user.property.' + key));
            }
        }
    }
</script>

<style scoped>
    .md-progress-spinner.md-theme-default .md-progress-spinner-circle  {
        stroke: white!important;
    }
</style>
