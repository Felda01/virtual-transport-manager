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
                            <md-card-actions md-alignment="right" v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
                                <template v-if="canDeleteTruck">
                                    <md-button class="md-danger md-simple" @click="deleteTruckModal"><md-icon>close</md-icon>{{ $t('detail.btn.sell') }}</md-button>
                                </template>
                                <template v-else>
                                    <span>{{ $t('truck.can_not_sell') }}</span>
                                    <md-button class="md-primary md-simple md-just-icon md-round" @click="tooltipCanNotSell = !tooltipCanNotSell">
                                        <md-icon>help</md-icon>
                                        <md-tooltip :md-active.sync="tooltipCanNotSell" md-direction="left">{{ $t('truck.can_not_sell_info') }}</md-tooltip>
                                    </md-button>
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
                                <md-table v-model="truck.drivers" v-if="truck.drivers" :key="mdTableDrivers">
                                    <md-table-row v-if="truck.drivers && truck.drivers.length > 0" slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'driver')" class="cursor-pointer-hover">
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
                                        <md-table-cell md-label="" v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES) && hasPermission(constants.PERMISSION.MANAGE_DRIVERS)">
                                            <md-button class="md-danger md-simple md-full-text" @click.native.stop="unassignDriverFromTruckModal(item)"><md-icon>close</md-icon>{{ $t('detail.btn.unassign')}}</md-button>
                                        </md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('truck.relations.no_drivers') }}
                                    </md-table-empty-state>
                                </md-table>
                                <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES) && hasPermission(constants.PERMISSION.MANAGE_DRIVERS)">
                                    <div class="text-center mt-3" v-if="!truck.drivers || truck.drivers.length < 2">
                                        <template v-if="this.availableDriversInGarage && this.availableDriversInGarage.data && this.availableDriversInGarage.data.length > 0">
                                            <md-button class="md-success md-simple" @click="assignDriverToTruckModal"><md-icon>add</md-icon>{{ $t('detail.btn.assign') }}</md-button>
                                        </template>
                                        <template v-else>
                                            {{ $t('truck.relations.no_available_drivers') }}
                                        </template>
                                    </div>
                                </template>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('truck.subNav.trailer') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="trailerTable" v-if="trailerTable" :key="mdTableTrailer">
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
                                        <md-table-cell md-label="" v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
                                            <md-button class="md-danger md-simple md-full-text" @click.native.stop="unassignTrailerFromTruckModal(item)"><md-icon>close</md-icon>{{ $t('detail.btn.unassign')}}</md-button>
                                        </md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('truck.relations.no_trailer') }}
                                    </md-table-empty-state>
                                </md-table>
                                <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
                                    <div class="text-center mt-3" v-if="!truck.trailer">
                                        <template v-if="this.availableTrailersInGarage && this.availableTrailersInGarage.data && this.availableTrailersInGarage.data.length > 0">
                                            <md-button class="md-success md-simple" @click="assignTrailerToTruckModal"><md-icon>add</md-icon>{{ $t('detail.btn.assign') }}</md-button>
                                        </template>
                                        <template v-else>
                                            {{ $t('truck.relations.no_available_trailers') }}
                                        </template>
                                    </div>
                                </template>
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

        <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
            <!-- Assign trailer to truck modal-->
            <mutation-modal ref="assignTrailerToTruckModal" @ok="assignTrailerToTruck" :modalSchema="modalSchemaAssignTrailerToTruck" />

            <!-- Unassign trailer from truck modal-->
            <delete-modal ref="unassignTrailerFromTruckModal" @ok="unassignTrailerFromTruck" :modalSchema="modalSchemaUnassignTrailerFromTruck" />

            <!-- Delete truck modal-->
            <delete-modal ref="deleteTruckModal" @ok="deleteTruck" :modalSchema="modalSchemaDeleteTruck" />
        </template>

        <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES) && hasPermission(constants.PERMISSION.MANAGE_DRIVERS)">
            <!-- Assign driver to truck modal-->
            <mutation-modal ref="assignDriverToTruckModal" @ok="assignDriverToTruck" :modalSchema="modalSchemaAssignDriverToTruck" />

            <!-- Unassign driver from truck modal-->
            <delete-modal ref="unassignDriverFromTruckModal" @ok="unassignDriverFromTruck" :modalSchema="modalSchemaUnassignDriverFromTruck" />
        </template>
    </div>
</template>

