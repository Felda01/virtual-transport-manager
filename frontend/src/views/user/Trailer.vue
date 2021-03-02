<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.trailer.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else-if="errorMessage">
            <div class="md-layout-item md-size-100">
                {{ errorMessage }}
            </div>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <tabs
                        :tab-name="[$t('trailer.subNav.info'), $t('trailer.subNav.truck'), $t('trailer.subNav.garage')]"
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
                                <h4 class="title">{{ $t('trailer.subNav.info') }}</h4>
                            </md-card-header>
                            <md-card-content class="pb-0">
                                <md-table v-model="trailerTable" v-if="trailerTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                        <md-table-cell md-label="">
                                            <div class="img-container table-detail-image">
                                                <img :src="item.trailerModel.image" :alt="item.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.name')" class="td-name">{{ item.trailerModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.type')">{{ item.trailerModel.type }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailer.relations.location')">
                                            <template v-if="item.truck && item.truck.drivers && item.truck.drivers.length > 0">{{ item.truck.drivers[0].location.name }} ({{ item.truck.drivers[0].location.country.short_name | uppercase }})</template>
                                            <template v-else>{{ item.garage.location.name }} ({{item.garage.location.country.short_name | uppercase }})</template>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.load')">{{ item.trailerModel.load | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.loadUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.adr')">{{ $t('ADRs.' + item.trailerModel.adr) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.km')">{{ item.km | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.kmUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.insurance')">{{ item.trailerModel.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.insuranceUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('trailerModel.property.tax')">{{ item.trailerModel.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.taxUnit') }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                            <md-card-actions md-alignment="right" v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
                                <template v-if="canDeleteTrailer">
                                    <md-button class="md-danger md-simple" @click="deleteTrailerModal"><md-icon>close</md-icon>{{ $t('detail.btn.sell') }}</md-button>
                                </template>
                                <div v-else class="vertical-center-flex">
                                    <span>{{ $t('trailer.can_not_sell') }}</span>
                                    <md-button class="md-primary md-simple md-just-icon md-round" @click="tooltipCanNotSell = !tooltipCanNotSell">
                                        <md-icon>help</md-icon>
                                        <md-tooltip :md-active.sync="tooltipCanNotSell" md-direction="left">{{ $t('trailer.can_not_sell_info') }}</md-tooltip>
                                    </md-button>
                                </div>
                            </md-card-actions>
                        </md-card>
                    </template>
                    <template slot="tab-pane-2">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('trailer.subNav.truck') }}</h4>
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
                                        <md-table-cell :md-label="$t('truck.relations.drivers')"><template v-if="item.drivers && item.drivers.length > 0">{{ driversString(item.drivers) }}</template><template v-else>{{ $t('truck.relations.no_drivers') }}</template></md-table-cell>
                                        <md-table-cell md-label="" v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
                                            <md-button class="md-danger md-simple md-full-text" @click.native.stop="unassignTruckFromTrailerModal(item)"><md-icon>close</md-icon>{{ $t('detail.btn.unassign')}}</md-button>
                                        </md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('trailer.relations.no_truck') }}
                                    </md-table-empty-state>
                                </md-table>
                                <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
                                    <div class="text-center mt-3" v-if="!trailer.truck">
                                        <template v-if="this.availableTrucksInGarage && this.availableTrucksInGarage.data && this.availableTrucksInGarage.data.length > 0">
                                            <md-button class="md-success md-simple" @click="assignTruckToTrailerModal"><md-icon>add</md-icon>{{ $t('detail.btn.assign') }}</md-button>
                                        </template>
                                        <template v-else>
                                            {{ $t('trailer.relations.no_available_trucks') }}
                                        </template>
                                    </div>
                                </template>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
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
            <div class="md-layout-item md-size-100">
                <orders-table v-if="trailer.lastOrders" :orders="trailer.lastOrders" />
            </div>
        </template>

        <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES)">
            <!-- Assign truck to trailer modal-->
            <mutation-modal ref="assignTruckToTrailerModal" @ok="assignTruckToTrailer" :modalSchema="modalSchemaAssignTruckToTrailer" />

            <!-- Unassign truck from trailer modal-->
            <delete-modal ref="unassignTruckFromTrailerModal" @ok="unassignTruckFromTrailer" :modalSchema="modalSchemaUnassignTruckFromTrailer" />

            <!-- Delete trailer modal-->
            <delete-modal ref="deleteTrailerModal" @ok="deleteTrailer" :modalSchema="modalSchemaDeleteTrailer" />
        </template>
    </div>
</template>

