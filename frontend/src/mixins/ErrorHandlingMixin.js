export default {
    data() {
        return {
            errorMessage: ''
        }
    },
    methods: {
        setErrorMessage(error) {
            if (error.graphQLErrors && error.graphQLErrors[0]) {
                if (error.graphQLErrors[0].extensions && error.graphQLErrors[0].extensions.validation && error.graphQLErrors[0].extensions.validation['id'] && error.graphQLErrors[0].extensions.validation['id'][0]) {
                    this.errorMessage = error.graphQLErrors[0].extensions.validation['id'][0];
                } else {
                    this.errorMessage = error.graphQLErrors[0].message;
                }
            }
        }
    }
}
