import gql from 'graphql-tag'

export const COUNTRIES_QUERY = gql`
    query CountriesQuery($limit: Int!, $page: Int!) {
        countries(limit: $limit, page: $page) {
            data {
                id,
                name,
                short_name
            }
            total,
            per_page,
            current_page,
            from,
            to,
            last_page,
            has_more_pages
        }
    }
`;
