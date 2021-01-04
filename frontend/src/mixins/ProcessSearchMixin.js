export default {
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
                    } else {
                        value = searchModel[key]
                    }
                    if (value) {
                        result.push({column: key, value: value});
                    }
                }
            }
            return result;
        }
    }
}
