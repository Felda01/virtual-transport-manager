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

export const UPDATE_COUNTRY_MUTATION = gql`
    mutation UpdateCountryMutation($id: String!, $name_translations: [TranslationInput]!, $short_name: String!) {
        updateCountry(id: $id, name_translations: $name_translations, short_name: $short_name) {
            id
            name
            short_name
        }
    }
`;

export const DELETE_COUNTRY_MUTATION = gql`
    mutation DeleteCountryMutation($id: String!) {
        deleteCountry(id: $id) {
            name
        }
    }
`;

export const CREATE_LOCATION_MUTATION = gql`
    mutation CreateLocationMutation($name_translations: [TranslationInput]!, $is_city: Boolean!, $lat: String!, $lng: String!, $country: String!) {
        createLocation(name_translations: $name_translations, is_city: $is_city, lat: $lat, lng: $lng, country: $country) {
            id
            name
            is_city
            lat
            lng
            country {
                id
                name
            }
        }
    }
`;

export const UPDATE_LOCATION_MUTATION = gql`
    mutation UpdateLocationMutation($id: String!, $name_translations: [TranslationInput]!, $is_city: Boolean!, $lat: String!, $lng: String!, $country: String!) {
        updateLocation(id: $id, name_translations: $name_translations, is_city: $is_city, lat: $lat, lng: $lng, country: $country) {
            id
            name
            is_city
            lat
            lng
            country {
                id
                name
            }
        }
    }
`;

export const DELETE_LOCATION_MUTATION = gql`
    mutation DeleteLocationMutation($id: String!) {
        deleteLocation(id: $id) {
            name
        }
    }
`;

export const CREATE_ROUTE_MUTATION = gql`
    mutation CreateRouteMutation($location1: String!, $location2: String!, $time: String, $distance: String, $fee: String!) {
        createRoute(location1: $location1, location2: $location2, time: $time, distance: $distance, fee: $fee) {
            id
            location1 {
                id
                name
            }
            location2 {
                id
                name
            }
        }
    }
`;

export const UPDATE_ROUTE_MUTATION = gql`
    mutation UpdateRouteMutation($id: String!, $location1: String!, $location2: String!, $time: String!, $distance: String!, $fee: String!) {
        updateRoute(id: $id, location1: $location1, location2: $location2, time: $time, distance: $distance, fee: $fee) {
            id
            location1 {
                id
                name
            }
            location2 {
                id
                name
            }
        }
    }
`;

export const DELETE_ROUTE_MUTATION = gql`
    mutation DeleteRouteMutation($id: String!) {
        deleteRoute(id: $id) {
            id
            location1 {
                id
                name
            }
            location2 {
                id
                name
            }
        }
    }
`;
