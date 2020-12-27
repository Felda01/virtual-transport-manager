import gql from 'graphql-tag'

export const COUNTRIES_QUERY = gql`
    query CountriesQuery($limit: Int!, $page: Int!) {
        countries(limit: $limit, page: $page) {
            data {
                id
                name
                name_translations
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

export const LOCATIONS_QUERY = gql`
    query LocationsQuery($limit: Int!, $page: Int!) {
        locations(limit: $limit, page: $page) {
            data {
                id
                name
                name_translations
                is_city
                lat
                lng
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

export const ROUTES_QUERY = gql`
    query RoutesQuery($limit: Int!, $page: Int!) {
        routes(limit: $limit, page: $page) {
            data {
                id
                location1 {
                    id
                    name
                },
                location2 {
                    id
                    name
                },
                time
                distance
                fee
                type
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

export const BANK_LOAN_TYPES_QUERY = gql`
    query BankLoanTypesQuery($limit: Int!, $page: Int!) {
        bankLoanTypes(limit: $limit, page: $page) {
            data {
                id
                value
                payment
                period
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

export const GARAGE_MODELS_QUERY = gql`
    query GarageModelsQuery($limit: Int!, $page: Int!) {
        garageModels(limit: $limit, page: $page) {
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
    query TrailerModelsQuery($limit: Int!, $page: Int!) {
        trailerModels(limit: $limit, page: $page) {
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
