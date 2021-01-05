import i18n, {enRoute} from "../lang";
import { getAlias } from "./index";

const userRoutes = [
    {
        path: '',
        component: () => import('../layouts/DefaultLayout.vue'),
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('../views/user/Dashboard.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.dashboard'),
                    path: {
                        en: enRoute.dashboard
                    }
                }
            },
            {
                path: enRoute.users,
                alias: getAlias('users'),
                name: 'users',
                component: () => import('../views/user/Users.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.users'),
                    path: {
                        en: enRoute.users
                    }
                }
            },
            {
                path: `${enRoute.users}/:id`,
                alias: `${getAlias('users')}/:id`,
                name: 'user',
                component: () => import('../views/user/User.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.user'),
                    path: {
                        en: `${enRoute.users}/:id`
                    }
                }
            }
        ]
    },
];

export default userRoutes;
