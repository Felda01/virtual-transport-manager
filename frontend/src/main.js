import Vue from 'vue'
import App from './App.vue'
// import './registerServiceWorker'
import router from './router'

import VueApollo from "vue-apollo";
Vue.use(VueApollo);

import VueI18n from 'vue-i18n'
Vue.use(VueI18n)

Vue.use(require('vue-moment'))

Vue.config.productionTip = false

import axios from 'axios';
import VueAxios from 'vue-axios'

Vue.use(VueAxios, axios);

import VueContentPlaceholders from 'vue-content-placeholders';

Vue.use(VueContentPlaceholders);

import VueLodash from 'vue-lodash';
import lodash from 'lodash';

// name is optional
Vue.use(VueLodash, { lodash: lodash })

import store from './store';

import { localize } from 'vee-validate';
import en from 'vee-validate/dist/locale/en.json';
import sk from 'vee-validate/dist/locale/sk.json';

import Chartist from "chartist";
Vue.prototype.$Chartist = Chartist;

// Install English and Slovak locales.
localize({
  en,
  sk
});

Vue.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Vue.axios.defaults.headers.common['Accept'] = 'application/json';
Vue.axios.defaults.withCredentials = true;

Vue.axios.interceptors.response.use(function (response) {
    return response
  }, function (error) {
    if (error.response.status === 401) {
      if (!error.config.url.includes('/api/logout')) {
        store.dispatch('logout', { fullPath: router.currentRoute.fullPath });
      }
    }
    return Promise.reject(error)
  });

import { ApolloClient } from 'apollo-client'
import { createHttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory'
import { BatchHttpLink } from 'apollo-link-batch-http'
import { onError } from 'apollo-link-error';
import { from } from 'apollo-link';

const batchLink = new BatchHttpLink({
  uri: process.env.VUE_APP_GRAPHQL_ENDPOINT,
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  credentials: 'include'
});

// HTTP connection to the API
const httpLinkDefault = createHttpLink({
  // You should use an absolute URL here
  uri: process.env.VUE_APP_GRAPHQL_ENDPOINT,
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  credentials: 'include'
})


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
const apolloClientDefault =  new ApolloClient({
  link: from([
    onError(errorHandler => {
      if (errorHandler && errorHandler.networkError && errorHandler.networkError.statusCode === 401) {
        store.dispatch('logout', { fullPath: router.currentRoute.fullPath });
      }
    }),
    // httpLinkDefault,
    batchLink,
  ]),
  cache,
  defaultOptions: defaultOptions,
});

export const apolloClient = apolloClientDefault;

const apolloProvider = new VueApollo({
  defaultClient: apolloClientDefault,
})

import DashboardPlugin from "./material-dashboard";
Vue.use(DashboardPlugin);

import titleMixin from './mixins/TitleMixin'
Vue.mixin(titleMixin)

import processSearchMixin from './mixins/ProcessSearchMixin';
Vue.mixin(processSearchMixin);

import errorHandlingMixin from './mixins/ErrorHandlingMixin';
Vue.mixin(errorHandlingMixin);

import i18n from './lang/index.js';

import Vue2Filters from 'vue2-filters';
Vue.use(Vue2Filters);

// import Echo from 'laravel-echo';
// window.io = require('socket.io-client');
//
// if (typeof io !== 'undefined') {
//   const echo_instance = new Echo({
//     broadcaster: 'socket.io',
//     host: process.env.VUE_APP_LARAVEL_ENDPOINT + ':6001',
//     transports: ['websocket', 'polling', 'flashsocket'],
//   });
//
//   window.Echo = echo_instance;
//   Vue.prototype.$echo = echo_instance;
// }


new Vue({
  router,
  i18n,
  store,
  apolloProvider,
  mixins: [Vue2Filters.mixin],
  render: h => h(App)
}).$mount('#app')
