import Vue from 'vue';
import Vuex from 'vuex';
import Cookies from 'js-cookie';
import router from '../router'


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
                            query: { redirect: fullPath }
                        });
                    } else {
                        router.push({
                            name: 'login'
                        });
                    }
                });
        },

        setUser({commit}, {user}) {
            commit('SET_USER', user);
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
