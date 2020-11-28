import Vue from 'vue';
import Vuex from 'vuex';
import Cookies from 'js-cookie';


Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        token: null,
        loading: false,
        // user: null,
    },
    getters: {
        loggedIn: state => state.token !== null,
        loading: state => state.loading,
        // userId: state => state.user ? state.user.id : null,
        // user: state => state.user,
        token: state => state.token ? state.token : null,
    },
    mutations: {
        SET_TOKEN(state, token) {
            state.token = token;
        },

        REMOVE_TOKEN(state) {
            state.token = null;
        },

        SET_LOADING(state, loading) {
            state.loading = loading;
        },

        // SET_USER(state, user) {
        //     state.user = user;
        // },
    },
    actions: {
        setToken({commit}, {token, expiresIn}) {
            const expiryTime = new Date(new Date().getTime() + expiresIn * 10000); //TODO set correct time
            Cookies.set('x-access-token', true, {expires: expiryTime, sameSite: 'lax'});
            commit('SET_TOKEN', token);
        },

         refreshToken({dispatch}) {
            return Vue.axios.post('/api/refresh-token').then(response => {
                let payload = {
                    token: response.data.token,
                    expiresIn: response.data.expiresIn
                };
                dispatch('setToken', payload);
            });

        },

        logout({commit}) {
            Cookies.remove('x-access-token');
            commit('REMOVE_TOKEN');
            commit('SET_USER', null);
        },

        setLoading({commit}, {loading}) {
            commit('SET_LOADING', loading);
        },

        // getUser({commit}) {
        //     Vue.axios.get('/api/user').then(response => {
        //         commit('SET_USER', response.data.data);
        //     });
        // },
    }
})
