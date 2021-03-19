<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.orders.loading && firstLoad">
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
            <template v-if="orders.data && orders.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="orders.data" v-if="orders && orders.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item)" class="cursor-pointer-hover">
                                    <md-table-cell md-label="#">{{ index + orders.from }}</md-table-cell>
                                    <md-table-cell md-label="">
                                        <div class="img-container">
                                            <img :src="item.market.cargo.image" :alt="item.market.cargo.name" />
                                        </div>
                                    </md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.cargo_name')" class="td-name">{{ item.market.cargo.name }}</md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.market_price')">{{ item.market.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('order.relations.market_priceUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.roadTrip_status')">{{ $t('status.' + item.roadTrip.status) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.roadTrip_arrival')">{{ item.roadTrip.arrival }}</md-table-cell>
                                    <md-table-cell :md-label="$t('market.property.expires_at')">{{ item.market.expires_at }}</md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.drivers')"><template v-if="item.drivers && item.drivers.length > 0">{{ drivers(item.drivers) }}</template><template v-else>{{ $t('order.relations.no_drivers') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.truck')"><template v-if="item.truck">{{ item.truck.truckModel.brand }} {{ item.truck.truckModel.name }}</template><template v-else>{{ $t('order.relations.no_truck') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.trailer')"><template v-if="item.trailer">{{ item.trailer.trailerModel.name }}</template><template v-else>{{ $t('order.relations.no_trailer') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.location_from')">{{ item.market.locationFrom.name }} ({{ item.market.locationFrom.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('order.relations.location_to')">{{ item.market.locationTo.name }} ({{ item.market.locationTo.country.short_name | uppercase }})</md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: orders.from, to: orders.to, total: orders.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="orders.per_page"
                                        :total="orders.total"></pagination>
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
    import { MutationModal, Pagination, SearchForm } from "@/components";
    import { ORDERS_QUERY } from "@/graphql/queries/user";
    import { STATUSES_QUERY } from "@/graphql/queries/common";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.orders');
        },
        name: "Orders",
        components: {
            MutationModal,
            Pagination,
            SearchForm
        },
        data() {
            return {
                orders: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                statusesOptions: [],
                statuses: [],
                searchModel: {
                    status: []
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
                                    label: this.$t('order.searchFields.status'),
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
                            ],
                        },
                    ],
                },
            }
        },
        methods: {
            clickTableRow(item) {
                this.$router.push({
                    name: 'order',
                    params: {id: item.id}
                });
            },
            drivers(drivers) {
                let result = [];

                for (let driver of drivers) {
                    result.push(driver.first_name.charAt(0) + '. ' + driver.last_name)
                }

                return result.join(', ');
            },
        },
        mounted() {
            EventBus.$on('refreshQuery', (payLoad) => {
                if (payLoad.modelType === 'Order') {
                    this.$apollo.queries.orders.refresh();
                }
            });
        },
        apollo: {
            orders: {
                query: ORDERS_QUERY,
                variables() {
                    return {page: this.page, limit: this.orders.per_page, filter: this.filters}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            statuses: {
                query: STATUSES_QUERY,
                variables() {
                    return { model: 'order'}
                },
                result({ data, loading, networkStatus }) {
                    this.statusesOptions = data.statuses;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.statusesOptions);
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
