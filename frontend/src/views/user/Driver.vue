<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.driver.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <tabs
                        :tab-name="[$t('driver.subNav.info'), $t('driver.subNav.truck'), $t('driver.subNav.garage')]"
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
                                <h4 class="title">{{ $t('driver.subNav.info') }}</h4>
                            </md-card-header>
                            <md-card-content class="pb-0">
                                <md-table v-model="driverTable" v-if="driverTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                        <md-table-cell md-label="">
                                            <div class="img-container table-profile-image">
                                                <img :src="item.image" :alt="item.first_name + ' ' + item.last_name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.full_name')" class="td-name">{{ item.first_name }} {{ item.last_name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.relations.truck')"><template v-if="item.truck">{{ item.truck.truckModel.brand }} {{ item.truck.truckModel.name }}</template><template v-else>{{ $t('driver.relations.no_trailer') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('driver.relations.trailer')"><template v-if="item.truck && item.truck.trailer">{{ item.truck.trailer.trailerModel.name }}</template><template v-else>{{ $t('driver.relations.no_trailer') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.garage')">{{ item.garage.garageModel.name }} - {{ item.garage.location.name }} ({{ item.garage.location.country.short_name | uppercase }})</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.satisfaction')">{{ item.satisfaction }} {{ $t('driver.property.satisfactionUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.adr')">{{ $t('ADRsShort.' + item.adr) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.salary')">{{ item.salary | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('driver.property.salaryUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.preferred_road_trips')">{{ $t('preferred_road_trips.' + item.preferred_road_trips) }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                            <md-card-actions md-alignment="right">
                                <template v-if="canDeleteDriver">
                                    <md-button class="md-danger md-simple" @click="deleteDriverModal"><md-icon>close</md-icon>{{ $t('detail.btn.fire') }}</md-button>
                                </template>
                                <template v-else>
                                    <p>{{ $t('driver.can_not_fire') }}</p>
                                </template>
                            </md-card-actions>
                        </md-card>
                    </template>
                    <template slot="tab-pane-2">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('driver.subNav.truck') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="truckTable" v-if="truckTable" :key="mdTableTrucks">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'truck')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container">
                                                <img :src="item.truckModel.image" :alt="item.truckModel.brand + ' ' +item.truckModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.model')" class="td-name">{{ item.truckModel.brand }} {{ item.truckModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.relations.trailer')"><template v-if="item.trailer">{{ item.trailer.trailerModel.name }}</template><template v-else>{{ $t('truck.relations.no_trailer') }}</template></md-table-cell>
                                        <md-table-cell md-label="">
                                            <md-button class="md-danger md-simple md-full-text" @click.native.stop="unassignTruckFromDriverModal(item)"><md-icon>close</md-icon>{{ $t('detail.btn.unassign')}}</md-button>
                                        </md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('driver.relations.no_truck') }}
                                    </md-table-empty-state>
                                </md-table>
                                <div class="text-center mt-3" v-if="!driver.truck">
                                    <template v-if="this.availableTrucksInGarage && this.availableTrucksInGarage.data && this.availableTrucksInGarage.data.length > 0">
                                        <md-button class="md-success md-simple" @click="assignTruckToDriverModal"><md-icon>add</md-icon>{{ $t('detail.btn.assign') }}</md-button>
                                    </template>
                                    <template v-else>
                                        {{ $t('driver.relations.no_available_trucks') }}
                                    </template>
                                </div>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('driver.subNav.garage') }}</h4>
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

        <!-- Assign truck to driver modal-->
        <mutation-modal ref="assignTruckToDriverModal" @ok="assignTruckToDriver" :modalSchema="modalSchemaAssignTruckToDriver" />

        <!-- Unassign truck from driver modal-->
        <delete-modal ref="unassignTruckFromDriverModal" @ok="unassignTruckFromDriver" :modalSchema="modalSchemaUnassignTruckFromDriver" />
    </div>
</template>

<script>
    import { DRIVER_QUERY, AVAILABLE_TRUCKS_IN_GARAGE_QUERY } from '@/graphql/queries/user';
    import { DELETE_DRIVER_MUTATION, ASSIGN_DRIVER_TO_TRUCK_MUTATION, UNASSIGN_DRIVER_FROM_TRUCK_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, ProductCard, MutationModal, DeleteModal } from "@/components";
    import constants from "../../constants";

    export default {
        title () {
            return this.$t('pages.driver');
        },
        name: "Driver",
        components: {
            Tabs,
            ProductCard,
            MutationModal,
            DeleteModal
        },
        computed: {
            truckTable() {
                return this.driver && this.driver.truck ? [this.driver.truck] : [];
            },
            driverTable() {
                return this.driver ? [this.driver] : [];
            },
            garageTable() {
                return this.driver && this.driver.garage ? [this.driver.garage] : [];
            },
            canDeleteDriver() {
                return true;
                //return this.trailer ?  (this.truck.drivers.length === 0 || this.truck.drivers[0].location.id === this.truck.garage.location.id) : false;
            }
        },
        data() {
            return {
                driver: null,
                id: this.$route.params.id,
                firstLoad: true,
                availableTrucksInGarage: [],
                modalSchemaDeleteDriver: {
                    message: '',
                    form: {
                        mutation: DELETE_DRIVER_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.sell'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaAssignTruckToDriver: {
                    form: {
                        mutation: ASSIGN_DRIVER_TO_TRUCK_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.assign.truckToDriver'),
                    okBtnTitle: this.$t('modal.btn.assign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUnassignTruckFromDriver: {
                    message: this.$t('model.modal.title.unassign.truckFromDriver'),
                    form: {
                        mutation: UNASSIGN_DRIVER_FROM_TRUCK_MUTATION,
                        hiddenFields: [],
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.unassign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                mdTableTrucks: 0
            }
        },
        methods: {
            clickTableRow(item, model) {
                this.$router.push({
                    name: model,
                    params: {id: item.id}
                });
            },
            deleteDriverModal() {

            },
            deleteDriver(response) {

            },
            assignTruckToDriverModal() {
                this.modalSchemaAssignTruckToDriver.form.fields = [
                    {
                        label: this.$t('driver.relations.trucks_from_same_garage'),
                        rules: 'required',
                        name: 'truck',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.availableTrucksInGarage.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.truckModel.brand + " " + option.truckModel.name;
                            }
                        }
                    },
                ];

                this.modalSchemaAssignTruckToDriver.form.hiddenFields = [
                    {
                        name: 'driver',
                        value: this.id
                    }
                ];

                this.$refs['assignTruckToDriverModal'].openModal();
            },
            assignTruckToDriver(response) {
                let driver = response.data.assignDriverToTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.assigned.truckToDriver'),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.driver.refresh();
                this.$apollo.queries.availableTrucksInGarage.refresh();
            },
            unassignTruckFromDriverModal(truck) {
                this.modalSchemaUnassignTruckFromDriver.form.hiddenFields = [
                    {
                        name: 'truck',
                        value: truck.id
                    },
                    {
                        name: 'driver',
                        value: this.id
                    }
                ];

                this.$refs['unassignTruckFromDriverModal'].openModal();
            },
            unassignTruckFromDriver(response) {
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.unassigned.truckFromDriver'),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.driver.refresh();
                this.$apollo.queries.availableTrucksInGarage.refresh();
            }
        },
        apollo: {
            driver: {
                query: DRIVER_QUERY,
                variables() {
                    return {id: this.id}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                    this.mdTableTrucks += 1;
                }
            },
            availableTrucksInGarage: {
                query: AVAILABLE_TRUCKS_IN_GARAGE_QUERY,
                variables() {
                    return {garage: this.driver.garage.id, relation: 'driver', limit: -1, page: 1}
                },
                skip () {
                    return !this.driver;
                },
            }
        }
    }
</script>

<style scoped>

</style>
