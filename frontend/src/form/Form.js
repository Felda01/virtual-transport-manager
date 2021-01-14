import { deepCopy } from './util'

export const TRANSLATABLE_KEY = 'translatable';

class Form {

    /**
     * Create a new form instance.
     *
     * @param {Object} data
     */
    constructor (data = {}) {
        this.busy = false
        this.successful = false
        this.originalData = deepCopy(data)

        Object.assign(this, data)
    }

    /**
     * Fill form data.
     *
     * @param {Object} data
     */
    fill (data) {
        this.keys().forEach(key => {
            this[key] = data[key]
        })
    }

    /**
     * Get the form data.
     *
     * @return {Object}
     */
    data () {
        return this.keys().reduce((data, key) => (
            { ...data, [key]: this[key] }
        ), {})
    }

    transformTranslatableData(data) {
        let result = [];

        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                if (key !== TRANSLATABLE_KEY) {
                    if (data[key] instanceof Array) {
                        data[key] = data[key].filter((item) => {
                            return item !== "";
                        })
                        result[key] = data[key].join(',');
                    } else {
                        result[key] = data[key];
                    }

                } else {
                    for (let translatableProperty in data[key]) {
                        if (data[key].hasOwnProperty(translatableProperty)) {
                            result[translatableProperty] = []
                            for (let locale in data[key][translatableProperty]) {
                                if (data[key][translatableProperty].hasOwnProperty(locale)) {
                                    result[translatableProperty].push({'locale': locale, 'value': data[key][translatableProperty][locale]});
                                }
                            }
                        }
                    }
                }
            }
        }

        return result;
    }

    /**
     * Get the form data keys.
     *
     * @return {Array}
     */
    keys () {
        return Object.keys(this)
            .filter(key => !Form.ignore.includes(key))
    }

    /**
     * Start processing the form.
     */
    startProcessing () {
        this.busy = true
        this.successful = false
    }

    /**
     * Finish processing the form.
     */
    finishProcessing () {
        this.busy = false
        this.successful = true
    }

    /**
     * Clear the form errors.
     */
    clear () {
        this.successful = false
    }

    /**
     * Reset the form fields.
     */
    reset () {
        Object.keys(this)
            .filter(key => !Form.ignore.includes(key))
            .forEach(key => {
                this[key] = deepCopy(this.originalData[key])
            })
    }

    /**
     * Submit the form data via an HTTP request.
     *
     * @param  {String} apollo (vue apollo client)
     * @param  {String} mutation
     * @return {Promise}
     */
    submit (apollo, mutation) {
        this.startProcessing()

        let data = this.data()

        const finalData = this.transformTranslatableData(data);

        return new Promise((resolve, reject) => {
            apollo.mutate({
                mutation: mutation,
                variables: finalData
            }).then(response => {
                this.finishProcessing()
                resolve(response)
            })
            .catch(error => {
                this.busy = false
                reject(error)
            })
        });
    }
}

Form.errorMessage = 'Something went wrong. Please try again.'
Form.ignore = ['busy', 'successful', 'originalData']

export default Form
