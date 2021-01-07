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
            },
            {
                path: enRoute.truckShop,
                alias: getAlias('truckShop'),
                name: 'truckShop',
                component: () => import('../views/user/TruckModels.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.truckShop'),
                    path: {
                        en: enRoute.truckShop
                    }
                }
            },
            {
                path: enRoute.garageShop,
                alias: getAlias('garageShop'),
                name: 'garageShop',
                component: () => import('../views/user/GarageModels.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.garageShop'),
                    path: {
                        en: enRoute.garageShop
                    }
                }
            },
            {
                path: enRoute.trailerShop,
                alias: getAlias('trailerShop'),
                name: 'trailerShop',
                component: () => import('../views/user/TrailerModels.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.trailerShop'),
                    path: {
                        en: enRoute.trailerShop
                    }
                }
            },
        ]
    },
];

export default userRoutes;
