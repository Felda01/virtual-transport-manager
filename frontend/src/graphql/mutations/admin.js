import gql from 'graphql-tag'

export const CREATE_COUNTRY_MUTATION = gql`
    mutation CreateCountryMutation($name: String!, $short_name: String!) {
        createCountry(name: $name, short_name: $short_name) {
            id
            name
            short_name
        }
    }
`;
