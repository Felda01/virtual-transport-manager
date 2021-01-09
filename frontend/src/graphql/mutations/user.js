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
