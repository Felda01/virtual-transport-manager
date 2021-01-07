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

export const CARGOS_QUERY = gql`
    query CargosQuery($limit: Int!, $page: Int!) {
        cargos(limit: $limit, page: $page) {
            data {
                id
                name
                name_translations
                adr
                engine_power
                chassis
                weight
                min_price
                max_price
                image
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

export const ADMIN_DASHBOARD_QUERY = gql`
    query AdminDashboardQuery {
        adminDashboard
    }
`;

