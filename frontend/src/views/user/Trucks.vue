<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.trucks.loading && firstLoad">
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
            <template v-if="trucks.data && trucks.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="trucks.data" v-if="trucks && trucks.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item)" class="cursor-pointer-hover">
                                    <md-table-cell md-label="#">{{ index + trucks.from }}</md-table-cell>
                                    <md-table-cell md-label="">
                                        <div class="img-container">
                                            <img :src="item.truckModel.image" :alt="item.truckModel.brand + ' ' +item.truckModel.name" />
                                        </div>
                                    </md-table-cell>
                                    <md-table-cell :md-label="$t('truckModel.model')" class="td-name">{{ item.truckModel.brand }} {{ item.truckModel.name }}</md-table-cell>
                                    <md-table-cell :md-label="$t('truck.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('truck.property.garage')">{{ item.garage.garageModel.name }} - {{ item.garage.location.name }} ({{ item.garage.location.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('truck.relations.drivers')"><template v-if="item.drivers && item.drivers.length > 0">{{ drivers(item.drivers) }}</template><template v-else>{{ $t('truck.relations.no_drivers') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('truck.relations.trailer')"><template v-if="item.trailer">{{ item.trailer.trailerModel.name }}</template><template v-else>{{ $t('truck.relations.no_trailer') }}</template></md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: trucks.from, to: trucks.to, total: trucks.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="trucks.per_page"
                                        :total="trucks.total"></pagination>
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
    import { TRUCKS_QUERY, GARAGES_SELECT_QUERY } from '@/graphql/queries/user';
    import { TRUCK_MODELS_SELECT_QUERY, STATUSES_QUERY } from "@/graphql/queries/common";
    import { Pagination, SearchForm } from "@/components";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.trucks');
        },
        name: "Trucks",
        components: {
            Pagination,
            SearchForm
        },
        data() {
            return {
                trucks: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                truckModelsOptions: [],
                truckModels: [],
                garagesOptions: [],
                garages: [],
                statuses: [],
                statusesOptions: [],
                searchModel: {
                    status: [],
                    truckModel: [],
                    garage: [],
                },
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'status',
                                    label: this.$t('truck.property.status'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        translatableLabel: 'status.',
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'truckModel',
                                    label: this.$t('truck.relations.truckModel'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        groupBy: 'brand',
                                        optgroupLabel: (optgroup) => {
                                            return optgroup.brand;
                                        },
                                        optionLabel: (option) => {
                                            return option.brand + ' ' + option.name;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'garage',
                                    label: this.$t('truck.property.garage'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        groupBy: 'location.country.name',
                                        optgroupLabel: (optgroup) => {
                                            return optgroup.location.country.name;
                                        },
                                        optionLabel: (option) => {
                                            return option.garageModel.name + ' - ' + option.location.name;
                                        },
                                        multiple: true
                                    }
                                },
                            ],
                        },
                    ]
                },
            }
        },
        methods: {
            drivers(drivers) {
                let result = [];

                for (let driver of drivers) {
                    result.push(driver.first_name.charAt(0) + '. ' + driver.last_name)
                }

                return result.join(', ');
            },
            clickTableRow(item) {
                this.$router.push({
                    name: 'truck',
                    params: {id: item.id}
                });
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', (payLoad) => {
                if (payLoad.modelType === 'Truck') {
                    this.$apollo.queries.trucks.refresh();
                }
                if (payLoad.modelType === 'Garage') {
                    this.$apollo.queries.garages.refresh();
                }
            });
        },
        apollo: {
            trucks: {
                query: TRUCKS_QUERY,
                variables() {
                    return {page: this.page, limit: this.trucks.per_page, filter: this.filters}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            statuses: {
                query: STATUSES_QUERY,
                variables() {
                    return { model: 'truck'}
                },
                result({ data, loading, networkStatus }) {
                    this.statusesOptions = data.statuses;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.statusesOptions);
                    });
                }
            },
            truckModels: {
                query: TRUCK_MODELS_SELECT_QUERY,
                variables() {
                    return { page: 1, limit: -1}
                },
                result({ data, loading, networkStatus }) {
                    this.truckModelsOptions = data.truckModels.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.truckModelsOptions);
                    });
                }
            },
            garages: {
                query: GARAGES_SELECT_QUERY,
                variables() {
                    return { page: 1, limit: -1}
                },
                result({ data, loading, networkStatus }) {
                    this.garagesOptions = data.garages.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[2].config, 'options', this.garagesOptions);
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
