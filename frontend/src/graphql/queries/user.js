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

export const ACTIVITIES_QUERY = gql`
    query ActivitiesQuery($limit: Int!, $page: Int!, $user: String) {
        activities(limit: $limit, page: $page, user: $user) {
            data {
                id
                description
                subject {
                    __typename
                    ... on Garage {
                        __typename
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
                    ... on Truck {
                        __typename
                        id
                        truckModel {
                            id
                            brand
                            name
                        }
                        garage {
                            id
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
                    ... on Trailer {
                        __typename
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
                                country {
                                    id
                                    short_name
                                }
                            }
                        }
                    }
                    ... on Driver {
                        __typename
                        id
                        first_name
                        last_name
                        garage {
                            id
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
                    ... on User {
                        __typename
                        id
                        first_name
                        last_name
                    }
                    ... on Order {
                        __typename 
                        id
                        market {
                            id
                            cargo {
                                id
                                name
                            }
                            locationFrom {
                                id
                                name
                                country {
                                    id
                                    short_name
                                }
                            }
                            locationTo {
                                id
                                name
                                country {
                                    id
                                    short_name
                                }
                            }
                        }
                    }
                    ... on BankLoan {
                        __typename
                        id
                        bankLoanType {
                            id
                            value
                            payment
                            period
                        }
                    }
                }
                created_at
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
            email
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
                    adr
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

export const DRIVERS_QUERY = gql`
    query DriversQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        drivers(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
                image
                salary
                adr              
                status
                sleep
                truck {
                    id
                    truckModel {
                        id
                        name
                        brand
                    }
                    trailer {
                        id
                        trailerModel {
                            id
                            name
                        }
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
                location {
                    id
                    name
                    country {
                        id
                        short_name
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

export const GARAGE_QUERY = gql`
    query GarageQuery($id: String!) {
        garage(id: $id) {
            id
            garageModel {
                id
                name
                truck_count
                trailer_count
                image
                price
                tax
                insurance
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
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                trailer {
                    id
                }
                drivers {
                    id
                }
            }
            trailers {
                id
                status
                trailerModel {
                    id
                    name
                    type
                    image
                    adr
                }
                truck {
                    id
                }
            }
            drivers {
                id
                first_name
                last_name
                image         
                adr
                status
                sleep
                truck {
                    id
                    trailer {
                        id
                    }
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
    }
`;

export const AVAILABLE_GARAGE_MODEL_UPGRADES_QUERY = gql`
    query AvailableGarageModelUpgradesQuery($id: String!, $limit: Int!, $page: Int!) {
        availableGarageModelUpgrades(id: $id, limit: $limit, page: $page) {
            data {
                id
                name
                price
                truck_count
                trailer_count
            }
        }
    }
`;

export const TRUCK_QUERY = gql`
    query TruckQuery($id: String!) {
        truck(id: $id) {
            id
            status
            truckModel {
                id
                name
                brand
                engine_power
                chassis
                load
                emission_class
                km
                image
                price
                tax
                insurance
            }
            km
            trailer {
                id
                status
                trailerModel {
                    id
                    name
                    type
                    image
                    adr
                }
            }
            drivers {
                id
                first_name
                last_name
                image         
                adr
                status
                sleep
                location {
                    id
                    name
                    country {
                        id
                        short_name
                    }
                }
            }
            garage {
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
            lastOrders {
                id
                market {
                    id
                    price
                    locationFrom {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    locationTo {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    cargo {
                        id
                        name
                        image
                    }
                }
                roadTrip {
                    id
                    status
                }
                drivers {
                    id
                    first_name
                    last_name
                }
                truck {
                    id
                    truckModel {
                        id
                        brand
                        name
                    }
                }
                trailer {
                    id
                    trailerModel {
                        id
                        name
                    }
                }
            }
        }
    }
`;

export const TRAILER_QUERY = gql`
    query TrailerQuery($id: String!) {
        trailer(id: $id) {
            id
            status
            km
            trailerModel {
                id
                name
                type
                load
                adr
                km
                insurance
                tax
                image
                price
            }
            truck {
                id
                status
                truckModel {
                    id
                    name
                    brand
                    image
                }
                drivers {
                    id
                    first_name
                    last_name
                    image         
                    adr
                    status
                    sleep
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
            garage {
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
            lastOrders {
                id
                market {
                    id
                    price
                    locationFrom {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    locationTo {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    cargo {
                        id
                        name
                        image
                    }
                }
                roadTrip {
                    id
                    status
                }
                drivers {
                    id
                    first_name
                    last_name
                }
                truck {
                    id
                    truckModel {
                        id
                        brand
                        name
                    }
                }
                trailer {
                    id
                    trailerModel {
                        id
                        name
                    }
                }
            }
        }
    }
`;

export const AVAILABLE_DRIVERS_IN_GARAGE_QUERY = gql`
    query AvailableDriversInGarageQuery($garage: String!, $limit: Int!, $page: Int!) {
        availableDriversInGarage(garage: $garage, limit: $limit, page: $page) {
            data {
                id
                first_name
                last_name
            }
        }
    }
`;

export const AVAILABLE_TRAILERS_IN_GARAGE_QUERY = gql`
    query AvailableTrailersInGarageQuery($garage: String!, $limit: Int!, $page: Int!) {
        availableTrailersInGarage(garage: $garage, limit: $limit, page: $page) {
            data {
                id
                trailerModel {
                    id
                    name
                }
            }
        }
    }
`;

export const AVAILABLE_TRUCKS_IN_GARAGE_QUERY = gql`
    query AvailableTrucksInGarageQuery($garage: String!, $relation: String!, $limit: Int!, $page: Int!) {
        availableTrucksInGarage(garage: $garage, relation: $relation limit: $limit, page: $page) {
            data {
                id
                truckModel {
                    id
                    name
                    brand
                }
            }
        }
    }
`;

export const DRIVER_QUERY = gql`
    query DriverQuery($id: String!) {
        driver(id: $id) {
            id
            first_name
            last_name
            image
            salary
            sleep
            adr
            status
            location {
                id
                name
                country {
                    id
                    short_name
                }
            }
            truck {
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
            }
            garage {
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
            lastOrders {
                id
                market {
                    id
                    price
                    locationFrom {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    locationTo {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    cargo {
                        id
                        name
                        image
                    }
                }
                roadTrip {
                    id
                    status
                }
                drivers {
                    id
                    first_name
                    last_name
                }
                truck {
                    id
                    truckModel {
                        id
                        brand
                        name
                    }
                }
                trailer {
                    id
                    trailerModel {
                        id
                        name
                    }
                }
            }
        }
    }
`;

export const MARKETS_QUERY = gql`
    query MarketsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        markets(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                price
                amount
                expires_at
                locationFrom {
                    id
                    name
                    lat
                    lng
                    country {
                        id
                        short_name
                    }
                }
                locationTo {
                    id
                    name
                    lat
                    lng
                    country {
                        id
                        short_name
                    }
                }
                cargo {
                    id
                    name
                    image
                    adr
                    weight
                    engine_power
                    chassis
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

export const ORDERS_QUERY = gql`
    query OrdersQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        orders(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                market {
                    id
                    price
                    expires_at
                    locationFrom {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    locationTo {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    cargo {
                        id
                        name
                        image 
                    }
                }
                roadTrip {
                    id
                    status
                }
                drivers {
                    id
                    first_name
                    last_name
                }
                truck {
                    id
                    truckModel {
                        id
                        name
                        brand
                    }
                }
                trailer {
                    id
                    trailerModel {
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

export const DONE_ORDERS_QUERY = gql`
    query OrdersQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String, $done: Boolean) {
        orders(limit: $limit, page: $page, filter: $filter, sort: $sort, done: $done) {
            data {
                id
                market {
                    id
                    price
                    locationFrom {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    locationTo {
                        id
                        name
                        country {
                            id
                            short_name
                        }
                    }
                    cargo {
                        id
                        name
                        image 
                    }
                }
                roadTrip {
                    id
                    status
                    km
                    time
                    fees
                    damage
                }
                drivers {
                    id
                    first_name
                    last_name
                }
                truck {
                    id
                    truckModel {
                        id
                        name
                        brand
                    }
                }
                trailer {
                    id
                    trailerModel {
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

export const ORDER_QUERY = gql`
    query OrderQuery($id: String!) {
        order(id: $id) {
            id
            market {
                id
                price
                expires_at
                locationFrom {
                    id
                    name
                    lat
                    lng
                    country {
                        id
                        short_name
                    }
                }
                locationTo {
                    id
                    name
                    lat
                    lng
                    country {
                        id
                        short_name
                    }
                }
                customerFrom {
                    id
                    name
                }
                customerTo {
                    id
                    name
                }
                cargo {
                    id
                    name
                    image 
                }
            }
            roadTrip {
                id
                status
            }
            drivers {
                id
                first_name
                last_name
                status
                image
                location {
                    id
                    name
                    lat
                    lng
                    country {
                        id
                        short_name
                    }
                }
            }
            truck {
                id
                status
                truckModel {
                    id
                    image
                    name
                    brand
                }
            }
            trailer {
                id
                status
                trailerModel {
                    id
                    image
                    name
                }
            }
        }
    }
`;

export const TRUCKS_FOR_ORDER_QUERY = gql`
    query TrucksForOrderQuery($order: String, $limit: Int!, $page: Int!) {
        trucksForOrder(order: $order, limit: $limit, page: $page) {
            data {
                id
                drivers {
                    id
                    first_name
                    last_name
                    location {
                        id
                        name
                        lat
                        lng
                        country {
                            id
                            short_name
                        }
                    }
                }
            }
        }
    }
`;

export const PATHS_FOR_ORDER = gql`
    query PathsForOrderQuery($order: String!, $truck: String) {
        pathsForOrder(order: $order, truck: $truck)
    }
`;

export const TRANSACTIONS_QUERY = gql`
    query TransactionsQuery($limit: Int!, $page: Int!, $filter: [FilterInput], $sort: String) {
        transactions(limit: $limit, page: $page, filter: $filter, sort: $sort) {
            data {
                id
                value
                activity
                created_at
                user {
                    id
                    first_name
                    last_name
                }
                productable {
                    __typename
                    ... on Garage {
                        __typename
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
                    ... on Truck {
                        __typename
                        id
                        truckModel {
                            id
                            brand
                            name
                        }
                        garage {
                            id
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
                    ... on Trailer {
                        __typename
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
                                country {
                                    id
                                    short_name
                                }
                            }
                        }
                    }
                    ... on Driver {
                        __typename
                        id
                        first_name
                        last_name
                        garage {
                            id
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
                    ... on Order {
                        __typename 
                        id
                        market {
                            id
                            cargo {
                                id
                                name
                            }
                            locationFrom {
                                id
                                name
                                country {
                                    id
                                    short_name
                                }
                            }
                            locationTo {
                                id
                                name
                                country {
                                    id
                                    short_name
                                }
                            }
                        }
                    }
                    ... on BankLoan {
                        __typename
                        id
                        bankLoanType {
                            id
                            value
                            payment
                            period
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

export const USERS_SELECT_QUERY = gql`
    query UsersQuery($limit: Int!, $page: Int!, $filter: [FilterInput]) {
        users(limit: $limit, page: $page, filter: $filter) {
            data {
                id
                first_name
                last_name
            }
        }
    }
`;

export const NEXT_PAYMENT_QUERY = gql`
    query NextPaymentQuery {
        nextPayment
    }
`;

export const BANK_LOANS_QUERY = gql`
    query BankLoansQuery($limit: Int!, $page: Int!) {
        bankLoans(limit: $limit, page: $page) {
            data {
                id
                paid
                bankLoanType {
                    id
                    value
                    payment
                    period
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

export const CONVERSATION_QUERY = gql`
    query ConversationQuery($user1: String!, $user2: String!, $limit: Int!, $page: Int!) {
        conversation(user1: $user1, user2: $user2,limit: $limit, page: $page) {
            data {
                id
                message
                userFrom {
                    id
                }
                userTo {
                    id
                }
                created_at
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
export const SCOREBOARD_QUERY = gql`
    query ScoreBoardQuery {
        scoreBoard {
            id
            name
            money
            value
        }
    }
`;

