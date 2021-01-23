import gql from 'graphql-tag'

export const USERS_QUERY = gql`
    query UsersQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        users(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
                image
                salary
                roles {
                    id
                    name
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const USER_QUERY = gql`
    query UserQuery($id: String!) {
        user(id: $id) {
            id
            first_name
            last_name
            image
            salary
            roles {
                id
                name
            }
        }
    }
`;

export const AVAILABLE_GARAGES_QUERY = gql`
    query AvailableGaragesQuery($limit: Int!, $page: Int!, $type: String!) {
        availableGarages(limit: $limit, page: $page, type: $type) {
            data {
                id
                garageModel {
                    id
                    name
                }
                location {
                    id
                    name
                    country {
                        id
                        name
                    }
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const GARAGES_QUERY = gql`
    query GaragesQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        garages(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                garageModel {
                    id
                    name
                    truck_count
                    trailer_count
                    image
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
                trucks {
                    id
                }
                trailers {
                    id
                }
                drivers {
                    id
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const GARAGES_SELECT_QUERY = gql`
    query GaragesQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        garages(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                garageModel {
                    id
                    name
                }
                location {
                    id
                    name
                    country {
                        id
                        name
                    }
                }
            }
        }
    }
`;

export const COUNTRIES_WITH_GARAGE_QUERY = gql`
    query CountriesWithGarages($limit: Int!, $page: Int!) {
        countriesWithGarages(limit: $limit, page: $page) {
            data {
                id
                name
                short_name
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const TRUCKS_QUERY = gql`
    query TrucksQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        trucks(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                trailer {
                    id
                    trailerModel {
                        id
                        name
                    }
                }
                garage {
                    id
                    garageModel {
                        id
                        name
                    }
                    location {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                }
                drivers {
                    id
                    first_name
                    last_name
                    image
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const TRAILERS_QUERY = gql`
    query TrailersQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        trailers(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                status
                trailerModel {
                    id
                    name
                    type
                    image
                    adr
                }
                truck {
                    id
                    truckModel {
                        id
                        name
                        brand
                    }
                }
                garage {
                    id
                    garageModel {
                        id
                        name
                    }
                    location {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const RECRUITMENT_AGENCY_DRIVERS_QUERY = gql`
    query RecruitmentAgencyDriversQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        recruitmentAgencyDrivers(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                first_name
                last_name
                image
                salary
                preferred_road_trips
                adr
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const DRIVERS_QUERY = gql`
    query DriversQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        drivers(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
                image
                salary
                preferred_road_trips
                adr
                satisfaction
                status
                truck {
                    id
                    truckModel {
                        id
                        name
                        brand
                    }
                    trailer {
                        id
                        trailerModel {
                            id
                            name
                        }
                    }
                }
                garage {
                    id
                    garageModel {
                        id
                        name
                    }
                    location {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const GARAGE_QUERY = gql`
    query GarageQuery($id: String!) {
        garage(id: $id) {
            id
            garageModel {
                id
                name
                truck_count
                trailer_count
                image
                price
                tax
                insurance
            }
            location {
                id
                name
                country {
                    id
                    short_name
                }
            }
            trucks {
                id
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                trailer {
                    id
                }
                drivers {
                    id
                }
            }
            trailers {
                id
                status
                trailerModel {
                    id
                    name
                    type
                    image
                    adr
                }
                truck {
                    id
                }
            }
            drivers {
                id
                first_name
                last_name
                image         
                adr
                status
                truck {
                    id
                    trailer {
                        id
                    }
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
            }
        }
    }
`;

export const AVAILABLE_GARAGE_MODEL_UPGRADES_QUERY = gql`
    query AvailableGarageModelUpgradesQuery($id: String!, $limit: Int!, $page: Int!) {
        availableGarageModelUpgrades(id: $id, limit: $limit, page: $page) {
            data {
                id
                name
                price
                truck_count
                trailer_count
            }
        }
    }
`;

export const TRUCK_QUERY = gql`
    query TruckQuery($id: String!) {
        truck(id: $id) {
            id
            status
            truckModel {
                id
                name
                brand
                engine_power
                chassis
                load
                emission_class
                km
                image
                price
                tax
                insurance
            }
            km
            trailer {
                id
                status
                trailerModel {
                    id
                    name
                    type
                    image
                    adr
                }
            }
            drivers {
                id
                first_name
                last_name
                image         
                adr
                status
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
            }
            garage {
                id
                garageModel {
                    id
                    name
                    truck_count
                    trailer_count
                    image
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
                trucks {
                    id
                }
                trailers {
                    id
                }
                drivers {
                    id
                }
            }
        }
    }
`;

export const TRAILER_QUERY = gql`
    query TrailerQuery($id: String!) {
        trailer(id: $id) {
            id
            status
            km
            trailerModel {
                id
                name
                type
                load
                adr
                km
                insurance
                tax
                image
                price
            }
            truck {
                id
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                drivers {
                    id
                    first_name
                    last_name
                    image         
                    adr
                    status
                    location {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                }
            }
            garage {
                id
                garageModel {
                    id
                    name
                    truck_count
                    trailer_count
                    image
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
                trucks {
                    id
                }
                trailers {
                    id
                }
                drivers {
                    id
                }
            }
        }
    }
`;

export const DRIVER_QUERY = gql`
    query DriverQuery($id: String!) {
        driver(id: $id) {
            id
            first_name
            last_name
            image
            salary
            preferred_road_trips
            adr
            satisfaction
            status
            location {
                id
                name
                country {
                    id
                    short_name
                }
            }
            truck {
                id
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                trailer {
                    id
                    trailerModel {
                        id
                        name
                    }
                }
            }
            garage {
                id
                garageModel {
                    id
                    name
                    truck_count
                    trailer_count
                    image
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
                trucks {
                    id
                }
                trailers {
                    id
                }
                drivers {
                    id
                }
            }
        }
    }
`;

