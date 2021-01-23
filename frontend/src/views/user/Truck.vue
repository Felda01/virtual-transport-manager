<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.truck.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <tabs
                        :tab-name="[$t('truck.subNav.info'), $t('truck.subNav.drivers'), $t('truck.subNav.trailer'), $t('truck.subNav.garage')]"
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
                                <h4 class="title">{{ $t('truck.subNav.info') }}</h4>
                            </md-card-header>
                            <md-card-content class="pb-0">
                                <md-table v-model="truckTable" v-if="truckTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                        <md-table-cell md-label="">
                                            <div class="img-container table-detail-image">
                                                <img :src="item.truckModel.image" :alt="item.truckModel.brand + ' ' +item.truckModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.model')" class="td-name">{{ item.truckModel.brand }} {{ item.truckModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.relations.location')">
                                            <template v-if="item.drivers && item.drivers.length > 0">{{ item.drivers[0].location.name }} ({{ item.drivers[0].location.country.short_name | uppercase }})</template>
                                            <template v-else>{{ item.garage.location.name }} ({{item.garage.location.country.short_name | uppercase }})</template>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.engine_power')">{{ item.truckModel.engine_power }} {{ $t('truckModel.property.engine_powerUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.chassis')">{{ item.truckModel.chassis }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.load')">{{ item.truckModelload | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.loadUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.emission_class')">{{ $t('truckEmissionClasses.' + item.truckModel.emission_class) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.km')">{{ item.km | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.kmUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.insurance')">{{ item.truckModel.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.insuranceUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.property.tax')">{{ item.truckModel.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.taxUnit') }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                            <md-card-actions md-alignment="right">
                                <template v-if="canDeleteTruck">
                                    <md-button class="md-danger md-simple" @click="deleteTruckModal"><md-icon>close</md-icon>{{ $t('detail.btn.sell') }}</md-button>
                                </template>
                                <template v-else>
                                    <p>{{ $t('truck.can_not_sell') }}</p>
                                </template>
                            </md-card-actions>
                        </md-card>
                    </template>
                    <template slot="tab-pane-2">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('truck.subNav.drivers') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="truck.drivers" v-if="truck.drivers">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'driver')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container table-profile-image">
                                                <img :src="item.image" :alt="item.first_name + ' ' + item.last_name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.full_name')" class="td-name">{{ driver(item) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.adr')">{{ $t('ADRsShort.' + item.adr) }}</md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('truck.relations.no_drivers') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('truck.subNav.trailer') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="trailerTable" v-if="trailerTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'trailer')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container">
                                                <img :src="item.trailerModel.image" :alt="item.trailerModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.relations.trailerModel')" class="td-name">{{ item.trailerModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.property.adr')">{{ $t('ADRs.' + item.trailerModel.adr) }}</md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('truck.relations.no_trailer') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-4">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('truck.subNav.garage') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="garageTable" v-if="garageTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'garage')" class="cursor-pointer-hover">
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
                        </md-card>
                    </template>
                </tabs>
            </div>
        </template>
    </div>
</template>

<script>
    import { TRUCK_QUERY } from '@/graphql/queries/user';
    import { UPDATE_TRUCK_MUTATION, DELETE_TRUCK_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, ProductCard, ChartCard, MutationModal, DeleteModal } from "@/components";
    import constants from "../../constants";

    export default {
        title () {
            return this.$t('pages.truck');
        },
        name: "Truck",
        components: {
            Tabs,
            ProductCard,
            ChartCard,
            MutationModal,
            DeleteModal
        },
        computed: {
            truckTable() {
                return this.truck ? [this.truck] : [];
            },
            trailerTable() {
                return this.truck && this.truck.trailer ? [this.truck.trailer] : [];
            },
            garageTable() {
                return this.truck && this.truck.garage ? [this.truck.garage] : [];
            },
            canDeleteTruck() {
                return this.truck ? [constants.STATUS.ASSIGNED, constants.STATUS.AVAILABLE].includes(this.truck.status) && (this.truck.drivers.length === 0 || this.truck.drivers[0].location.id === this.truck.garage.location.id) : false;
            }
        },
        data() {
            return {
                truck: null,
                id: this.$route.params.id,
                firstLoad: true,
                modalSchemaDeleteTruck: {
                    message: '',
                    form: {
                        mutation: DELETE_TRUCK_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.sell'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
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
            driver(driver) {
                return driver.first_name.charAt(0) + '. ' + driver.last_name
            },
            deleteTruckModal() {

            },
            deleteTruck(response) {

            },
        },
        apollo: {
            truck: {
                query: TRUCK_QUERY,
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
