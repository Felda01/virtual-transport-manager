import Vue from 'vue'
import App from './App.vue'
// import './registerServiceWorker'
import router from './router'

import VueApollo from "vue-apollo";
Vue.use(VueApollo);

import VueI18n from 'vue-i18n'
Vue.use(VueI18n)

Vue.config.productionTip = false

import axios from 'axios';
import VueAxios from 'vue-axios'

Vue.use(VueAxios, axios);

import VueContentPlaceholders from 'vue-content-placeholders';

Vue.use(VueContentPlaceholders);

import store from './store';

import { localize } from 'vee-validate';
import en from 'vee-validate/dist/locale/en.json';
import sk from 'vee-validate/dist/locale/sk.json';

// Install English and Slovak locales.
localize({
  en,
  sk
});

Vue.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Vue.axios.defaults.headers.common['Accept'] = 'application/json';
Vue.axios.defaults.withCredentials = true;

import { ApolloClient } from 'apollo-client'
import { createHttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory'
import { onError } from 'apollo-link-error';
import { from } from 'apollo-link';

import { setContext } from "apollo-link-context";

// HTTP connection to the API
const httpLinkDefault = createHttpLink({
  // You should use an absolute URL here
  uri: 'https://virtual-transport-manager.ddev.site/api/graphql',
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  credentials: 'include'
})

// HTTP connection to the API
// const httpLinkAuth = createHttpLink({
//   // You should use an absolute URL here
//   uri: 'https://virtual-transport-manager.ddev.site/api/graphql/auth',
//   headers: {
//     'Accept': 'application/json',
//     'X-Requested-With': 'XMLHttpRequest'
//   },
//   credentials: 'include'
// })

// Cache implementation
const cache = new InMemoryCache();

const defaultOptions = {
  // You can use `wss` for secure connection (recommended in production)
  // Use `null` to disable subscriptions
  wsEndpoint: null,
  // Enable Automatic Query persisting with Apollo Engine
  persisting: false,
  // Use websockets for everything (no HTTP)
  // You need to pass a `wsEndpoint` for this to work
  websocketsOnly: false,
  // Is being rendered on the server?
  ssr: false,

  watchQuery: {fetchPolicy: 'network-only'},

  query: {fetchPolicy: 'network-only'},
}

// Create the apollo client default
const apolloClientDefault = new ApolloClient({
  link: from([
    onError(errorHandler => {
      if (errorHandler && errorHandler.networkError && errorHandler.networkError.statusCode === 401) {
        store.dispatch('refreshToken', { fullPath: router.currentRoute.fullPath });
      }
     }),
    httpLinkDefault,
  ]),
  cache,
  defaultOptions: defaultOptions,
});

Vue.axios.interceptors.response.use(function (response) {
    return response
  }, function (error) {
    if (error.response.status === 401) {
      store.dispatch('refreshToken', { fullPath: router.currentRoute.fullPath });
    }
    return Promise.reject(error)
  });

// Create the apollo client auth
// const apolloClientAuth = new ApolloClient({
//   link: httpLinkAuth,
//   cache,
//   defaultOptions: defaultOptions,
//   connectToDevTools: true
// })

const apolloProvider = new VueApollo({
  // clients: {
  //   apolloClientAuth,
  //   apolloClientDefault
  // },
  // defaultClient: apolloClientAuth,
  defaultClient: apolloClientDefault,
})

import DashboardPlugin from "./material-dashboard";
Vue.use(DashboardPlugin);

import titleMixin from './mixins/titleMixin'
Vue.mixin(titleMixin)

import i18n from './lang/index.js';

new Vue({
  router,
  i18n,
  store,
  apolloProvider,
  render: h => h(App)
}).$mount('#app')
