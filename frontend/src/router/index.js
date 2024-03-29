import Vue from 'vue'
import VueRouter from 'vue-router'
import Cookies from 'js-cookie'
import { localize } from 'vee-validate';



Vue.use(VueRouter)

import i18n from '../lang';
import store from '../store';

import authRoutes from "./auth";
import adminRoutes from "./admin";
import userRoutes from "./user";
import EventBus from "../event-bus";

const currentLocale = location.pathname.split("/")[1] || "en";
i18n.locale = currentLocale;

function getTranslatedAlias(name) {
  let translation = '';
  if (i18n.locale !== 'en') {
    translation = i18n.t(`routes.${name}`);
  }
  return translation;
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
        ...userRoutes,
      {
        path: '*',
        component: () => import('../layouts/ErrorLayout.vue'),
        children: [
          {
            path: '',
            component: () => import('../views/errors/PageNotFound.vue'),
            meta: {
              image: 'notFound',
            }
          }
        ],
      }
    ]
  },
  {
    path: '*',
    component: () => import('../layouts/ErrorLayout.vue'),
    children: [
      {
        path: '',
        component: () => import('../views/errors/PageNotFound.vue'),
        meta: {
          image: 'notFound',
        }
      }
    ],
  }
];

const router = new VueRouter({
  mode: 'history',
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

function user(fullPath) {
  return new Promise((resolve, reject) => {
    if (!store.state.user) {
      store.dispatch('getUser', {fullPath: fullPath}).then(response => {
        resolve();
      });
    } else {
      resolve();
    }
  });
}

router.beforeEach((to, from, next) => {
  if (to.fullPath.slice(-1) === '/') {
    next(to.fullPath.slice(0, -1));
  } else {
    let currentLocale = to.params.locale;

    let availableLocales = Object.keys(i18n.messages);
    if (!availableLocales.includes(currentLocale)) {
      next({ name: 'dashboard', params: {locale: i18n.fallbackLocale || 'en'}, replace: true});
    }
    if (!currentLocale) {
      currentLocale = i18n.fallbackLocale || 'en';
    }

    // setting the new locale
    i18n.locale = currentLocale;
    localize(currentLocale);
    store.dispatch('setLanguageCookie', { locale: currentLocale });

    if (!from.params.locale) {
      from.params.locale = currentLocale;
    }

    const redirectRouteByRole = {
      admin: { name: 'adminDashboard', params: { locale: i18n.locale } },
      user: { name: 'dashboard', params: { locale: i18n.locale } },
    }

    if (to.matched.some(record => record.meta.requiresAuth)) {
      if (!Cookies.get('x-access-token')) {
        store.dispatch('logout', { fullPath: to.fullPath });
      } else {
        user(to.fullPath).then(() => {
          if (to.matched.some(record => record.meta.adminOnly)) {
            if (store.state.user && store.state.user.roles.some(record => record.name === 'admin')) {
              next();
            } else {
              if (to.name !== redirectRouteByRole['user']['name']) {
                next(redirectRouteByRole['user']);
              }
            }
          } else if (to.matched.some(record => record.meta.userOnly)) {
            if (store.state.user && store.state.user.roles.some(record => record.name === 'admin')) {
              if (to.name !== redirectRouteByRole['admin']['name']) {
                next(redirectRouteByRole['admin']);
              }
            } else {
              next();
            }
          } else {
            next();
          }
        });
      }
    } else if (to.matched.some(record => record.meta.guest)) {
      if (!Cookies.get('x-access-token')) {
        next()
      }
      else {
        if (store.state.user && store.state.user.roles.some(record => record.name === 'admin')) {
          if (to.name !== redirectRouteByRole['admin']['name']) {
            next(redirectRouteByRole['admin']);
          }
        } else if (store.state.user){
          if (to.name !== redirectRouteByRole['user']['name']) {
            next(redirectRouteByRole['user']);
          }
        } else {
          next();
        }
      }
    } else {
      next()
    }

    if (to.params.locale !== from.params.locale) {
      EventBus.$emit('localeChanged');
    }
  }
});

// PATH LOCALIZATION PROCCESS
router.afterEach(to => {
  const isLocalizable = router.currentRoute.meta.path;

  if (isLocalizable) {
    const localizedRoute = {};
    // constructing a new localized path using the previously created route meta data
    let routeSegments = router.currentRoute.meta.path[i18n.locale];

    for (let key in to.params) {
      if (to.params.hasOwnProperty(key)) {
        if (key !== "locale") {
          routeSegments = routeSegments.replace(`:${key}`, to.params[key]);
        }
      }
    }

    localizedRoute.path = `/${i18n.locale}/${routeSegments}`;
    // appending query (if any)
    localizedRoute.query = { ...router.currentRoute.query };
    localizedRoute.params = to.params;

    const hasUnresolvedPath = Object.values(to.params).some(el =>
        el.includes(":")
    );
    if (router.currentRoute.path !== localizedRoute.path && !hasUnresolvedPath) {
      router.replace(localizedRoute).catch(() => {});
    }
  }

  if (!document.body.classList.contains("loaded")) {
    document.body.classList.add("loaded");
  }
});

export default router

export function getAlias(name) {
  return getTranslatedAlias(name);
}
