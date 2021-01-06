export default {
    data() {
        return {
            searchModel: null,
            filters: [],
        }
    },
    methods: {
        processSearch(searchModel) {
            let result = [];
            for (let key in searchModel) {
                if (searchModel.hasOwnProperty(key)) {
                    let value = null;
                    if (searchModel[key] instanceof Array) {
                        searchModel[key] = searchModel[key].filter(item => {
                            return item !== '';
                        });

                        value = searchModel[key].join(',');
                    } else if (searchModel[key] instanceof Object && searchModel[key]['type'] === 'range') {
                        value = searchModel[key]['min'] + '_' + searchModel[key]['max'];
                    } else {
                        value = searchModel[key];
                    }
                    if (value) {
                        result.push({column: key, value: value});
                    }
                }
            }
            return result;
        },
        applySearch() {
            this.filters = this.processSearch(this.searchModel);
        },
    },
    created() {
        this.debounceFunction = this._.debounce(this.applySearch, 1000);
    },
    watch: {
        searchModel: {
            handler: function (val, oldVal) {
                this.debounceFunction();
            },
            deep: true
        }
    },
}
