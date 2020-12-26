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
    mutation UpdateRouteMutation($id: String!, $time: String!, $distance: String!, $fee: String!) {
        updateRoute(id: $id, time: $time, distance: $distance, fee: $fee) {
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

export const CREATE_BANK_LOAN_TYPE_MUTATION = gql`
    mutation CreateBankLoanTypeMutation($value: String!, $payment: String!, $period: String!) {
        createBankLoanType(value: $value, payment: $payment, period: $period) {
            id
            value
            payment
            period
        }
    }
`;

export const UPDATE_BANK_LOAN_TYPE_MUTATION = gql`
    mutation UpdateBankLoanTypeMutation($id: String!, $value: String!, $payment: String!, $period: String!) {
        updateBankLoanType(id: $id, value: $value, payment: $payment, period: $period) {
            id
            value
        }
    }
`;

export const DELETE_BANK_LOAN_TYPE_MUTATION = gql`
    mutation DeleteBankLoanTypeMutation($id: String!) {
        deleteBankLoanType(id: $id) {
            id
            value
            payment
            period
        }
    }
`;

export const CREATE_GARAGE_MODEL_MUTATION = gql`
    mutation CreateGarageModelMutation($image: String!, $name: String!, $truck_count: String!, $trailer_count: String!, $insurance: String!, $tax: String!) {
        createGarageModel(image: $image, name: $name, truck_count: $truck_count, trailer_count: $trailer_count, insurance: $insurance, tax: $tax) {
            id
            image
            name
            truck_count
            trailer_count
            insurance
            tax
        }
    }
`;

export const UPDATE_GARAGE_MODEL_MUTATION = gql`
    mutation UpdateGarageModelMutation($id: String!, $image: String!, $name: String!, $truck_count: String!, $trailer_count: String!, $insurance: String!, $tax: String!) {
        updateGarageModel(id: $id, image: $image, name: $name, truck_count: $truck_count, trailer_count: $trailer_count, insurance: $insurance, tax: $tax) {
            id
            name
        }
    }
`;

export const DELETE_GARAGE_MODEL_MUTATION = gql`
    mutation DeleteGarageModelMutation($id: String!) {
        deleteGarageModel(id: $id) {
            id
            name
        }
    }
`;
