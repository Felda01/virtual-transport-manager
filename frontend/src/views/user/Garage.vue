<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.garage.loading && firstLoad">
            <content-placeholders class="md-layout-item md-medium-size-100 md-size-33">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-medium-size-100 md-size-66">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-medium-size-100 md-size-33">
                <div class="md-layout">
                    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-100">
                        <product-card header-animation="false" class="mb-4">
                            <img class="img" slot="imageHeader" :src="garage.garageModel.image" />
                            <h4 slot="title" class="title mt-2 mb-2">
                                {{ garage.garageModel.name }}
                            </h4>
                            <div slot="description" class="card-description">
                                <div class="md-layout md-alignment-center-space-between">
                                    <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.truck_count') }}</div>
                                    <div class="md-layout-item md-size-50 text-right">{{ garage.garageModel.truck_count }}</div>

                                    <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.trailer_count') }}</div>
                                    <div class="md-layout-item md-size-50 text-right">{{ garage.garageModel.trailer_count }}</div>

                                    <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.insurance') }}</div>
                                    <div class="md-layout-item md-size-50 text-right">{{ garage.garageModel.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.insuranceUnit') }}</div>

                                    <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.tax') }}</div>
                                    <div class="md-layout-item md-size-50 text-right">{{ garage.garageModel.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.taxUnit') }}</div>
                                </div>
                            </div>
                            <template slot="footer">
                                <md-button class="md-primary md-simple" @click="updateGarageModal"><md-icon>edit</md-icon>{{ $t('detail.btn.upgrade') }}</md-button>
                                <md-button class="md-danger md-simple" @click="deleteGarageModal"><md-icon>close</md-icon>{{ $t('detail.btn.sell') }}</md-button>
                            </template>
                        </product-card>
                    </div>
<!--                    <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-100">-->
<!--                        <chart-card-->
<!--                                header-animation="false"-->
<!--                                :chart-data="roundedLineChart.data"-->
<!--                                :chart-options="roundedLineChart.options"-->
<!--                                chart-type="Line"-->
<!--                                chart-inside-header-->
<!--                                no-footer-->
<!--                                background-color="green"-->
<!--                        >-->
<!--                            <template slot="content">-->
<!--                                <h4 class="title">Rounded Line Chart</h4>-->
<!--                                <p class="category">-->
<!--                                    Line Chart-->
<!--                                </p>-->
<!--                            </template>-->
<!--                        </chart-card>-->
<!--                    </div>-->
                </div>
            </div>
            <div class="md-layout-item md-size-66 mx-auto md-medium-size-100">
                <tabs
                        :tab-name="[$t('garage.subNav.drivers'), $t('garage.subNav.trucks'), $t('garage.subNav.trailers')]"
                        class="page-subcategories"
                        plain
                        :tab-content-center="true"
                        :tab-content-full-width="true"
                        color-button="success"
                >
                    <!-- here you can add your content for tab-content -->
                    <template slot="tab-pane-1">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('garage.subNav.drivers') }} {{ garage.drivers.length }} / {{ garage.garageModel.truck_count }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="garage.drivers" v-if="garage.drivers">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'driver')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container table-profile-image">
                                                <img :src="item.image" :alt="item.first_name + ' ' + item.last_name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.full_name')" class="td-name">{{ driver(item) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.relations.truck')"><template v-if="item.truck">{{ findTruck(item.truck.id).truckModel.brand }} {{ findTruck(item.truck.id).truckModel.name }}</template><template v-else>{{ $t('driver.relations.no_trailer') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('driver.relations.trailer')"><template v-if="item.truck && item.truck.trailer">{{ findTruck(item.truck.trailer.id).trailerModel.name }}</template><template v-else>{{ $t('driver.relations.no_trailer') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.adr')">{{ $t('ADRsShort.' + item.adr) }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-2">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('garage.subNav.trucks') }} {{ garage.trucks.length }} / {{ garage.garageModel.truck_count }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="garage.trucks" v-if="garage.trucks">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'truck')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container">
                                                <img :src="item.truckModel.image" :alt="item.truckModel.brand + ' ' +item.truckModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.model')" class="td-name">{{ item.truckModel.brand }} {{ item.truckModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.relations.drivers')"><template v-if="item.drivers && item.drivers.length > 0">{{ drivers(findDrivers(item.drivers)) }}</template><template v-else>{{ $t('truck.relations.no_driver') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('truck.relations.trailer')"><template v-if="item.trailer">{{ findTrailer(item.trailer).trailerModel.name }}</template><template v-else>{{ $t('truck.relations.no_trailer') }}</template></md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('garage.subNav.trailers') }} {{ garage.trailers.length }} / {{ garage.garageModel.trailer_count }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="garage.trailers" v-if="garage.trailers">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'trailer')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container">
                                                <img :src="item.trailerModel.image" :alt="item.trailerModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.relations.trailerModel')" class="td-name">{{ item.trailerModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.relations.truck')"><template v-if="item.truck">{{ findTruck(item.truck).truckModel.brand }} {{ findTruck(item.truck).truckModel.name }}</template><template v-else>{{ $t('trailer.relations.no_truck') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.property.adr')">{{ $t('ADRs.' + item.trailerModel.adr) }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-4">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">Help center</h4>
                                <p class="category">More information here</p>
                            </md-card-header>

                            <md-card-content>
                                Completely synergize resource taxing relationships via premier
                                niche markets. Professionally cultivate one-to-one customer
                                service with robust ideas. Dynamically innovate resource-leveling
                                customer service for state of the art customer service.
                            </md-card-content>
                        </md-card>
                    </template>
                </tabs>
            </div>
        </template>
    </div>
</template>

<script>
    import { GARAGE_QUERY } from '@/graphql/queries/user';
    import { Tabs, ProductCard, ChartCard } from "@/components";

    export default {
        title () {
            return this.$t('pages.garage');
        },
        name: "Garage",
        components: {
            Tabs,
            ProductCard,
            ChartCard
        },
        data() {
            return {
                garage: null,
                id: this.$route.params.id,
                firstLoad: true,
                roundedLineChart: {
                    data: {
                        labels: ["M", "T", "W", "T", "F", "S", "S"],
                        series: [[12, 17, 7, 17, 23, 18, 38]]
                    },
                    options: {
                        lineSmooth: this.$Chartist.Interpolation.cardinal({
                            tension: 10
                        }),
                        axisX: {
                            showGrid: false
                        },
                        low: 0,
                        high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
                        chartPadding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0
                        },
                        showPoint: false
                    }
                },
            }
        },
        methods: {
            clickTableRow(item, model) {
                this.$router.push({
                    name: model,
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
            findDrivers(ids) {
                if (this.garage && this.garage.drivers) {
                    return this.garage.drivers.filter(driver => ids.contains(driver.id));
                }
                return null;
            },
            findTruck(id) {
                if (this.garage && this.garage.trucks) {
                    return this.garage.trucks.filter(truck => truck.id === id);
                }
                return null;
            },
            findTrailer(id) {
                if (this.garage && this.garage.trailers) {
                    return this.garage.trailers.filter(trailer => trailer.id === id);
                }
                return null;
            },
            driver(driver) {
                return driver.first_name.charAt(0) + '. ' + driver.last_name
            },
            updateGarageModal() {

            },
            deleteGarageModal() {

            }
        },
        apollo: {
            garage: {
                query: GARAGE_QUERY,
                variables() {
                    return {id: this.id}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
        }
    }
</script>

<style scoped>

</style>