<script>
    import { TRAILER_QUERY, AVAILABLE_TRUCKS_IN_GARAGE_QUERY } from '@/graphql/queries/user';
    import { DELETE_TRAILER_MUTATION, ASSIGN_TRAILER_TO_TRUCK_MUTATION, UNASSIGN_TRAILER_FROM_TRUCK_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, ProductCard, MutationModal, DeleteModal, OrdersTable } from "@/components";
    import constants from "../../constants";
    import { mapGetters } from "vuex";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.trailer');
        },
        name: "Trailer",
        components: {
            Tabs,
            ProductCard,
            MutationModal,
            DeleteModal,
            OrdersTable
        },
        computed: {
            ...mapGetters([
                'hasPermission',
            ]),
            truckTable() {
                return this.trailer.truck ? [this.trailer.truck] : [];
            },
            trailerTable() {
                return this.trailer && this.trailer ? [this.trailer] : [];
            },
            garageTable() {
                return this.trailer && this.trailer.garage ? [this.trailer.garage] : [];
            },
            canDeleteTrailer() {
                return this.trailer ? this.trailer.status === constants.STATUS.IDLE : false;
            }
        },
        data() {
            return {
                trailer: null,
                id: this.$route.params.id,
                firstLoad: true,
                modalSchemaDeleteTrailer: {
                    message: '',
                    form: {
                        mutation: DELETE_TRAILER_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.sell'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaAssignTruckToTrailer: {
                    form: {
                        mutation: ASSIGN_TRAILER_TO_TRUCK_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.assign.truckToTrailer'),
                    okBtnTitle: this.$t('modal.btn.assign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUnassignTruckFromTrailer: {
                    message: this.$t('model.modal.title.unassign.truckFromTrailer'),
                    form: {
                        mutation: UNASSIGN_TRAILER_FROM_TRUCK_MUTATION,
                        hiddenFields: [],
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.unassign'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                constants: constants,
                mdTableTrucks: 0,
                tooltipCanNotSell: false
            }
        },
        methods: {
            clickTableRow(item, model) {
                this.$router.push({
                    name: model,
                    params: {id: item.id}
                });
            },
            driversString(drivers) {
                let result = [];

                for (let driver of drivers) {
                    result.push(driver.first_name.charAt(0) + '. ' + driver.last_name)
                }

                return result.join(', ');
            },
            deleteTrailerModal() {
                let price = this.$options.filters.currency(this.trailer.trailerModel.price / 2, ' ', 2, { thousandsSeparator: ' ' });
                this.modalSchemaDeleteTrailer.message = this.$t('model.modal.title.delete.trailer', { price: price });

                this.modalSchemaDeleteTrailer.form.idField = this.id;

                this.$refs['deleteTrailerModal'].openModal();
            },
            deleteTrailer(response) {
                let trailer = response.data.deleteTrailer;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.trailer', { modelName: trailer.trailerModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$router.push({
                    name: 'trailers',
                    params: {locale: this.$i18n.locale}
                });
            },
            assignTruckToTrailerModal() {
                this.modalSchemaAssignTruckToTrailer.form.fields = [
                    {
                        label: this.$t('trailer.relations.trucks_from_same_garage'),
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

                this.modalSchemaAssignTruckToTrailer.form.hiddenFields = [
                    {
                        name: 'trailer',
                        value: this.id
                    }
                ];

                this.$refs['assignTruckToTrailerModal'].openModal();
            },
            assignTruckToTrailer(response) {
                let trailer = response.data.assignTrailerToTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.assigned.truckToTrailer'),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.trailer.refresh();
                this.$apollo.queries.availableTrucksInGarage.refresh();
            },
            unassignTruckFromTrailerModal(truck) {
                this.modalSchemaUnassignTruckFromTrailer.form.hiddenFields = [
                    {
                        name: 'truck',
                        value: truck.id
                    },
                    {
                        name: 'trailer',
                        value: this.id
                    }
                ];

                this.$refs['unassignTruckFromTrailerModal'].openModal();
            },
            unassignTruckFromTrailer(response) {
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.unassigned.truckFromTrailer'),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.trailer.refresh();
                this.$apollo.queries.availableTrucksInGarage.refresh();
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', (payLoad) => {
                if (payLoad.modelType === 'Trailer' && payLoad.id === this.id) {
                    this.$apollo.queries.trailer.refresh();
                }
                if (payLoad.modelType === 'Truck') {
                    this.$apollo.queries.availableTrucksInGarage.refresh();
                }
            });
        },
        apollo: {
            trailer: {
                query: TRAILER_QUERY,
                variables() {
                    return {id: this.id}
                },
                error(error, vm, key, type, options) {
                    this.setErrorMessage(error);
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                    this.mdTableTrucks += 1;
                }
            },
            availableTrucksInGarage: {
                query: AVAILABLE_TRUCKS_IN_GARAGE_QUERY,
                variables() {
                    return {garage: this.trailer.garage.id, relation: 'trailer', limit: -1, page: 1}
                },
                skip () {
                    return !this.trailer;
                },
            }
        }
    }
</script>

<style scoped>

</style>
