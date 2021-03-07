import i18n, { enRoute, skRoute } from "../lang";
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
                    title: 'pages.dashboard',
                    path: {
                        en: enRoute.dashboard,
                        sk: skRoute.dashboard,
                    }
                }
            },
            {
                path: enRoute.users,
                alias: [skRoute.users],
                name: 'users',
                component: () => import('../views/user/Users.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.users',
                    path: {
                        en: enRoute.users,
                        sk: skRoute.users,
                    }
                }
            },
            {
                path: `${enRoute.users}/:id`,
                alias: [`${skRoute.users}/:id`],
                name: 'user',
                component: () => import('../views/user/User.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.user',
                    path: {
                        en: `${enRoute.users}/:id`,
                        sk: `${skRoute.users}/:id`,
                    }
                }
            },
            {
                path: enRoute.truckShop,
                alias: [skRoute.truckShop],
                name: 'truckShop',
                component: () => import('../views/user/TruckModels.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.truckShop',
                    path: {
                        en: enRoute.truckShop,
                        sk: skRoute.truckShop,
                    }
                }
            },
            {
                path: enRoute.garageShop,
                alias: [skRoute.garageShop],
                name: 'garageShop',
                component: () => import('../views/user/GarageModels.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.garageShop',
                    path: {
                        en: enRoute.garageShop,
                        sk: skRoute.garageShop,
                    }
                }
            },
            {
                path: enRoute.trailerShop,
                alias: [skRoute.trailerShop],
                name: 'trailerShop',
                component: () => import('../views/user/TrailerModels.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.trailerShop',
                    path: {
                        en: enRoute.trailerShop,
                        sk: skRoute.trailerShop,
                    }
                }
            },
            {
                path: enRoute.garages,
                alias: [skRoute.garages],
                name: 'garages',
                component: () => import('../views/user/Garages.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.garages',
                    path: {
                        en: enRoute.garages,
                        sk: skRoute.garages,
                    }
                }
            },
            {
                path: `${enRoute.garages}/:id`,
                alias: [`${skRoute.garages}/:id`],
                name: 'garage',
                component: () => import('../views/user/Garage.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.garage',
                    path: {
                        en: `${enRoute.garages}/:id`,
                        sk: `${skRoute.garages}/:id`,
                    }
                }
            },
            {
                path: enRoute.trucks,
                alias: [skRoute.trucks],
                name: 'trucks',
                component: () => import('../views/user/Trucks.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.trucks',
                    path: {
                        en: enRoute.trucks,
                        sk: skRoute.trucks,
                    }
                }
            },
            {
                path: `${enRoute.trucks}/:id`,
                alias: [`${skRoute.trucks}/:id`],
                name: 'truck',
                component: () => import('../views/user/Truck.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.truck',
                    path: {
                        en: `${enRoute.trucks}/:id`,
                        sk: `${skRoute.trucks}/:id`,
                    }
                }
            },
            {
                path: enRoute.trailers,
                alias: [skRoute.trailers],
                name: 'trailers',
                component: () => import('../views/user/Trailers.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.trailers',
                    path: {
                        en: enRoute.trailers,
                        sk: skRoute.trailers,
                    }
                }
            },
            {
                path: `${enRoute.trailers}/:id`,
                alias: [`${skRoute.trailers}/:id`],
                name: 'trailer',
                component: () => import('../views/user/Trailer.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.trailers',
                    path: {
                        en: `${enRoute.trailers}/:id`,
                        sk: `${skRoute.trailers}/:id`,
                    }
                }
            },
            {
                path: enRoute.recruitmentAgencyDrivers,
                alias: [skRoute.recruitmentAgencyDrivers],
                name: 'recruitmentAgencyDrivers',
                component: () => import('../views/user/RecruitmentAgencyDrivers.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.recruitmentAgencyDrivers',
                    path: {
                        en: enRoute.recruitmentAgencyDrivers,
                        sk: skRoute.recruitmentAgencyDrivers,
                    }
                }
            },
            {
                path: enRoute.drivers,
                alias: [skRoute.drivers],
                name: 'drivers',
                component: () => import('../views/user/Drivers.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.drivers',
                    path: {
                        en: enRoute.drivers,
                        sk: skRoute.drivers,
                    }
                }
            },
            {
                path: `${enRoute.drivers}/:id`,
                alias: [`${skRoute.drivers}/:id`],
                name: 'driver',
                component: () => import('../views/user/Driver.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.driver',
                    path: {
                        en: `${enRoute.drivers}/:id`,
                        sk: `${skRoute.drivers}/:id`,
                    }
                }
            },
            {
                path: enRoute.orderOffers,
                alias: [skRoute.orderOffers],
                name: 'orderOffers',
                component: () => import('../views/user/Markets.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.orderOffers',
                    path: {
                        en: enRoute.orderOffers,
                        sk: skRoute.orderOffers,
                    }
                }
            },
            {
                path: enRoute.orders,
                alias: [skRoute.orders],
                name: 'orders',
                component: () => import('../views/user/Orders.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.orders',
                    path: {
                        en: enRoute.orders,
                        sk: skRoute.orders,
                    }
                }
            },
            {
                path: enRoute.doneOrders,
                alias: [skRoute.doneOrders],
                name: 'doneOrders',
                component: () => import('../views/user/DoneOrders.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.doneOrders',
                    path: {
                        en: enRoute.doneOrders,
                        sk: skRoute.doneOrders,
                    }
                }
            },
            {
                path: `${enRoute.orders}/:id`,
                alias: [`${skRoute.orders}/:id`],
                name: 'order',
                component: () => import('../views/user/Order.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.order',
                    path: {
                        en: `${enRoute.orders}/:id`,
                        sk: `${skRoute.orders}/:id`,
                    }
                }
            },
            {
                path: enRoute.transactions,
                alias: [skRoute.transactions],
                name: 'transactions',
                component: () => import('../views/user/Transactions.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.transactions',
                    path: {
                        en: enRoute.transactions,
                        sk: skRoute.transactions,
                    }
                }
            },
            {
                path: enRoute.bankLoans,
                alias: [skRoute.bankLoans],
                name: 'bankLoans',
                component: () => import('../views/user/BankLoans.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.bankLoans',
                    path: {
                        en: enRoute.bankLoans,
                        sk: skRoute.bankLoans,
                    }
                }
            },
            {
                path: enRoute.messages,
                alias: [skRoute.messages],
                name: 'messages',
                component: () => import('../views/user/Messages.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.messages',
                    path: {
                        en: enRoute.messages,
                        sk: skRoute.messages,
                    }
                }
            },
            {
                path: enRoute.scoreBoard,
                alias: [skRoute.scoreBoard],
                name: 'scoreBoard',
                component: () => import('../views/user/ScoreBoard.vue'),
                meta: {
                    requiresAuth: true,
                    userOnly: true,
                    title: 'pages.scoreBoard',
                    path: {
                        en: enRoute.scoreBoard,
                        sk: skRoute.scoreBoard,
                    }
                }
            },
        ]
    },
];

export default userRoutes;
