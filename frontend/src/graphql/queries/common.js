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

export const TRUCK_BRANDS_QUERY = gql`
    query TruckBrandsQuery {
        truckBrands
    }
`;

export const TRUCK_CHASSIS_QUERY = gql`
    query TruckChassisQuery {
        truckChassis
    }
`;

export const TRUCK_EMISSION_CLASSES_QUERY = gql`
    query TruckEmissionClassesQuery {
        truckEmissionClasses
    }
`;
