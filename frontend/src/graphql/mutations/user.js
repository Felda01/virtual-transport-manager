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
