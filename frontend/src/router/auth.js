import i18n, { enRoute, skRoute } from "../lang";
import { getAlias } from "./index";

const authRoutes = [
    {
        path: enRoute.login,
        alias: [skRoute.login],
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'login',
                component: () => import('../views/auth/Login.vue'),
                meta: {
                    guest: true,
                    title: 'pages.login',
                    image: 'login',
                    path: {
                        en: enRoute.login,
                        sk: skRoute.login
                    }
                }
            },
        ],
    },
    {
        path: enRoute.register,
        alias: [skRoute.register],
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'register',
                component: () => import('../views/auth/Register.vue'),
                meta: {
                    guest: true,
                    title: 'pages.register',
                    image: 'register',
                    path: {
                        en: enRoute.register,
                        sk: skRoute.register
                    }
                }
            },
        ],
    },
    {
        path: enRoute.forgotPassword,
        alias: skRoute.forgotPassword,
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'forgotPassword',
                component: () => import('../views/auth/ForgotPassword.vue'),
                meta: {
                    guest: true,
                    title: 'pages.forgotPassword',
                    image: 'forgotPassword',
                    path: {
                        en: enRoute.forgotPassword,
                        sk: skRoute.forgotPassword
                    }
                }
            },
        ],
    },
    {
        path: `${enRoute.resetPassword}/:token/:email`,
        alias: [`${skRoute.resetPassword}/:token/:email`],
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'resetPassword',
                component: () => import('../views/auth/ResetPassword.vue'),
                meta: {
                    guest: true,
                    title: 'pages.resetPassword',
                    image: 'resetPassword',
                    path: {
                        en: `${enRoute.resetPassword}/:token/:email`,
                        sk: `${skRoute.resetPassword}/:token/:email`
                    }
                }
            },
        ],
    },
];

export default authRoutes
