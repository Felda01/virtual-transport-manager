import i18n, {enRoute} from "../lang";
import { getAlias } from "./index";

const authRoutes = [
    {
        path: enRoute.login,
        ...(getAlias('login')) && { alias: getAlias('login') },
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'login',
                component: () => import('../views/auth/Login.vue'),
                meta: {
                    guest: true,
                    title: i18n.t('pages.login'),
                    image: 'login',
                    path: {
                        en: enRoute.login
                    }
                }
            },
        ],
    },
    {
        path: enRoute.register,
        ...(getAlias('register')) && { alias: getAlias('register') },
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'register',
                component: () => import('../views/auth/Register.vue'),
                meta: {
                    guest: true,
                    title: i18n.t('pages.register'),
                    image: 'register',
                    path: {
                        en: enRoute.register
                    }
                }
            },
        ],
    },
    {
        path: enRoute.forgotPassword,
        ...(getAlias('forgotPassword')) && { alias: getAlias('forgotPassword') },
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'forgotPassword',
                component: () => import('../views/auth/ForgotPassword.vue'),
                meta: {
                    guest: true,
                    title: i18n.t('pages.forgotPassword'),
                    image: 'forgotPassword',
                    path: {
                        en: enRoute.forgotPassword
                    }
                }
            },
        ],
    },
    {
        path: `${enRoute.resetPassword}/:token/:email`,
        ...(getAlias('forgotPassword')) && { alias: `${getAlias('resetPassword')}/:token/:email` },
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'resetPassword',
                component: () => import('../views/auth/ResetPassword.vue'),
                meta: {
                    guest: true,
                    title: i18n.t('pages.resetPassword'),
                    image: 'resetPassword',
                    path: {
                        en: `${enRoute.resetPassword}/:token/:email`
                    }
                }
            },
        ],
    },
];

export default authRoutes
