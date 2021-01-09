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
