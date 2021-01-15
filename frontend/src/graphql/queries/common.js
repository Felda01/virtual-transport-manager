import gql from 'graphql-tag'

export const ME_QUERY = gql`
    query MeQuery {
        me {
            id
            first_name
            last_name
            image
            roles {
                name
            }
        }
    }
`;

export const COMPANY_QUERY = gql`
    query CompanyQuery {
        company {
            id
            name
            money
        }
    }
`;

export const LOCALES_QUERY = gql`
    query LocalesQuery {
        locales
    }
`;

export const TRAILER_TYPES_QUERY = gql`
    query TrailerTypesQuery {
        trailerTypes
    }
`;

export const ADRS_QUERY = gql`
    query ADRsQuery {
        ADRs
    }
`;

export const TRUCK_BRANDS_QUERY = gql`
    query TruckBrandsQuery {
        truckBrands
    }
`;

export const CHASSIS_QUERY = gql`
    query ChassisQuery {
        chassis
    }
`;

export const TRUCK_EMISSION_CLASSES_QUERY = gql`
    query TruckEmissionClassesQuery {
        truckEmissionClasses
    }
`;

export const ROLES_QUERY = gql`
    query RolesQuery {
        roles {
            id
            name
        }
    }
`;

export const GARAGE_MODELS_QUERY = gql`
    query GarageModelsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        garageModels(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                name
                truck_count
                trailer_count
                insurance
                tax
                image
                price
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


export const TRAILER_MODELS_QUERY = gql`
    query TrailerModelsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        trailerModels(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
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

export const TRAILER_MODELS_SELECT_QUERY = gql`
    query TrailerModelsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        trailerModels(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                name
                type
            }
        }
    }
`;

export const TRUCK_MODELS_QUERY = gql`
    query TruckModelsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        truckModels(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                name
                brand
                engine_power
                chassis
                load
                emission_class
                km
                insurance
                tax
                image
                price
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

export const TRUCK_MODELS_SELECT_QUERY = gql`
    query TruckModelsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        truckModels(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                name
                brand
            }
        }
    }
`;

export const AVAILABLE_LOCATIONS_QUERY = gql`
    query AvailableLocationsQuery($limit: Int!, $page: Int!) {
        availableLocations(limit: $limit, page: $page) {
            data {
                id
                name
                country {
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

export const STATUSES_QUERY = gql`
    query StatusesQuery($model: String) {
        statuses(model: $model)
    }
`;
