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

export const TRAILER_ADRS_QUERY = gql`
    query TrailerADRsQuery {
        trailerADRs
    }
`;
