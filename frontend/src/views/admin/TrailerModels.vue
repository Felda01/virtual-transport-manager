<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.trailerModels') }}</h4>
                        <md-button class="md-primary md-simple" @click="addTrailerModelModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.trailerModels.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="trailerModels.data" v-if="trailerModels && trailerModels.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + trailerModels.from }}</md-table-cell>
                                <md-table-cell md-label="">
                                    <div class="img-container">
                                        <img :src="item.image" :alt="item.name" />
                                    </div>
                                </md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.name')" class="td-name">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.type')">{{ item.type }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.load')">{{ item.load | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.loadUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.adr')">{{ $t('ADRs.' + item.adr) }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.km')">{{ item.km | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.kmUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.price')">{{ item.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.priceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.insurance')">{{ item.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.insuranceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('trailerModel.property.tax')">{{ item.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.taxUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateTrailerModelModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteTrailerModelModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: trailerModels.from, to: trailerModels.to, total: trailerModels.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="trailerModels.per_page"
                                :total="trailerModels.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add trailer model modal-->
        <mutation-modal ref="addTrailerModelModal" @ok="addTrailerModel" :modalSchema="modalSchemaAddTrailerModel" />

        <!-- Update trailer model modal-->
        <mutation-modal ref="updateTrailerModelModal" @ok="updateTrailerModel" :modalSchema="modalSchemaUpdateTrailerModel" />

        <!-- Delete trailer model modal-->
        <delete-modal ref="deleteTrailerModelModal" @ok="deleteTrailerModel" :modalSchema="modalSchemaDeleteTrailerModel" />
    </div>
</template>

<script>
    import { TRAILER_MODELS_QUERY, TRAILER_TYPES_QUERY, ADRS_QUERY } from "@/graphql/queries/common";
    import { CREATE_TRAILER_MODEL_MUTATION, UPDATE_TRAILER_MODEL_MUTATION, DELETE_TRAILER_MODEL_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";

    export default {
        title () {
            return this.$t('pages.trailerModels');
        },
        name: "TrailerModels",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                trailerModels: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                ADRs: [],
                trailerTypes: [],
                page: 1,
                modalSchemaAddTrailerModel: {
                    form: {
                        mutation: CREATE_TRAILER_MODEL_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.trailerModel'),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateTrailerModel: {
                    form: {
                        mutation: UPDATE_TRAILER_MODEL_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.trailerModel'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteTrailerModel: {
                    message: this.$t('model.modal.title.delete.trailerModel'),
                    form: {
                        mutation: DELETE_TRAILER_MODEL_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addTrailerModelModal() {
                this.modalSchemaAddTrailerModel.form.fields = [
                    {
                        label: this.$t('trailerModel.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('trailerModel.property.type'),
                        rules: 'required',
                        name: 'type',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.trailerTypes,
                            optionValue: (option) => {
                                return option;
                            },
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.load'),
                        rules: 'required|min_integer:1',
                        name: 'load',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.load')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.adr'),
                        rules: 'required',
                        name: 'adr',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.ADRs,
                            optionValue: (option) => {
                                return option;
                            },
                            translatableLabel: 'ADRs.',
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.price'),
                        rules: 'required|min_integer:1',
                        name: 'price',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.price')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.km'),
                        rules: 'required|min_integer:0',
                        name: 'km',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.km')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.insurance'),
                        rules: 'required|min_integer:1',
                        name: 'insurance',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.insurance')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.tax'),
                        rules: 'required|min_integer:1',
                        name: 'tax',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.tax')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'image',
                        type: 'image',
                        value: '',
                        config: {}
                    },
                ];

                this.$refs['addTrailerModelModal'].openModal();
            },
            addTrailerModel(response) {
                let trailerModel = response.data.createTrailerModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.trailerModel', { modelName: trailerModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.trailerModels.refresh();
            },
            updateTrailerModelModal(trailerModel) {
                this.modalSchemaUpdateTrailerModel.form.fields = [
                    {
                        label: this.$t('trailerModel.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: trailerModel.name,
                        config: {}
                    },
                    {
                        label: this.$t('trailerModel.property.type'),
                        rules: 'required',
                        name: 'type',
                        input: 'select',
                        type: 'select',
                        value: trailerModel.type,
                        config: {
                            options: this.trailerTypes,
                            optionValue: (option) => {
                                return option;
                            },
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.load'),
                        rules: 'required|min_integer:1',
                        name: 'load',
                        input: 'text',
                        type: 'text',
                        value: trailerModel.load,
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.load')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.adr'),
                        rules: 'required',
                        name: 'adr',
                        input: 'select',
                        type: 'select',
                        value: trailerModel.adr,
                        config: {
                            options: this.ADRs,
                            optionValue: (option) => {
                                return option;
                            },
                            translatableLabel: 'ADRs.',
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.price'),
                        rules: 'required|min_integer:1',
                        name: 'price',
                        input: 'text',
                        type: 'text',
                        value: trailerModel.price,
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.price')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.km'),
                        rules: 'required|min_integer:0',
                        name: 'km',
                        input: 'text',
                        type: 'text',
                        value: trailerModel.km,
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.km')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.insurance'),
                        rules: 'required|min_integer:1',
                        name: 'insurance',
                        input: 'text',
                        type: 'text',
                        value: trailerModel.insurance,
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.insurance')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.tax'),
                        rules: 'required|min_integer:1',
                        name: 'tax',
                        input: 'text',
                        type: 'text',
                        value: trailerModel.tax,
                        config: {
                            labelAdditionalText: this.$t('trailerModel.additionalLabelText.tax')
                        }
                    },
                    {
                        label: this.$t('trailerModel.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'image',
                        type: 'image',
                        value: trailerModel.image,
                        config: {}
                    },
                ];

                this.modalSchemaUpdateTrailerModel.form.idField = trailerModel.id;

                this.$refs['updateTrailerModelModal'].openModal();
            },
            updateTrailerModel(response) {
                let trailerModel = response.data.updateTrailerModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.trailerModel', { modelName: trailerModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.trailerModels.refresh();
            },
            deleteTrailerModelModal(trailerModel) {
                this.modalSchemaDeleteTrailerModel.form.idField = trailerModel.id;

                this.$refs['deleteTrailerModelModal'].openModal();
            },
            deleteTrailerModel(response) {
                let trailerModel = response.data.deleteTrailerModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.trailerModel', { modelName: trailerModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.trailerModels.refresh();
            }
        },
        apollo: {
            trailerModels: {
                query: TRAILER_MODELS_QUERY,
                variables() {
                    return { page: this.page, limit: this.trailerModels.per_page }
                }
            },
            trailerTypes: {
                query: TRAILER_TYPES_QUERY,
            },
            ADRs: {
                query: ADRS_QUERY,
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
