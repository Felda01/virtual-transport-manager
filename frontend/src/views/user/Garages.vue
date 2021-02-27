<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.garages.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="10" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100 mb-3">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <template v-if="garages.data && garages.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="garages.data" v-if="garages && garages.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item)" class="cursor-pointer-hover">
                                    <md-table-cell md-label="#">{{ index + garages.from }}</md-table-cell>
                                    <md-table-cell md-label="">
                                        <div class="img-container">
                                            <img :src="item.garageModel.image" :alt="item.garageModel.name" />
                                        </div>
                                    </md-table-cell>
                                    <md-table-cell :md-label="$t('garageModel.property.name')" class="td-name">{{ item.garageModel.name }}</md-table-cell>
                                    <md-table-cell :md-label="$t('garage.property.drivers')">{{ item.drivers.length }} / {{ item.garageModel.truck_count * 2 }}</md-table-cell>
                                    <md-table-cell :md-label="$t('garage.property.trucks')">{{ item.trucks.length }} / {{ item.garageModel.truck_count }}</md-table-cell>
                                    <md-table-cell :md-label="$t('garage.property.trailers')">{{ item.trailers.length }} / {{ item.garageModel.trailer_count }}</md-table-cell>
                                    <md-table-cell :md-label="$t('garage.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                 </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: garages.from, to: garages.to, total: garages.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="garages.per_page"
                                        :total="garages.total"></pagination>
                        </md-card-actions>
                    </md-card>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>
    </div>
</template>

<script>
    import { GARAGES_QUERY, COUNTRIES_WITH_GARAGE_QUERY } from '@/graphql/queries/user';
    import { GARAGE_MODELS_QUERY } from "@/graphql/queries/common";
    import { Pagination, SearchForm } from "@/components";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.garages');
        },
        name: "Garages",
        components: {
            Pagination,
            SearchForm
        },
        data() {
            return {
                garages: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                garageModels: [],
                garageModelsOptions: [],
                countriesWithGarages: [],
                countriesWithGaragesOptions: [],
                searchModel: {
                    garage_model: '',
                    country: [''],
                    available_truck_spot: false,
                    available_trailer_spot: false,
                },
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'garage_model',
                                    label: this.$t('garage.property.garage_model'),
                                    value: '',
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.name;
                                        },
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'country',
                                    label: this.$t('garage.searchFields.country'),
                                    value: [''],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.name + ' (' + option.short_name.toUpperCase() + ')';
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'switch',
                                    input: 'switch',
                                    name: 'available_truck_spot',
                                    label: this.$t('garage.searchFields.available_truck_spot'),
                                    value: false
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'switch',
                                    input: 'switch',
                                    name: 'available_trailer_spot',
                                    label: this.$t('garage.searchFields.available_trailer_spot'),
                                    value: false
                                }
                            ],
                        },
                    ]
                },
            }
        },
        methods: {
            clickTableRow(item) {
                this.$router.push({
                    name: 'garage',
                    params: {id: item.id}
                });
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', function (payLoad) {
                if (payLoad.modelType === 'Garage') {
                    this.$apollo.queries.garages.refresh();
                    this.$apollo.queries.countriesWithGarages.refresh();
                }
            });
        },
        apollo: {
            garages: {
                query: GARAGES_QUERY,
                variables() {
                    return { page: this.page, limit: this.garages.per_page, filter: this.filters }
                },
                result({ data, loading, networkStatus }) {
                    this.firstLoad = false;
                }
            },
            garageModels: {
                query: GARAGE_MODELS_QUERY,
                variables() {
                    return { page: 1, limit: -1 }
                },
                result({ data, loading, networkStatus }) {
                    this.garageModelsOptions = data.garageModels.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.garageModelsOptions);
                    });
                }
            },
            countriesWithGarages: {
                query: COUNTRIES_WITH_GARAGE_QUERY,
                variables() {
                    return { page: 1, limit: -1 }
                },
                result({ data, loading, networkStatus }) {
                    this.countriesWithGaragesOptions = data.countriesWithGarages.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.countriesWithGaragesOptions);
                    });
                }
            }

        },
    }
</script>

<style scoped>

</style>
