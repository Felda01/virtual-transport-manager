import i18n, {enRoute} from "../lang";
import { getAlias } from "./index";

const authRoutes = [
    {
        path: enRoute.login,
        alias: getAlias('login'),
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'login',
                component: () => import('../views/auth/Login.vue'),
                meta: {
                    guest: true,
                    title: i18n.t('pages.login'),
                    path: {
                        en: enRoute.login
                    }
                }
            },
        ],
    },
    {
        path: enRoute.register,
        alias: getAlias('register'),
        component: () => import('../layouts/AuthLayout.vue'),
        children: [
            {
                path: '',
                name: 'register',
                component: () => import('../views/auth/Register.vue'),
                meta: {
                    guest: true,
                    title: i18n.t('pages.register'),
                    path: {
                        en: enRoute.register
                    }
                }
            },
        ],
    },
];

export default authRoutes
