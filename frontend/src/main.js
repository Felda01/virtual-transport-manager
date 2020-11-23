import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'

import VueApollo from "vue-apollo";
Vue.use(VueApollo);

import VueI18n from 'vue-i18n'
Vue.use(VueI18n)

Vue.config.productionTip = false

import axios from 'axios';
import VueAxios from 'vue-axios'

Vue.use(VueAxios, axios);

Vue.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Vue.axios.defaults.headers.common['Accept'] = 'application/json';
Vue.axios.defaults.withCredentials = true;

import { ApolloClient } from 'apollo-client'
import { createHttpLink } from 'apollo-link-http'
import { InMemoryCache } from 'apollo-cache-inmemory'

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

  query: {fetchPolicy: 'network-only'}
}

// HTTP connection to the API
const httpLinkDefault = createHttpLink({
  // You should use an absolute URL here
  uri: 'https://virtual-transport-manager.ddev.site/graphql',
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// HTTP connection to the API
const httpLinkAuth = createHttpLink({
  // You should use an absolute URL here
  uri: 'https://virtual-transport-manager.ddev.site/graphql/auth',
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})


// Cache implementation
const cache = new InMemoryCache()

// Create the apollo client default
const apolloClientDefault = new ApolloClient({
  link: httpLinkDefault,
  cache,
  defaultOptions: defaultOptions,
  connectToDevTools: true,
})

// Create the apollo client auth
const apolloClientAuth = new ApolloClient({
  link: httpLinkAuth,
  cache,
  defaultOptions: defaultOptions,
  connectToDevTools: true,
})

const apolloProvider = new VueApollo({
  clients: {
    apolloClientAuth,
    apolloClientDefault
  },
  defaultClient: apolloClientAuth,
})

import DashboardPlugin from "./material-dashboard";
Vue.use(DashboardPlugin);

import titleMixin from './mixins/titleMixin'
Vue.mixin(titleMixin)

import i18n from './lang/index.js';

new Vue({
  router,
  i18n,
  apolloProvider,
  render: h => h(App)
}).$mount('#app')
