<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.drivers.loading && firstLoad">
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
            <template v-if="drivers.data && drivers.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="drivers.data" v-if="drivers && drivers.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item)" class="cursor-pointer-hover">
                                    <md-table-cell md-label="#">{{ index + drivers.from }}</md-table-cell>
                                    <md-table-cell md-label="">
                                        <div class="img-container table-profile-image">
                                            <img :src="item.image" :alt="item.first_name + ' ' + item.last_name" />
                                        </div>
                                    </md-table-cell>
                                    <md-table-cell :md-label="$t('driver.property.full_name')" class="td-name">{{ driver(item) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('driver.property.status')"><template v-if="item.sleep">{{ $t('status.sleep') }}</template><template v-else>{{ $t('status.' + item.status) }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('driver.relations.truck')"><template v-if="item.truck">{{ item.truck.truckModel.brand }} {{ item.truck.truckModel.name }}</template><template v-else>{{ $t('driver.relations.no_truck') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('driver.relations.trailer')"><template v-if="item.truck && item.truck.trailer">{{ item.truck.trailer.trailerModel.name }}</template><template v-else>{{ $t('driver.relations.no_trailer') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('driver.property.garage')">{{ item.garage.garageModel.name }} - {{ item.garage.location.name }} ({{ item.garage.location.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('driver.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('driver.property.adr')">{{ $t('ADRsShort.' + item.adr) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('driver.property.salary')">{{ item.salary | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('driver.property.salaryUnit') }}</md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: drivers.from, to: drivers.to, total: drivers.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="drivers.per_page"
                                        :total="drivers.total"></pagination>
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
    import { DRIVERS_QUERY, GARAGES_SELECT_QUERY } from '@/graphql/queries/user';
    import { STATUSES_QUERY, ADRS_QUERY } from "@/graphql/queries/common";
    import { Pagination, SearchForm } from "@/components";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.drivers');
        },
        name: "Drivers",
        components: {
            Pagination,
            SearchForm
        },
        data() {
            return {
                drivers: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                ADRsOptions: [],
                ADRs: [],
                garagesOptions: [],
                garages: [],
                statuses: [],
                statusesOptions: [],
                searchModel: {
                    status: [],
                    adr: [],
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
                                    label: this.$t('driver.property.status'),
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
                                    name: 'adr',
                                    label: this.$t('driver.property.adr'),
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
                                    name: 'garage',
                                    label: this.$t('driver.property.garage'),
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
            clickTableRow(item) {
                this.$router.push({
                    name: 'driver',
                    params: {id: item.id}
                });
            },
            driver(driver) {
                return driver.first_name.charAt(0) + '. ' + driver.last_name
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', function (payLoad) {
                if (payLoad.modelType === 'Driver') {
                    this.$apollo.queries.drivers.refresh();
                }
                if (payLoad.modelType === 'Garage') {
                    this.$apollo.queries.garages.refresh();
                }
            });
        },
        apollo: {
            drivers: {
                query: DRIVERS_QUERY,
                variables() {
                    return {page: this.page, limit: this.drivers.per_page, filter: this.filters}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            statuses: {
                query: STATUSES_QUERY,
                variables() {
                    return { model: 'driver'}
                },
                result({ data, loading, networkStatus }) {
                    this.statusesOptions = data.statuses;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.statusesOptions);
                    });
                }
            },
            ADRs: {
                query: ADRS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.ADRsOptions = data.ADRs;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.ADRsOptions);
                    });
                },
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
