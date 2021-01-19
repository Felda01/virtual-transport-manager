import gql from 'graphql-tag'

export const CREATE_GARAGE_MUTATION = gql`
    mutation CreateGarageMutation($garage_model: String!, $location: String!) {
        createGarage(garage_model: $garage_model, location: $location) {
            id
            garageModel {
                id
                name
            }
            location {
                id
                name
            }
        }
    }
`;

export const CREATE_TRUCK_MUTATION = gql`
    mutation CreateTruckMutation($truck_model: String!, $garage: String!) {
        createTruck(truck_model: $truck_model, garage: $garage) {
            id
            truckModel {
                id
                name
                brand
            }
            garage {
                id
                location {
                    id
                    name
                }
            }
        }
    }
`;

export const CREATE_TRAILER_MUTATION = gql`
    mutation CreateTrailerMutation($trailer_model: String!, $garage: String!) {
        createTrailer(trailer_model: $trailer_model, garage: $garage) {
            id
            trailerModel {
                id
                name
            }
            garage {
                id
                location {
                    id
                    name
                }
            }
        }
    }
`;

export const CREATE_DRIVER_MUTATION = gql`
    mutation CreateDriverMutation($driver: String!, $garage: String!) {
        createDriver(driver: $driver, garage: $garage) {
            id
            first_name
            last_name
            garage {
                id
                location {
                    id
                    name
                }
            }
        }
    }
`;

export const CREATE_USER_MUTATION = gql`
    mutation CreateUserMutation($first_name: String!, $last_name: String!, $email: String!, $roles: String!) {
        createUser(first_name: $first_name, last_name: $last_name, email: $email, roles: $roles) {
            id
            first_name
            last_name
        }
    }
`;
