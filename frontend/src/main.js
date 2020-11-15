import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'

Vue.config.productionTip = false

import ApolloClient from 'apollo-boost'

const apolloClient = new ApolloClient({
  // You should use an absolute URL here
  uri: 'https://api.graphcms.com/simple/v1/awesomeTalksClone'
})

import VueApollo from 'vue-apollo'

Vue.use(VueApollo)

const apolloProvider = new VueApollo({
  defaultClient: apolloClient,
})



new Vue({
  router,
  apolloProvider,
  render: h => h(App)
}).$mount('#app')
