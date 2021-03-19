<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.order.loading && firstLoad">
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
            <div class="md-layout-item md-size-100" v-if="hasPermission(constants.PERMISSION.MANAGE_JOBS) && order && order.roadTrip && order.roadTrip.status === constants.STATUS.WAITING_FOR_DRIVERS">
                <simple-wizard ref="formWizard" :prevButtonText="$t('order.form.prevButtonText')" :nextButtonText="$t('order.form.nextButtonText')" :finishButtonText="$t('order.form.finishButtonText')">
                    <template slot="header">
                        <h3 class="title">{{ $t('order.form.title') }}</h3>
                        <h5 class="category">{{ $t('order.form.subtitle') }}</h5>
                    </template>

                    <wizard-tab :before-change="() => validateStep('step1')">
                        <template slot="label">
                            {{ $t('order.form.drivers') }}
                        </template>
                        <first-step ref="step1" v-model="form" :options="trucksForOrder.data" :location-from="order.market.locationFrom" :location-to="order.market.locationTo"></first-step>
                    </wizard-tab>

                    <wizard-tab :before-change="() => validateStep('step2')">
                        <template slot="label">
                            {{ $t('order.form.path') }}
                        </template>
                        <second-step ref="step2" v-model="form" :options-truck="trucksForOrder.data" :options-path="pathsForOrder" :location-from="order.market.locationFrom" :location-to="order.market.locationTo"></second-step>
                    </wizard-tab>

                    <wizard-tab :before-change="() => validateStep('step3')">
                        <template slot="label">
                            {{ $t('order.form.summary') }}
                        </template>
                        <third-step ref="step3" v-model="form" :location-from="order.market.locationFrom" :location-to="order.market.locationTo" :options-truck="trucksForOrder.data" :options-path="pathsForOrder" @on-validated="wizardComplete"></third-step>
                    </wizard-tab>
                </simple-wizard>
            </div>
            <div class="md-layout-item md-size-100">
                <tabs
                        :tab-name="[$t('order.subNav.info'), $t('order.subNav.drivers'), $t('order.subNav.truck'), $t('order.subNav.trailer') ]"
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
                                <h4 class="title">{{ $t('order.subNav.info') }}</h4>
                            </md-card-header>
                            <md-card-content class="pb-0">
                                <md-table v-model="orderTable" v-if="orderTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
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
                                        <md-table-cell :md-label="$t('order.relations.location_from')">{{ item.market.locationFrom.name }} ({{ item.market.locationFrom.country.short_name | uppercase }})</md-table-cell>
                                        <md-table-cell :md-label="$t('order.relations.location_to')">{{ item.market.locationTo.name }} ({{ item.market.locationTo.country.short_name | uppercase }})</md-table-cell>
                                        <md-table-cell :md-label="$t('order.relations.customer_from')">{{ item.market.customerFrom.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('order.relations.customer_to')">{{ item.market.customerTo.name }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-2">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('order.subNav.drivers') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="order.drivers" v-if="order.drivers" :key="mdTableDrivers">
                                    <md-table-row v-if="order.drivers && order.drivers.length > 0" slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'driver')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container table-profile-image">
                                                <img :src="item.image" :alt="item.first_name + ' ' + item.last_name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.full_name')" class="td-name">{{ driverName(item) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.status')"><template v-if="item.sleep">{{ $t('status.sleep') }}</template><template v-else>{{ $t('status.' + item.status) }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('driver.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('order.relations.no_drivers') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-3">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('order.subNav.truck') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="truckTable" v-if="truckTable" :key="mdTableTruck">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'truck')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container">
                                                <img :src="item.truckModel.image" :alt="item.truckModel.brand + ' ' +item.truckModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.model')" class="td-name">{{ item.truckModel.brand }} {{ item.truckModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('driver.relations.no_truck') }}
                                    </md-table-empty-state>
                                </md-table>
                            </md-card-content>
                        </md-card>
                    </template>
                    <template slot="tab-pane-4">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('order.subNav.trailer') }}</h4>
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
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('truck.relations.no_trailer') }}
                                    </md-table-empty-state>
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
    import { ORDER_QUERY, TRUCKS_FOR_ORDER_QUERY, PATHS_FOR_ORDER } from '@/graphql/queries/user';
    import { UPDATE_ORDER_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, MutationModal, DeleteModal, SimpleWizard, WizardTab } from "@/components";
    import constants from "../../constants";
    import { mapGetters } from "vuex";
    import FirstStep from "./Order/FirstStep";
    import SecondStep from "./Order/SecondStep";
    import ThirdStep from "./Order/ThirdStep";
    import OrdersTable from "../../components/OrdersTable";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.order');
        },
        name: "Order",
        components: {
            OrdersTable,
            Tabs,
            MutationModal,
            DeleteModal,
            SimpleWizard,
            WizardTab,
            FirstStep,
            SecondStep,
            ThirdStep,
        },
        computed: {
            ...mapGetters([
                'hasPermission',
            ]),
            truckTable() {
                return this.order && this.order.truck ? [this.order.truck] : [];
            },
            trailerTable() {
                return this.order && this.order.trailer ? [this.order.trailer] : [];
            },
            orderTable() {
                return this.order ? [this.order] : [];
            },
        },
        data() {
            return {
                order: null,
                id: this.$route.params.id,
                firstLoad: true,
                constants: constants,
                mdTableDrivers: 0,
                mdTableTruck: 0,
                mdTableTrailer: 0,
                form: {
                    truck: '',
                    path: '',
                    id: '',
                },
                trucksForOrder: {
                    data: []
                },
                pathsForOrder: []
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
            driverName(driver) {
                return driver.first_name.charAt(0) + '. ' + driver.last_name
            },
            validateStep(ref) {
                return this.$refs[ref].validate();
            },
            wizardComplete() {
                this.form.id = this.id;
                this.$apollo.mutate({
                    mutation: UPDATE_ORDER_MUTATION,
                    variables: this.form
                }).then(response => {
                    let order = response.data.updateOrder;
                    let price = this.$options.filters.currency(order.market.price, ' ', 2, { thousandsSeparator: ' ' }) + ' €';
                    this.$notify({
                        timeout: 5000,
                        message: this.$t('model.response.success.updated.order', {cargoName: order.market.cargo.name, locationFromName: order.market.locationFrom.name, locationToName: order.market.locationTo.name, price: price}),
                        icon: "add_alert",
                        horizontalAlign: 'right',
                        verticalAlign: 'top',
                        type: 'success'
                    });

                    this.$apollo.queries.order.refresh();
                }).catch(error => {
                    let errors = {};
                    if (error.graphQLErrors && error.graphQLErrors[0]) {
                        if (error.graphQLErrors[0].extensions && error.graphQLErrors[0].extensions.validation) {
                            errors = error.graphQLErrors[0].extensions.validation;
                        } else if (error.graphQLErrors[0].message) {
                            errors = {'General': error.graphQLErrors[0].message};
                        }
                    } else if (error.errors && error.errors[0]) {
                        errors = error.errors[0].extensions.validation;
                    }

                    if (errors['truck']) {
                        errors[this.$t('order.form.firstStep.truck.label')] = errors['truck'];
                        errors[this.$t('order.form.secondStep.path.label')] = errors['path'];
                        this.$refs['step1'].setErrors(errors);
                        this.$refs['step2'].setErrors(errors);
                        this.$refs['formWizard'].navigateToTab(0);
                    } else if (errors['path']) {
                        errors[this.$t('order.form.secondStep.path.label')] = errors['path'];
                        this.$refs['step2'].setErrors(errors);
                        this.$refs['formWizard'].navigateToTab(1);
                    }
                });
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', (payLoad) => {
                if (payLoad.modelType === 'Order' && payLoad.id === this.id) {
                    this.$apollo.queries.order.refresh();
                }
                if (['Driver', 'Truck', 'Trailer'].includes(payLoad.modelType)) {
                    this.$apollo.queries.trucksForOrder.refresh();
                }
            });
        },
        apollo: {
            order: {
                query: ORDER_QUERY,
                variables() {
                    return {id: this.id}
                },
                error(error, vm, key, type, options) {
                    this.setErrorMessage(error);
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                    this.mdTableDrivers += 1;
                    this.mdTableTruck += 1;
                    this.mdTableTrailer += 1;
                }
            },
            trucksForOrder: {
                query: TRUCKS_FOR_ORDER_QUERY,
                variables() {
                    return {order: this.id, page: 1, limit: -1}
                },
                skip () {
                    return !this.order;
                },
            },
            pathsForOrder: {
                query: PATHS_FOR_ORDER,
                variables() {
                    return {order: this.id, truck: this.form.truck}
                },
                result({data, loading, networkStatus}) {
                    if (data.pathsForOrder && data.pathsForOrder.length > 0) {
                        this.$nextTick( () => {
                            this.$set(this.form, 'path', 1);
                        });
                    }
                },
                skip () {
                    return !this.order;
                },
            }
        }
    }
</script>

<style scoped>

</style>
