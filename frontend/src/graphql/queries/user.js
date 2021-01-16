import gql from 'graphql-tag'

export const USERS_QUERY = gql`
    query UsersQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        users(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
                image
                salary
                roles {
                    id
                    name
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const USER_QUERY = gql`
    query UserQuery($id: String!) {
        user(id: $id) {
            id
            first_name
            last_name
            image
            salary
            roles {
                id
                name
            }
        }
    }
`;

export const AVAILABLE_GARAGES_QUERY = gql`
    query AvailableGaragesQuery($limit: Int!, $page: Int!, $type: String!) {
        availableGarages(limit: $limit, page: $page, type: $type) {
            data {
                id
                garageModel {
                    id
                    name
                }
                location {
                    id
                    name
                    country {
                        id
                        name
                    }
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const GARAGES_QUERY = gql`
    query GaragesQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        garages(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                garageModel {
                    id
                    name
                    truck_count
                    trailer_count
                    image
                }
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
                trucks {
                    id
                }
                trailers {
                    id
                }
                drivers {
                    id
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const GARAGES_SELECT_QUERY = gql`
    query GaragesQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        garages(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                garageModel {
                    id
                    name
                }
                location {
                    id
                    name
                    country {
                        id
                        name
                    }
                }
            }
        }
    }
`;

export const COUNTRIES_WITH_GARAGE_QUERY = gql`
    query CountriesWithGarages($limit: Int!, $page: Int!) {
        countriesWithGarages(limit: $limit, page: $page) {
            data {
                id
                name
                short_name
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const TRUCKS_QUERY = gql`
    query TrucksQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        trucks(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                trailer {
                    id
                    trailerModel {
                        id
                        name
                    }
                }
                garage {
                    id
                    garageModel {
                        id
                        name
                    }
                    location {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                }
                drivers {
                    id
                    first_name
                    last_name
                    image
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const TRAILERS_QUERY = gql`
    query TrailersQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        trailers(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                status
                trailerModel {
                    id
                    name
                    type
                    image
                }
                truck {
                    id
                    truckModel {
                        id
                        name
                        brand
                    }
                }
                garage {
                    id
                    garageModel {
                        id
                        name
                    }
                    location {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                }
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

export const RECRUITMENT_AGENCY_DRIVERS_QUERY = gql`
    query RecruitmentAgencyDriversQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        recruitmentAgencyDrivers(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                first_name
                last_name
                image
                salary
                preferred_road_trips
                adr
            }
            total
            per_page
            current_page
            from
            to
            last_page
            has_more_pages
        }
    }
`;

