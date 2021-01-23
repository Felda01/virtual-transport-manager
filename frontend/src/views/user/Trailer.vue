<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.trailer.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
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
                            <md-card-actions md-alignment="right">
                                <template v-if="canDeleteTrailer">
                                    <md-button class="md-danger md-simple" @click="deleteTrailerModal"><md-icon>close</md-icon>{{ $t('detail.btn.sell') }}</md-button>
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
                                <h4 class="title">{{ $t('trailer.subNav.truck') }}</h4>
                            </md-card-header>
                            <md-card-content>
                                <md-table v-model="truckTable" v-if="truckTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'truck')" class="cursor-pointer-hover">
                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                        <md-table-cell md-label="">
                                            <div class="img-container">
                                                <img :src="item.truckModel.image" :alt="item.truckModel.brand + ' ' +item.truckModel.name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('truckModel.model')" class="td-name">{{ item.truckModel.brand }} {{ item.truckModel.name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                        <md-table-cell :md-label="$t('truck.relations.drivers')"><template v-if="item.drivers && item.drivers.length > 0">{{ drivers(findDrivers(item.drivers)) }}</template><template v-else>{{ $t('truck.relations.no_drivers') }}</template></md-table-cell>
                                        <md-table-cell :md-label="$t('truck.relations.trailer')"><template v-if="item.trailer">{{ findTrailer(item.trailer).trailerModel.name }}</template><template v-else>{{ $t('truck.relations.no_trailer') }}</template></md-table-cell>
                                    </md-table-row>
                                    <md-table-empty-state>
                                        {{ $t('trailer.relations.no_truck') }}
                                    </md-table-empty-state>
                                </md-table>
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
        </template>
    </div>
</template>

<script>
    import { TRAILER_QUERY } from '@/graphql/queries/user';
    import { DELETE_TRAILER_MUTATION } from '@/graphql/mutations/user';
    import { Tabs, ProductCard, MutationModal, DeleteModal } from "@/components";
    import constants from "../../constants";

    export default {
        title () {
            return this.$t('pages.trailer');
        },
        name: "Trailer",
        components: {
            Tabs,
            ProductCard,
            MutationModal,
            DeleteModal
        },
        computed: {
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
                return true;
                //return this.trailer ?  (this.truck.drivers.length === 0 || this.truck.drivers[0].location.id === this.truck.garage.location.id) : false;
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
            deleteTrailerModal() {

            },
            deleteTrailer(response) {

            },
        },
        apollo: {
            trailer: {
                query: TRAILER_QUERY,
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
