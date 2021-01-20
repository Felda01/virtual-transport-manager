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
                ...(getAlias('users')) && { alias: getAlias('users') },
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
                ...(getAlias('users')) && { alias: `${getAlias('users')}/:id` },
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
                ...(getAlias('truckShop')) && { alias: getAlias('truckShop') },
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
                ...(getAlias('garageShop')) && { alias: getAlias('garageShop') },
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
                ...(getAlias('trailerShop')) && { alias: getAlias('trailerShop') },
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
                ...(getAlias('garages')) && { alias: getAlias('garages') },
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
                ...(getAlias('garages')) && { alias: `${getAlias('garages')}/:id` },
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
                ...(getAlias('trucks')) && { alias: getAlias('trucks') },
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
                ...(getAlias('trucks')) && { alias: `${getAlias('trucks')}/:id` },
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
                ...(getAlias('trailers')) && { alias: getAlias('trailers') },
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
                ...(getAlias('trailers')) && { alias: `${getAlias('trailers')}/:id` },
                name: 'trailer',
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
            {
                path: enRoute.recruitmentAgencyDrivers,
                ...(getAlias('recruitmentAgencyDrivers')) && { alias: getAlias('recruitmentAgencyDrivers') },
                name: 'recruitmentAgencyDrivers',
                component: () => import('../views/user/RecruitmentAgencyDrivers.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.recruitmentAgencyDrivers'),
                    path: {
                        en: enRoute.recruitmentAgencyDrivers
                    }
                }
            },
            {
                path: enRoute.drivers,
                ...(getAlias('drivers')) && { alias: getAlias('drivers') },
                name: 'drivers',
                component: () => import('../views/user/Drivers.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.drivers'),
                    path: {
                        en: enRoute.drivers
                    }
                }
            },
            {
                path: `${enRoute.drivers}/:id`,
                ...(getAlias('drivers')) && { alias: `${getAlias('drivers')}/:id` },
                name: 'driver',
                component: () => import('../views/user/Driver.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: i18n.t('pages.drivers'),
                    path: {
                        en: `${enRoute.drivers}/:id`
                    }
                }
            },
        ]
    },
];

export default userRoutes;
