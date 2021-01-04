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
