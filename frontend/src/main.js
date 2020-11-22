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

// HTTP connection to the API
const httpLink = createHttpLink({
  // You should use an absolute URL here
  uri: 'https://virtual-transport-manager.ddev.site/graphql',
})

// Cache implementation
const cache = new InMemoryCache()

// Create the apollo client
const apolloClient = new ApolloClient({
  link: httpLink,
  cache,
  connectToDevTools: true
})

const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
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
