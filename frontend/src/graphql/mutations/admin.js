import gql from 'graphql-tag'

export const CREATE_COUNTRY_MUTATION = gql`
    mutation CreateCountryMutation($name_translations: [TranslationInput]!, $short_name: String!) {
        createCountry(name_translations: $name_translations, short_name: $short_name) {
            id
            name
            short_name
        }
    }
`;