<script>
    import { TRUCK_QUERY, AVAILABLE_DRIVERS_IN_GARAGE_QUERY, AVAILABLE_TRAILERS_IN_GARAGE_QUERY } from '@/graphql/queries/user';
    import { ASSIGN_DRIVER_TO_TRUCK_MUTATION, ASSIGN_TRAILER_TO_TRUCK_MUTATION, DELETE_TRUCK_MUTATION, UNASSIGN_DRIVER_FROM_TRUCK_MUTATION, UNASSIGN_TRAILER_FROM_TRUCK_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, ProductCard, ChartCard, MutationModal, DeleteModal } from "@/components";
    import constants from "../../constants";
    import { mapGetters } from "vuex";

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
            ...mapGetters([
                'hasPermission',
            ]),
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
                return this.truck ? constants.STATUS.IDLE === this.truck.status && this.truck.drivers.length === 0 && this.truck.trailer === null : false;
            }
        },
        data() {
            return {
                truck: null,
                id: this.$route.params.id,
                firstLoad: true,
                availableDriversInGarage: [],
                availableTrailersInGarage: [],
                modalSchemaDeleteTruck: {
                    message: '',
                    form: {
                        mutation: DELETE_TRUCK_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.sell'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUnassignDriverFromTruck: {
                    message: this.$t('model.modal.title.unassign.driverFromTruck'),
                    form: {
                        mutation: UNASSIGN_DRIVER_FROM_TRUCK_MUTATION,
                        hiddenFields: [],
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.unassign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUnassignTrailerFromTruck: {
                    message: this.$t('model.modal.title.unassign.trailerFromTruck'),
                    form: {
                        mutation: UNASSIGN_TRAILER_FROM_TRUCK_MUTATION,
                        hiddenFields: [],
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.unassign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaAssignDriverToTruck: {
                    form: {
                        mutation: ASSIGN_DRIVER_TO_TRUCK_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.assign.driverToTruck'),
                    okBtnTitle: this.$t('modal.btn.assign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaAssignTrailerToTruck: {
                    form: {
                        mutation: ASSIGN_TRAILER_TO_TRUCK_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.assign.trailerToTruck'),
                    okBtnTitle: this.$t('modal.btn.assign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                constants: constants,
                tooltipCanNotSell: false,
                mdTableDrivers: 0,
                mdTableTrailer: 0,
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
                let price = this.$options.filters.currency(this.truck.truckModel.price / 2, ' ', 2, { thousandsSeparator: ' ' });
                this.modalSchemaDeleteTruck.message = this.$t('model.modal.title.delete.truck', { price: price });

                this.modalSchemaDeleteTruck.form.idField = this.id;

                this.$refs['deleteTruckModal'].openModal();
            },
            deleteTruck(response) {
                let truck = response.data.deleteTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.truck', { modelName: truck.truckModel.brand + " " + truck.truckModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$router.push({
                    name: 'trucks',
                    params: {locale: this.$i18n.locale}
                });
            },
            assignDriverToTruckModal() {
                this.modalSchemaAssignDriverToTruck.form.fields = [
                    {
                        label: this.$t('truck.relations.drivers_from_same_garage'),
                        rules: 'required',
                        name: 'driver',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.availableDriversInGarage.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.first_name + " " + option.last_name;
                            }
                        }
                    },
                ];

                this.modalSchemaAssignDriverToTruck.form.hiddenFields = [
                    {
                        name: 'truck',
                        value: this.id
                    }
                ];

                this.$refs['assignDriverToTruckModal'].openModal();
            },
            assignDriverToTruck(response) {
                let driver = response.data.assignDriverToTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.assigned.driverToTruck', { modelName: driver.first_name + ' ' + driver.last_name, truck: driver.truck.truckModel.brand + " " + driver.truck.truckModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.truck.refresh();
                this.$apollo.queries.availableDriversInGarage.refresh();
            },
            assignTrailerToTruckModal() {
                this.modalSchemaAssignTrailerToTruck.form.fields = [
                    {
                        label: this.$t('truck.relations.trailers_from_same_garage'),
                        rules: 'required',
                        name: 'trailer',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.availableTrailersInGarage.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.trailerModel.name;
                            }
                        }
                    },
                ];

                this.modalSchemaAssignTrailerToTruck.form.hiddenFields = [
                    {
                        name: 'truck',
                        value: this.id
                    }
                ];

                this.$refs['assignTrailerToTruckModal'].openModal();
            },
            assignTrailerToTruck(response) {
                let trailer = response.data.assignTrailerToTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.assigned.trailerToTruck', { modelName: trailer.trailerModel.name, truck: trailer.truck.truckModel.brand + " " + trailer.truck.truckModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.truck.refresh();
                this.$apollo.queries.availableTrailersInGarage.refresh();
            },
            unassignDriverFromTruckModal(driver) {
                this.modalSchemaUnassignDriverFromTruck.form.hiddenFields = [
                    {
                        name: 'driver',
                        value: driver.id
                    },
                    {
                        name: 'truck',
                        value: this.id
                    }
                ];

                this.$refs['unassignDriverFromTruckModal'].openModal();
            },
            unassignDriverFromTruck(response) {
                let driver = response.data.unassignDriverFromTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.unassigned.driverFromTruck', { modelName: driver.first_name + " " + driver.last_name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.truck.refresh();
                this.$apollo.queries.availableDriversInGarage.refresh();
            },
            unassignTrailerFromTruckModal(trailer) {
                this.modalSchemaUnassignTrailerFromTruck.form.hiddenFields = [
                    {
                        name: 'trailer',
                        value: trailer.id
                    },
                    {
                        name: 'truck',
                        value: this.id
                    }
                ];

                this.$refs['unassignTrailerFromTruckModal'].openModal();
            },
            unassignTrailerFromTruck(response) {
                let trailer = response.data.unassignTrailerFromTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.unassigned.trailerFromTruck', { modelName: trailer.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.truck.refresh();
                this.$apollo.queries.availableTrailersInGarage.refresh();
            }
        },
        apollo: {
            truck: {
                query: TRUCK_QUERY,
                variables() {
                    return {id: this.id}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                    this.mdTableDrivers += 1;
                    this.mdTableTrailer += 1;
                }
            },
            availableDriversInGarage: {
                query: AVAILABLE_DRIVERS_IN_GARAGE_QUERY,
                variables() {
                    return {garage: this.truck.garage.id, limit: -1, page: 1}
                },
                skip () {
                    return !this.truck;
                },
            },
            availableTrailersInGarage: {
                query: AVAILABLE_TRAILERS_IN_GARAGE_QUERY,
                variables() {
                    return {garage: this.truck.garage.id, limit: -1, page: 1}
                },
                skip () {
                    return !this.truck;
                },
            }
        }
    }
</script>

<style scoped>

</style>
