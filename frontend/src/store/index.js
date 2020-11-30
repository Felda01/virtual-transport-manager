import Vue from 'vue';
import Vuex from 'vuex';
import Cookies from 'js-cookie';
import router from '../router'
import i18n from "../lang";


Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        loading: false,
        user: null
    },
    getters: {
        loading: state => state.loading,
        user: state => state.user,
    },
    mutations: {
        SET_LOADING(state, loading) {
            state.loading = loading;
        },
        SET_USER(state, user) {
            state.user = user
        }
    },
    actions: {
        setToken({commit}, {expiresIn}) {
            const expiryTime = new Date(new Date().getTime() + expiresIn * 1000);
            Cookies.set('x-access-token', true, {expires: expiryTime, sameSite: 'lax'});
        },

         refreshToken({dispatch}, {fullPath}) {
            return Vue.axios.post('https://virtual-transport-manager.ddev.site/api/refresh-token')
                .then(response => {
                    let payload = {
                        expiresIn: response.data.expiresIn
                    };
                    dispatch('setToken', payload);
                }).catch(error => {
                    dispatch('logout');
                    if (fullPath) {
                        router.push({
                            name: 'login',
                            query: { redirect: fullPath },
                            params: { locale: i18n.locale }
                        });
                    } else {
                        router.push({
                            name: 'login',
                            params: { locale: i18n.locale }
                        });
                    }
                });
        },

        setUser({commit}, {user}) {
            commit('SET_USER', user);
        },

        getUser({commit}) {
            return Vue.axios.get('https://virtual-transport-manager.ddev.site/api/user').then(response => {
                commit('SET_USER', response.data.user);
            });
        },

        logout({commit}) {
            Cookies.remove('x-access-token');
            commit('SET_USER', null);
        },

        setLoading({commit}, {loading}) {
            commit('SET_LOADING', loading);
        },
    }
})
