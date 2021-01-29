import Vue from 'vue';
import Vuex from 'vuex';
import Cookies from 'js-cookie';
import router from '../router'
import i18n from "../lang";
import { apolloClient } from "../main";
import { COMPANY_QUERY, ME_QUERY } from '../graphql/queries/common';


Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        loading: false,
        user: null,
        company: null
    },
    getters: {
        user: state => state.user,
        money: state => state.company && state.company ? parseFloat(state.company.money) : null,
        hasPermission: (state) => (name) => {
            return state.user ? state.user.permissions.some(permission => permission.name === name) : false
        }
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user
        },
        SET_COMPANY(state, company) {
            state.company = company;
        }
    },
    actions: {
        setToken({commit}, {expiresIn}) {
            let expiryTime = new Date();
            expiryTime.setSeconds(expiryTime.getSeconds() + expiresIn - 3600);
            Cookies.set('x-access-token', true, {expires: expiryTime, sameSite: 'lax', secure: true});
        },

        //  refreshToken({dispatch}, {fullPath}) {
        //     return Vue.axios.post('https://virtual-transport-manager.ddev.site/api/refresh-token')
        //         .then(response => {
        //             let payload = {
        //                 expiresIn: response.data.expiresIn
        //             };
        //             dispatch('setToken', payload);
        //         }).catch(error => {
        //             dispatch('logout');
        //             if (fullPath) {
        //                 router.push({
        //                     name: 'login',
        //                     query: { redirect: fullPath },
        //                     params: { locale: i18n.locale }
        //                 });
        //             } else {
        //                 router.push({
        //                     name: 'login',
        //                     params: { locale: i18n.locale }
        //                 });
        //             }
        //         });
        // },

        setUser({commit}, {user}) {
            commit('SET_USER', user);
        },

        getUser({dispatch}, {fullPath}) {
            return apolloClient.query({
                query: ME_QUERY,
            }).then(response => {
                dispatch('setUser', {user: response.data.me});
                dispatch('getCompany');
            }).catch(error => {
                dispatch('logout', {fullPath: fullPath});
            });
        },

        getCompany({commit}) {
            return apolloClient.query({
                query: COMPANY_QUERY,
            }).then(response => {
                commit('SET_COMPANY', response.data.company);
            });
        },

        logout({commit}, {fullPath}) {
            return Vue.axios.post(process.env.VUE_APP_LARAVEL_ENDPOINT + '/api/logout')
                .then(response => {
                    Cookies.remove('x-access-token');
                    commit('SET_USER', null);

                    if (fullPath) {
                        if (router.currentRoute.name !== 'login') {
                            router.push({
                                name: 'login',
                                query: {redirect: fullPath},
                                params: {locale: i18n.locale}
                            });
                        }
                    } else {
                        if (router.currentRoute.name !== 'login') {
                            router.push({
                                name: 'login',
                                params: {locale: i18n.locale}
                            });
                        }
                    }
                })
                .catch(error => {
                    Cookies.remove('x-access-token');
                    commit('SET_USER', null);

                    if (fullPath) {
                        if (router.currentRoute.name !== 'login') {
                            router.push({
                                name: 'login',
                                query: { redirect: fullPath },
                                params: { locale: i18n.locale }
                            });
                        }
                    } else {
                        if (router.currentRoute.name !== 'login') {
                            router.push({
                                name: 'login',
                                params: {locale: i18n.locale}
                            });
                        }
                    }
                });

        },

        setLanguageCookie({commit}, {locale}) {
            let current = Cookies.get('locale');
            if (locale !== current) {
                const expiryTime = new Date(new Date().getTime() + 5 * 24 * 60 * 60 * 1000);
                Cookies.set('locale', locale, {expires: expiryTime, sameSite: 'lax', secure: true});
            }
        }
    }
})
