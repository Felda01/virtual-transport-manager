import Vue from 'vue'
import VueRouter from 'vue-router'
import Cookies from 'js-cookie'

Vue.use(VueRouter)

import i18n from '../lang';
import store from '../store';

const routes = [
  {
    path: '/login',
    component: () => import('../layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'login',
        component: () => import('../views/auth/Login.vue'),
        meta: {
          guest: true,
          title: i18n.t('pages.login'),
        }
      },
    ],
  },
  {
    path: '/register',
    component: () => import('../layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'register',
        component: () => import('../views/auth/Login.vue'),
        meta: {
          guest: true,
          title: i18n.t('pages.register'),
        }
      },
    ],
  },
  {
    path: '/',
    component: () => import('../layouts/DefaultLayout.vue'),
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('../views/Dashboard.vue'),
        meta: {
          requiresAuth: true,
          title: i18n.t('pages.dashboard'),
        }
      }
    ]
  },
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    children: [
      {
        path: '',
        name: 'adminDashboard',
        component: () => import('../views/admin/Dashboard.vue'),
        meta: {
          requiresAuth: true,
          title: i18n.t('pages.adminDashboard'),
        }
      },
      {
        path: 'countries',
        name: 'countries',
        component: () => import('../views/admin/Countries.vue'),
        meta: {
          requiresAuth: true,
          title: i18n.t('pages.countries'),
        }
      }
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
})

router.beforeEach((to, from, next) => {
  if(to.matched.some(record => record.meta.requiresAuth)) {
    if (!Cookies.get('x-access-token')) {
      store.dispatch('refreshToken', { fullPath: to.fullPath });
    } else {
      // let user = JSON.parse(localStorage.getItem('user'))
      // if(to.matched.some(record => record.meta.is_admin)) {
      //   if(user.is_admin == 1){
      //     next()
      //   }
      //   else{
      //     next({ name: 'userboard'})
      //   }
      // }else {
      //   next()
      // }
      next()
    }
  } else if(to.matched.some(record => record.meta.guest)) {
    if(!Cookies.get('x-access-token')){
      next()
    }
    else{
      next({ name: 'dashboard' })
    }
  }else {
    next()
  }
});

export default router
