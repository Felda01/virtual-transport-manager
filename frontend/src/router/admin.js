import i18n, {enRoute} from "../lang";
import { getAlias } from "./index";

const adminRoutes = [
    {
        path: enRoute.adminDashboard,
        alias: getAlias('login'),
        component: () => import('../layouts/AdminLayout.vue'),
        children: [
            {
                path: '',
                name: 'adminDashboard',
                component: () => import('../views/admin/Dashboard.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.adminDashboard'),
                    path: {
                        en: enRoute.adminDashboard + '/'
                    }
                }
            },
            {
                path: enRoute.countries,
                name: 'countries',
                component: () => import('../views/admin/Countries.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.countries'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.countries + '/'
                    }
                }
            },
            {
                path: enRoute.locations,
                name: 'locations',
                component: () => import('../views/admin/Locations.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.locations'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.locations + '/'
                    }
                }
            },
            {
                path: enRoute.routes,
                name: 'routes',
                component: () => import('../views/admin/Routes.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.routes'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.routes + '/'
                    }
                }
            },
            {
                path: enRoute.bankLoanTypes,
                name: 'bankLoanTypes',
                component: () => import('../views/admin/BankLoanTypes.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.bankLoanTypes'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.bankLoanTypes + '/'
                    }
                }
            },
            {
                path: enRoute.garageModels,
                name: 'garageModels',
                component: () => import('../views/admin/GarageModels.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.garageModels'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.garageModels + '/'
                    }
                }
            },
            {
                path: enRoute.trailerModels,
                name: 'trailerModels',
                component: () => import('../views/admin/TrailerModels.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.trailerModels'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.trailerModels + '/'
                    }
                }
            },
            {
                path: enRoute.truckModels,
                name: 'truckModels',
                component: () => import('../views/admin/TruckModels.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.truckModels'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.truckModels + '/'
                    }
                }
            },
            {
                path: enRoute.cargos,
                name: 'cargos',
                component: () => import('../views/admin/Cargos.vue'),
                meta: {
                    requiresAuth: true,
                    adminOnly: true,
                    title: i18n.t('pages.cargos'),
                    path: {
                        en: enRoute.adminDashboard + '/' + enRoute.cargos + '/'
                    }
                }
            }
        ]
    },
];

export default adminRoutes;
