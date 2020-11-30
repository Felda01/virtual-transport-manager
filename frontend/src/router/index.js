import Vue from 'vue'
import VueRouter from 'vue-router'
import Cookies from 'js-cookie'

Vue.use(VueRouter)

import i18n from '../lang';
import store from '../store';

import authRoutes from "./auth";
import adminRoutes from "./admin";

const currentLocale = location.pathname.split("/")[1] || "en";
i18n.locale = currentLocale;

function getTranslatedAlias(name) {
  const translation = i18n.t(`routes.${name}`);
  return translation !== name ? translation : undefined;
}

const routes = [
  {
    path: '/',
    redirect: i18n.locale
  },
  {
    path: '/:locale',
    component: () => import('../layouts/Layout.vue'),
    children: [
        ...authRoutes,
        ...adminRoutes,
      {
        path: '',
        component: () => import('../layouts/DefaultLayout.vue'),
        children: [
          {
            path: '',
            name: 'dashboard',
            component: () => import('../views/Dashboard.vue'),
            meta: {
              requiresAuth: true,
              userOnly: true,
              title: i18n.t('pages.dashboard'),
            }
          }
        ]
      },

    ]
  },
  {
    path: '*',
    component: () => import('../views/errors/404.vue'),
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior: to => {
    if (to.hash) {
      return { selector: to.hash };
    } else {
      return { x: 0, y: 0 };
    }
  },
  linkExactActiveClass: "nav-item active",
  routes
});

function user() {
  return new Promise((resolve, reject) => {
    if (!store.state.user) {
      store.dispatch('getUser').then(response => {
        resolve();
      });
    } else {
      resolve();
    }
  });
}

router.beforeEach((to, from, next) => {
  let currentLocale = to.params.locale;
  // setting the new locale
  i18n.locale = currentLocale;

  if(to.matched.some(record => record.meta.requiresAuth)) {
    if (!Cookies.get('x-access-token')) {
      store.dispatch('refreshToken', { fullPath: to.fullPath });
    } else {
      user().then(() => {
        if (to.matched.some(record => record.meta.adminOnly)) {
          if (store.state.user && store.state.user.roles.some(record => record.name === 'admin')) {
            next();
          } else {
            next({ name: 'dashboard', params: { locale: i18n.locale } })
          }
        }
        if (to.matched.some(record => record.meta.userOnly)) {
          if (store.state.user && store.state.user.roles.some(record => record.name === 'admin')) {
            next({ name: 'adminDashboard', params: { locale: i18n.locale } })
          } else {
            next();
          }
        }
        next()
      })
    }
  } else if(to.matched.some(record => record.meta.guest)) {
    if(!Cookies.get('x-access-token')){
      next()
    }
    else{
      next({ name: 'dashboard', params: { locale: i18n.locale } })
    }
  }else {
    next()
  }
});

export default router

export function getAlias(name) {
  return getTranslatedAlias(name);
}
