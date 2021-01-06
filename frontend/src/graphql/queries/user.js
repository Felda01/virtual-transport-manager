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
