export default {
    data() {
        return {
            errorMessage: ''
        }
    },
    methods: {
        setErrorMessage(error) {
            if (error.graphQLErrors && error.graphQLErrors[0]) {
                this.errorMessage = error.graphQLErrors[0].message;
            }
        }
    }
}
