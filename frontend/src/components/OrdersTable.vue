<template>
    <div class="orders">
        <md-card>
            <md-card-header>
                <h4 class="title">{{ $t('order.subNav.last_orders') }}</h4>
            </md-card-header>
            <md-card-content class="pb-0">
                <md-table v-model="orders" v-if="orders">
                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                        <md-table-cell md-label="">
                            <div class="img-container">
                                <img :src="item.market.cargo.image" :alt="item.market.cargo.name" />
                            </div>
                        </md-table-cell>
                        <md-table-cell :md-label="$t('order.relations.cargo_name')" class="td-name">{{ item.market.cargo.name }}</md-table-cell>
                        <md-table-cell :md-label="$t('order.relations.market_price')">{{ item.market.price }}</md-table-cell>
                        <md-table-cell :md-label="$t('order.relations.routeTrip_status')">{{ item.roadTrip.status }}</md-table-cell>
                        <md-table-cell v-if="canShowColumn('drivers')" :md-label="$t('order.relations.drivers')">{{ drivers(item.drivers) }}</md-table-cell>
                        <md-table-cell v-if="canShowColumn('truck')" :md-label="$t('order.relations.truck')">{{ item.truck.truckModel.brand }} {{ item.truck.truckModel.name }}</md-table-cell>
                        <md-table-cell v-if="canShowColumn('trailer')" :md-label="$t('order.relations.trailer')">{{ item.trailer.trailerModel.name }}</md-table-cell>
                        <md-table-cell :md-label="$t('order.relations.location_from')">{{ item.market.locationFrom.name }} ({{ item.market.locationFrom.country.short_name | uppercase }})</md-table-cell>
                        <md-table-cell :md-label="$t('order.relations.location_to')">{{ item.market.locationTo.name }} ({{ item.market.locationTo.country.short_name | uppercase }})</md-table-cell>
                    </md-table-row>
                    <md-table-empty-state>
                        {{ $t('order.no_orders') }}
                    </md-table-empty-state>
                </md-table>
            </md-card-content>
        </md-card>
    </div>
</template>

<script>
    export default {
        name: "OrdersTable",
        props: [
            'orders',
            'hiddenColumn'
        ],
        methods: {
            canShowColumn(column) {
                return this.hiddenColumn ? this.hiddenColumn === column : true;
            },
            drivers(drivers) {
                let result = [];

                for (let driver of drivers) {
                    result.push(driver.first_name.charAt(0) + '. ' + driver.last_name)
                }

                return result.join(', ');
            }
        }
    }
</script>

<style scoped>

</style>
