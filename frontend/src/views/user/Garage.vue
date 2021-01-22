<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.garage.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <tabs
                        :tab-name="[$t('garage.subNav.info'), $t('garage.subNav.drivers'), $t('garage.subNav.trucks'), $t('garage.subNav.trailers')]"
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
                                <h4 class="title">{{ $t('garage.subNav.info') }}</h4>
                            </md-card-header>
                            <md-card-content class="pb-0">
                                <md-table v-model="garageTable" v-if="garage">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                        <md-table-cell md-label="">
                                            <div class="img-container table-detail-image">
                                                <img :src="item.garageModel.image" :alt="item.garageModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('garageModel.property.name')" class="td-name">{{ item.garageModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('garage.property.drivers')">{{ item.drivers.length }} / {{ item.garageModel.truck_count }}</md-table-cell>
                                        <md-table-cell :md-label="$t('garage.property.trucks')">{{ item.trucks.length }} / {{ item.garageModel.truck_count }}</md-table-cell>
                                        <md-table-cell :md-label="$t('garage.property.trailers')">{{ item.trailers.length }} / {{ item.garageModel.trailer_count }}</md-table-cell>
                                        <md-table-cell :md-label="$t('garage.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                            <md-card-actions md-alignment="space-between">
                                <template v-if="canUpdateGarage">
                                    <md-button class="md-primary md-simple" @click="updateGarageModal"><md-icon>edit</md-icon>{{ $t('detail.btn.upgrade') }}</md-button>
                                </template>
                                <template v-else>
                                    <p>{{ $t('garage.no_upgrades') }}</p>
                                </template>

                                <template v-if="canDeleteGarage">
                                    <md-button class="md-danger md-simple" @click="deleteGarageModal"><md-icon>close</md-icon>{{ $t('detail.btn.sell') }}</md-button>
                                </template>
                                <template v-else>
                                    <p>{{ $t('garage.can_not_sell') }}</p>
                                </template>
                            </md-card-actions>
                        </md-card>
                    </template>
                    <template slot="tab-pane-2">
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
                                    <md-table-empty-state>
                                        {{ $t('garage.relations.no_drivers') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
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
                                    <md-table-empty-state>
                                        {{ $t('garage.relations.no_trucks') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-4">
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
                                    <md-table-empty-state>
                                        {{ $t('garage.relations.no_trailers') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                </tabs>
            </div>
        </template>
        <!-- Update garage modal-->
        <mutation-modal ref="updateGarageModal" @ok="updateGarage" :modalSchema="modalSchemaUpdateGarage" />

        <!-- Delete garage modal-->
        <delete-modal ref="deleteGarageModal" @ok="deleteGarage" :modalSchema="modalSchemaDeleteGarage" />
    </div>
</template>

<script>
    import { GARAGE_QUERY, AVAILABLE_GARAGE_MODEL_UPGRADES_QUERY } from '@/graphql/queries/user';
    import { UPDATE_GARAGE_MUTATION, DELETE_GARAGE_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, ProductCard, ChartCard, MutationModal, DeleteModal } from "@/components";

    export default {
        title () {
            return this.$t('pages.garage');
        },
        name: "Garage",
        components: {
            Tabs,
            ProductCard,
            ChartCard,
            MutationModal,
            DeleteModal
        },
        computed: {
            canUpdateGarage() {
                return this.availableGarageModelUpgrades && this.availableGarageModelUpgrades.data && this.availableGarageModelUpgrades.data.length > 0;
            },
            canDeleteGarage() {
                return this.garage ? this.garage.drivers.length === 0 && this.garage.trucks.length === 0 && this.garage.trailers.length === 0 : false;
            },
            garageSellPrice() {
                return this.garage ? this.garage.garageModel.price / 2 : 0;
            },
            garageTable() {
                return this.garage ? [this.garage] : [];
            }
        },
        data() {
            return {
                garage: null,
                id: this.$route.params.id,
                firstLoad: true,
                availableGarageModelUpgrades: [],
                modalSchemaUpdateGarage: {
                    form: {
                        mutation: UPDATE_GARAGE_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.garage'),
                    okBtnTitle: this.$t('modal.btn.upgrade'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteGarage: {
                    message: '',
                    form: {
                        mutation: DELETE_GARAGE_MUTATION,
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
                this.modalSchemaUpdateGarage.form.fields = [
                    {
                        label: this.$t('garage.property.garage_model'),
                        rules: 'required',
                        name: 'garage_model',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.availableGarageModelUpgrades.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                let price = option.price - this.garage.garageModel.price;
                                return option.name
                                    + " (" + this.$t('garageModel.selectOptions.slots', {trucks: option.truck_count, trailers: option.trailer_count}) + ")"
                                    + " - " +  this.$options.filters.currency(price, ' ', 2, { thousandsSeparator: ' ' }) + " €";
                            }
                        }
                    },
                ];

                this.modalSchemaUpdateGarage.form.idField = this.id;

                this.$refs['updateGarageModal'].openModal();
            },
            updateGarage(response) {
                let garage = response.data.updateGarage;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.garage', { modelName: garage.garageModel.name, location: garage.location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.garage.refresh();
            },
            deleteGarageModal() {
                this.modalSchemaDeleteGarage.form.idField = this.id;

                this.$refs['deleteGarageModal'].openModal();
            },
            deleteGarage(response) {
                let garage = response.data.deleteGarage;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.garageModel', { modelName: garage.garageModel.name, location: garage.location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$router.push({
                    name: 'garages',
                    params: {locale: this.$i18n.locale}
                });
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
                    let price = this.$options.filters.currency(data.garage.garageModel.price / 2, ' ', 2, { thousandsSeparator: ' ' })
                    this.modalSchemaDeleteGarage.message = this.$t('model.modal.title.delete.garage', {price: price});
                }
            },
            availableGarageModelUpgrades: {
                query: AVAILABLE_GARAGE_MODEL_UPGRADES_QUERY,
                variables() {
                    return {id: this.id, page: 1, limit: -1}
                }
            }
        }
    }
</script>

<style scoped>

</style>
