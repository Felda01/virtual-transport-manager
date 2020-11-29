import gql from 'graphql-tag'

export const ME_QUERY = gql`
    query MeQuery {
        me {
            id,
            first_name,
            last_name,
            image,
            roles {
                name
            }
        }
    }
`;
