import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import i18n from '../lang';

const routes = [
  {
    path: '/login',
    component: () => import('../layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: i18n.t('pages.login'),
        component: () => import('../views/auth/Login.vue'),
        meta: {
          guest: true
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
        name: i18n.t('pages.register'),
        component: () => import('../views/auth/Login.vue'),
        meta: {
          guest: true
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
        name: i18n.t('pages.dashboard'),
        component: () => import('../views/Dashboard.vue'),
        meta: {
          requiresAuth: true
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
        name: i18n.t('pages.adminDashboard'),
        component: () => import('../views/admin/Dashboard.vue'),
        meta: {
          requiresAuth: true
        }
      },
      {
        path: 'countries',
        name: i18n.t('pages.countries'),
        component: () => import('../views/admin/Countries.vue'),
        meta: {
          requiresAuth: true
        }
      }
    ]
  },
  {
    path: '*',
    component: () => import('../views/errors/404.vue'),
  }
];

// router.beforeEach((to, from, next) => {
//   if(to.matched.some(record => record.meta.requiresAuth)) {
//     if (localStorage.getItem('jwt') == null) {
//       next({
//         path: '/login',
//         params: { nextUrl: to.fullPath }
//       })
//     } else {
//       let user = JSON.parse(localStorage.getItem('user'))
//       if(to.matched.some(record => record.meta.is_admin)) {
//         if(user.is_admin == 1){
//           next()
//         }
//         else{
//           next({ name: 'userboard'})
//         }
//       }else {
//         next()
//       }
//     }
//   } else if(to.matched.some(record => record.meta.guest)) {
//     if(localStorage.getItem('jwt') == null){
//       next()
//     }
//     else{
//       next({ name: 'userboard'})
//     }
//   }else {
//     next()
//   }
// });

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

export default router
