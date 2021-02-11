<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.locations') }}</h4>
                        <md-button class="md-primary md-simple" @click="addLocationModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.locations.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="locations.data" v-if="locations && locations.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + locations.from }}</md-table-cell>
                                <md-table-cell :md-label="$t('location.property.name')">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('location.property.lat')">{{ item.lat }}</md-table-cell>
                                <md-table-cell :md-label="$t('location.property.lng')">{{ item.lng }}</md-table-cell>
                                <md-table-cell :md-label="$t('location.property.country')">{{ item.country.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateLocationModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteLocationModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: locations.from, to: locations.to, total: locations.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="locations.per_page"
                                :total="locations.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add location modal-->
        <mutation-modal ref="addLocationModal" @ok="addLocation" :modalSchema="modalSchemaAddLocation" :locales="locales" />

        <!-- Update location modal-->
        <mutation-modal ref="updateLocationModal" @ok="updateLocation" :modalSchema="modalSchemaUpdateLocation" :locales="locales" />

        <!-- Delete location modal-->
        <delete-modal ref="deleteLocationModal" @ok="deleteLocation" :modalSchema="modalSchemaDeleteLocation" />
    </div>
</template>

<script>
    import { LOCATIONS_QUERY, COUNTRIES_QUERY } from '@/graphql/queries/admin';
    import { CREATE_LOCATION_MUTATION, UPDATE_LOCATION_MUTATION, DELETE_LOCATION_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";
    import { LOCALES_QUERY } from "../../graphql/queries/common";

    export default {
        title () {
            return this.$t('pages.locations');
        },
        name: "Locations",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                locations: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                },
                countries: {
                    data: [],
                },
                locales: null,
                page: 1,
                modalSchemaAddLocation: {
                    form: {
                        mutation: CREATE_LOCATION_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.location'),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateLocation: {
                    form: {
                        mutation: UPDATE_LOCATION_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.location'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteLocation: {
                    message: this.$t('model.modal.title.delete.location'),
                    form: {
                        mutation: DELETE_LOCATION_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addLocationModal() {
                let translatableNameFields = [];
                for (let locale in this.locales) {
                    if (this.locales.hasOwnProperty(locale)) {
                        translatableNameFields.push({
                            label: this.$t('location.property.name'),
                            rules: 'required',
                            name: 'name_translations',
                            input: 'text',
                            type: 'text',
                            value: '',
                            config: {
                                translatable: true,
                                locale: this.locales[locale]
                            }
                        });
                    }
                }

                this.modalSchemaAddLocation.form.fields = [
                    ...translatableNameFields,
                    {
                        label: this.$t('location.property.is_city'),
                        rules: 'required',
                        name: 'is_city',
                        input: 'switch',
                        type: 'switch',
                        value: true,
                        config: {}
                    },
                    {
                        label: this.$t('location.property.lat'),
                        rules: 'required|latitude',
                        name: 'lat',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('location.property.lng'),
                        rules: 'required|longitude',
                        name: 'lng',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('location.property.country'),
                        rules: 'required',
                        name: 'country',
                        input: 'select',
                        type: 'select',
                        value: null,
                        config: {
                            options: this.countries.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.name;
                            }
                        }
                    },
                ];

                this.$refs['addLocationModal'].openModal();
            },
            addLocation(response) {
                let location = response.data.createLocation;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.location', { modelName: location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.locations.refresh();
            },
            updateLocationModal(location) {
                let translatableNameFields = [];
                let translatableNameValue = JSON.parse(location.name_translations);
                for (let locale in this.locales) {
                    if (this.locales.hasOwnProperty(locale)) {
                        translatableNameFields.push({
                            label: this.$t('location.property.name'),
                            rules: 'required',
                            name: 'name_translations',
                            input: 'text',
                            type: 'text',
                            value: translatableNameValue[this.locales[locale]],
                            config: {
                                translatable: true,
                                locale: this.locales[locale]
                            }
                        });
                    }
                }

                this.modalSchemaUpdateLocation.form.fields = [
                    ...translatableNameFields,
                    {
                        label: this.$t('location.property.is_city'),
                        rules: 'required',
                        name: 'is_city',
                        input: 'switch',
                        type: 'switch',
                        value: location.is_city,
                        config: {}
                    },
                    {
                        label: this.$t('location.property.lat'),
                        rules: 'required|latitude',
                        name: 'lat',
                        input: 'text',
                        type: 'text',
                        value: location.lat,
                        config: {}
                    },
                    {
                        label: this.$t('location.property.lng'),
                        rules: 'required|longitude',
                        name: 'lng',
                        input: 'text',
                        type: 'text',
                        value: location.lng,
                        config: {}
                    },
                    {
                        label: this.$t('location.property.country'),
                        rules: 'required',
                        name: 'country',
                        input: 'select',
                        type: 'select',
                        value: location.country.id,
                        config: {
                            options: this.countries.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.name;
                            }
                        }
                    },
                ];

                this.modalSchemaUpdateLocation.form.idField = location.id;

                this.$refs['updateLocationModal'].openModal();
            },
            updateLocation(response) {
                let location = response.data.updateLocation;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.location', { modelName: location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.locations.refresh();
            },
            deleteLocationModal(location) {
                this.modalSchemaDeleteLocation.form.idField = location.id;

                this.$refs['deleteLocationModal'].openModal();
            },
            deleteLocation(response) {
                let location = response.data.deleteLocation;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.location', { modelName: location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.locations.refresh();
            }
        },
        apollo: {
            locations: {
                query: LOCATIONS_QUERY,
                variables() {
                    return { page: this.page, limit: this.locations.per_page }
                }
            },
            locales: {
                query: LOCALES_QUERY,
            },
            countries: {
                query: COUNTRIES_QUERY,
                variables() {
                    return { page: 1, limit: -1 }
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
