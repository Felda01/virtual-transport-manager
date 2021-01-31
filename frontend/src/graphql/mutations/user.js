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

export const UPDATE_GARAGE_MUTATION = gql`
    mutation UpdateGarageMutation($id: String!, $garage_model: String!) {
        updateGarage(id: $id, garage_model: $garage_model) {
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
`;

export const DELETE_GARAGE_MUTATION = gql`
    mutation DeleteGarageMutation($id: String!) {
        deleteGarage(id: $id) {
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
`;

export const ASSIGN_DRIVER_TO_TRUCK_MUTATION = gql`
    mutation AssignDriverToTruckMutation($driver: String!, $truck: String!) {
        assignDriverToTruck(driver: $driver, truck: $truck) {
            id
            first_name
            last_name
            truck {
                id
                truckModel {
                    id
                    brand
                    name
                }
            }
        }
    }
`;

export const ASSIGN_TRAILER_TO_TRUCK_MUTATION = gql`
    mutation AssignTrailerToTruckMutation($trailer: String!, $truck: String!) {
        assignTrailerToTruck(trailer: $trailer, truck: $truck) {
            id
            trailerModel {
                id
                name
            }
            truck {
                id
                truckModel {
                    id
                    brand
                    name
                }
            }
        }
    }
`;

export const UNASSIGN_DRIVER_FROM_TRUCK_MUTATION = gql`
    mutation unassignDriverFromTruckMutation($driver: String!, $truck: String!) {
        unassignDriverFromTruck(driver: $driver, truck: $truck) {
            id
            first_name
            last_name
        }
    }
`;

export const UNASSIGN_TRAILER_FROM_TRUCK_MUTATION = gql`
    mutation unassignTrailerFromTruckMutation($trailer: String!, $truck: String!) {
        unassignTrailerFromTruck(trailer: $trailer, truck: $truck) {
            id
            trailerModel {
                id
                name
            }
        }
    }
`;

export const UPDATE_DRIVER_MUTATION = gql`
    mutation UpdateDriverMutation($id: String!) {
        updateDriver(id: $id) {
            id
            first_name
            last_name
        }
    }
`;

export const DELETE_DRIVER_MUTATION = gql`
    mutation DeleteDriverMutation($id: String!) {
        deleteDriver(id: $id) {
            id
            first_name
            last_name
        }
    }
`;

export const DELETE_TRAILER_MUTATION = gql`
    mutation DeleteTrailerMutation($id: String!) {
        deleteTrailer(id: $id) {
            id
            trailerModel {
                id 
                name
            }
        }
    }
`;

export const DELETE_TRUCK_MUTATION = gql`
    mutation DeleteTruckMutation($id: String!) {
        deleteTruck(id: $id) {
            id
            truckModel {
                id 
                name
                brand
            }
        }
    }
`;

export const DELETE_USER_MUTATION = gql`
    mutation DeleteUserMutation($id: String!) {
        deleteUser(id: $id) {
            id
            first_name
            last_name
        }
    }
`;

export const UPDATE_USER_PASSWORD_MUTATION = gql`
    mutation UpdateUserPasswordMutation($id: String!, $password: String!, $new_password: String!, $new_password_confirmation: String!) {
        updateUserPassword(id: $id, password: $password, new_password: $new_password, new_password_confirmation: $new_password_confirmation) {
            id
        }
    }
`;

export const UPDATE_USER_MUTATION = gql`
    mutation UpdateUserMutation($id: String!, $first_name: String!, $last_name: String!, $email: String!, $image: String) {
        updateUser(id: $id, first_name: $first_name, last_name: $last_name, email: $email, image: $image) {
            id
        }
    }
`;
export const UPDATE_USER_SALARY_MUTATION = gql`
    mutation UpdateUserSalaryMutation($id: String!, $salary: String!, $roles: String!) {
        updateUserSalary(id: $id, salary: $salary, roles: $roles) {
            id
            first_name
            last_name
        }
    }
`;

