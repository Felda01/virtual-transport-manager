<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.markets.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100 mb-3">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <template v-if="markets.data && markets.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="markets.data" v-if="markets && markets.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item)" class="cursor-pointer-hover">
                                    <md-table-cell md-label="#">{{ index + markets.from }}</md-table-cell>
                                    <md-table-cell md-label="">
                                        <div class="img-container">
                                            <img :src="item.cargo.image" :alt="item.cargo.name" />
                                        </div>
                                    </md-table-cell>
                                    <md-table-cell :md-label="$t('cargo.property.name')" class="td-name">{{ item.cargo.name }}</md-table-cell>
                                    <md-table-cell :md-label="$t('market.property.price')">{{ item.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('market.property.priceUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('market.property.expires_at')">{{ item.expires_at }}</md-table-cell>
                                    <md-table-cell :md-label="$t('market.property.amount')">{{ item.amount | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('market.property.amountUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('market.relations.location_from')">{{ item.locationFrom.name }} ({{ item.locationFrom.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('market.relations.location_to')">{{ item.locationTo.name }} ({{ item.locationTo.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('cargo.property.adr')">{{ $t('ADRs.' + item.cargo.adr) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('cargo.property.weight')">{{ item.cargo.weight | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('cargo.property.weightUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('cargo.property.engine_power')">{{ item.cargo.engine_power }}  {{ $t('cargo.property.engine_powerUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('cargo.property.chassis')">{{ item.cargo.chassis }}</md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: markets.from, to: markets.to, total: markets.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="markets.per_page"
                                        :total="markets.total"></pagination>
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
    import { MutationModal, Pagination, DeleteModal, SearchForm } from "@/components";
    import { ADRS_QUERY, CHASSIS_QUERY, TRAILER_MODELS_SELECT_QUERY } from "@/graphql/queries/common";
    import { MARKETS_QUERY, READY_DRIVERS_SELECT_QUERY } from "@/graphql/queries/user";
    import constants from "../../constants";

    export default {
        title () {
            return this.$t('pages.orderOffers');
        },
        name: "Markets",
        components: {
            MutationModal,
            Pagination,
            DeleteModal,
            SearchForm
        },
        data() {
            return {
                markets: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                chassisOptions: [],
                driversOptions: [],
                trailerModelsOptions: [],
                searchModel: {
                    chassis: [],
                    adr: [],
                    engine_power: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    weight: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    trailers: [],
                    driver: '',
                    sort: ''
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
                                    name: 'adr',
                                    label: this.$t('market.searchFields.adr'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        translatableLabel: 'ADRs.',
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
                                    name: 'chassis',
                                    label: this.$t('market.searchFields.chassis'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'engine_power',
                                    labelFrom: this.$t('market.searchFields.engine_power') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('market.searchFields.engine_power') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'weight',
                                    labelFrom: this.$t('market.searchFields.weight') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('market.searchFields.weight') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'trailers',
                                    label: this.$t('market.searchFields.trailers'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.name;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'driver',
                                    label: this.$t('market.searchFields.driver'),
                                    value: '',
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.first_name + " " + option.last_name + " - " + option.location.name + "(" + option.location.country.short_name.toUpperCase() + ")";
                                        },
                                        emptyOption: this.$t('market.searchFields.no_drivers_option')
                                    }
                                },
                            ],
                        },
                        {
                            class: ['mt-2'],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'sort',
                                    label: this.$t('search.sortBy'),
                                    value: '',
                                    config: {
                                        options: [
                                            { id: 'price_asc', name: this.$t('market.searchFields.price_asc') },
                                            { id: 'price_desc', name: this.$t('market.searchFields.price_desc') },
                                            { id: 'km_asc', name: this.$t('market.searchFields.km_asc') },
                                            { id: 'km_desc', name: this.$t('market.searchFields.km_desc') },
                                        ],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.name;
                                        },
                                    }
                                },
                            ]
                        }
                    ],
                },
            }
        },
        methods: {
            clickTableRow(item) {

            }
        },
        apollo: {
            markets: {
                query: MARKETS_QUERY,
                variables() {
                    return { page: this.page, limit: this.markets.per_page, filter: this.filters }
                },
                result({ data, loading, networkStatus }) {
                    this.firstLoad = false;
                }
            },
            chassis: {
                query: CHASSIS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.chassisOptions = data.chassis;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.chassisOptions);
                    });
                },
            },
            ADRs: {
                query: ADRS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.ADRsOptions = data.ADRs;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.ADRsOptions);
                    });
                },
            },
            trailerModels: {
                query: TRAILER_MODELS_SELECT_QUERY,
                variables() {
                    return { page: 1, limit: -1 }
                },
                result({ data, loading, networkStatus }) {
                    this.trailerModelsOptions = data.trailerModels.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[4].config, 'options', this.trailerModelsOptions);
                    });
                },
            },
            drivers: {
                query: READY_DRIVERS_SELECT_QUERY,
                variables() {
                    return { page: 1, limit: -1, filter: [{ column: 'status', value: constants.STATUS.READY + "," + constants.STATUS.SLEEP}] }
                },
                result({ data, loading, networkStatus }) {
                    this.driversOptions = data.drivers.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[5].config, 'options', this.driversOptions);
                    });
                },
            }
        }
    }
</script>

<style scoped>

</style>