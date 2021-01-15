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
            {
                path: enRoute.garages,
                alias: getAlias('garages'),
                name: 'garages',
                component: () => import('../views/user/Garages.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.garages'),
                    path: {
                        en: enRoute.garages
                    }
                }
            },
            {
                path: `${enRoute.garages}/:id`,
                alias: `${getAlias('garages')}/:id`,
                name: 'garage',
                component: () => import('../views/user/Garage.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.garage'),
                    path: {
                        en: `${enRoute.garages}/:id`
                    }
                }
            },
            {
                path: enRoute.trucks,
                alias: getAlias('trucks'),
                name: 'trucks',
                component: () => import('../views/user/Trucks.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.trucks'),
                    path: {
                        en: enRoute.trucks
                    }
                }
            },
            {
                path: `${enRoute.trucks}/:id`,
                alias: `${getAlias('trucks')}/:id`,
                name: 'truck',
                component: () => import('../views/user/Truck.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.truck'),
                    path: {
                        en: `${enRoute.trucks}/:id`
                    }
                }
            },
            {
                path: enRoute.trailers,
                alias: getAlias('trailers'),
                name: 'trailers',
                component: () => import('../views/user/Trailers.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.trailers'),
                    path: {
                        en: enRoute.trailers
                    }
                }
            },
            {
                path: `${enRoute.trailers}/:id`,
                alias: `${getAlias('trailers')}/:id`,
                name: 'trailers',
                component: () => import('../views/user/Trailer.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.trailers'),
                    path: {
                        en: `${enRoute.trailers}/:id`
                    }
                }
            },
        ]
    },
];

export default userRoutes;
