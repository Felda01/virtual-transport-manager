<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.countries') }}</h4>
                        <md-button class="md-primary md-simple" @click="addCountryModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.countries.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="countries.data" v-if="countries && countries.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + countries.from }}</md-table-cell>
                                <md-table-cell :md-label="$t('country.property.name')">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('country.property.short_name')">{{ item.short_name }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')" class="text-right">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateCountryModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteCountryModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: countries.from, to: countries.to, total: countries.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                            v-model="page"
                            :per-page="countries.per_page"
                            :total="countries.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add country modal-->
        <mutation-modal ref="addCountryModal" @ok="addCountry" :modalSchema="modalSchemaAddCountry" :locales="locales" />

        <!-- Update country modal-->
        <mutation-modal ref="updateCountryModal" @ok="updateCountry" :modalSchema="modalSchemaUpdateCountry" :locales="locales" />

        <!-- Delete country modal-->
        <delete-modal ref="deleteCountryModal" @ok="deleteCountry" :modalSchema="modalSchemaDeleteCountry" />
    </div>
</template>

<script>
    import { COUNTRIES_QUERY } from '@/graphql/queries/admin';
    import { CREATE_COUNTRY_MUTATION, UPDATE_COUNTRY_MUTATION, DELETE_COUNTRY_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";
    import { LOCALES_QUERY } from "../../graphql/queries/common";

    export default {
        title () {
            return this.$t('pages.countries');
        },
        name: "Countries",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                countries: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                },
                locales: null,
                page: 1,
                modalSchemaAddCountry: {
                    form: {
                        mutation: CREATE_COUNTRY_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.country'),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateCountry: {
                    form: {
                        mutation: UPDATE_COUNTRY_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.country'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteCountry: {
                    message: this.$t('model.modal.title.delete.country'),
                    form: {
                        mutation: DELETE_COUNTRY_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addCountryModal() {
                let translatableNameFields = [];
                for (let locale in this.locales) {
                    if (this.locales.hasOwnProperty(locale)) {
                        translatableNameFields.push({
                            label: this.$t('country.property.name'),
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

                this.modalSchemaAddCountry.form.fields = [
                    ...translatableNameFields,
                    {
                        label: this.$t('country.property.short_name'),
                        rules: 'required',
                        name: 'short_name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    }
                ];

                this.$refs['addCountryModal'].openModal();
            },
            addCountry(response) {
                let country = response.data.createCountry;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.country', { modelName: country.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.countries.refresh();
            },
            updateCountryModal(country) {
                let translatableNameFields = [];
                let translatableNameValue = JSON.parse(country.name_translations);
                for (let locale in this.locales) {
                    if (this.locales.hasOwnProperty(locale)) {
                        translatableNameFields.push({
                            label: this.$t('country.property.name'),
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

                this.modalSchemaUpdateCountry.form.fields = [
                    ...translatableNameFields,
                    {
                        label: this.$t('country.property.short_name'),
                        rules: 'required',
                        name: 'short_name',
                        input: 'text',
                        type: 'text',
                        value: country.short_name,
                        config: {}
                    }
                ];

                this.modalSchemaUpdateCountry.form.idField = country.id;

                this.$refs['updateCountryModal'].openModal();
            },
            updateCountry(response) {
                let country = response.data.updateCountry;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.country', { modelName: country.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.countries.refresh();
            },
            deleteCountryModal(country) {
                this.modalSchemaDeleteCountry.form.idField = country.id;

                this.$refs['deleteCountryModal'].openModal();
            },
            deleteCountry(response) {
                let country = response.data.deleteCountry;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.country', { modelName: country.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.countries.refresh();
            }
        },
        apollo: {
            countries: {
                query: COUNTRIES_QUERY,
                variables() {
                    return { page: this.page, limit: this.countries.per_page }
                }
            },
            locales: {
                query: LOCALES_QUERY,
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
