<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.garageModels') }}</h4>
                        <md-button class="md-primary md-simple" @click="addGarageModelModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.garageModels.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="garageModels.data" v-if="garageModels && garageModels.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + garageModels.from }}</md-table-cell>
                                <md-table-cell md-label="">
                                    <div class="img-container">
                                        <img :src="item.image" :alt="item.name" />
                                    </div>
                                </md-table-cell>
                                <md-table-cell :md-label="$t('garageModel.property.name')" class="td-name">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('garageModel.property.truck_count')">{{ item.truck_count }}</md-table-cell>
                                <md-table-cell :md-label="$t('garageModel.property.trailer_count')">{{ item.trailer_count }}</md-table-cell>
                                <md-table-cell :md-label="$t('garageModel.property.price')">{{ item.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.priceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('garageModel.property.insurance')">{{ item.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.insuranceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('garageModel.property.tax')">{{ item.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.taxUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateGarageModelModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteGarageModelModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: garageModels.from, to: garageModels.to, total: garageModels.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="garageModels.per_page"
                                :total="garageModels.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add garage model modal-->
        <mutation-modal ref="addGarageModelModal" @ok="addGarageModel" :modalSchema="modalSchemaAddGarageModel" />

        <!-- Update garage model modal-->
        <mutation-modal ref="updateGarageModelModal" @ok="updateGarageModel" :modalSchema="modalSchemaUpdateGarageModel" />

        <!-- Delete garage model modal-->
        <delete-modal ref="deleteGarageModelModal" @ok="deleteGarageModel" :modalSchema="modalSchemaDeleteGarageModel" />
    </div>
</template>

<script>
    import { GARAGE_MODELS_QUERY } from '@/graphql/queries/common';
    import { CREATE_GARAGE_MODEL_MUTATION, UPDATE_GARAGE_MODEL_MUTATION, DELETE_GARAGE_MODEL_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";

    export default {
        title () {
            return this.$t('pages.garageModels');
        },
        name: "GarageModels",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                garageModels: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                modalSchemaAddGarageModel: {
                    form: {
                        mutation: CREATE_GARAGE_MODEL_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.garageModel'),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateGarageModel: {
                    form: {
                        mutation: UPDATE_GARAGE_MODEL_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.garageModel'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteGarageModel: {
                    message: this.$t('model.modal.title.delete.garageModel'),
                    form: {
                        mutation: DELETE_GARAGE_MODEL_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addGarageModelModal() {
                this.modalSchemaAddGarageModel.form.fields = [
                    {
                        label: this.$t('garageModel.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('garageModel.property.truck_count'),
                        rules: 'required|min_integer:1',
                        name: 'truck_count',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.truck_count')
                        }
                    },
                    {
                        label: this.$t('garageModel.property.trailer_count'),
                        rules: 'required|min_integer:1',
                        name: 'trailer_count',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.trailer_count')
                        }
                    },
                    {
                        label: this.$t('garageModel.property.price'),
                        rules: 'required|min_integer:1',
                        name: 'price',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.price')
                        }
                    },
                    {
                        label: this.$t('garageModel.property.insurance'),
                        rules: 'required|min_integer:1',
                        name: 'insurance',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.insurance')
                        }
                    },
                    {
                        label: this.$t('garageModel.property.tax'),
                        rules: 'required|min_integer:1',
                        name: 'tax',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.tax')
                        }
                    },
                    // {
                    //     label: this.$t('garageModel.property.image'),
                    //     rules: 'required',
                    //     name: 'image',
                    //     input: 'image',
                    //     type: 'text',
                    //     value: '',
                    //     config: {}
                    // },
                    {
                        label: this.$t('garageModel.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                ];

                this.$refs['addGarageModelModal'].openModal();
            },
            addGarageModel(response) {
                let garageModel = response.data.createGarageModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.garageModel', { modelName: garageModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.garageModels.refresh();
            },
            updateGarageModelModal(garageModel) {
                this.modalSchemaUpdateGarageModel.form.fields = [
                    {
                        label: this.$t('garageModel.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: garageModel.name,
                        config: {}
                    },
                    {
                        label: this.$t('garageModel.property.truck_count'),
                        rules: 'required|min_integer:1',
                        name: 'truck_count',
                        input: 'text',
                        type: 'text',
                        value: garageModel.truck_count,
                        config: {}
                    },
                    {
                        label: this.$t('garageModel.property.trailer_count'),
                        rules: 'required|min_integer:1',
                        name: 'trailer_count',
                        input: 'text',
                        type: 'text',
                        value: garageModel.trailer_count,
                        config: {}
                    },
                    {
                        label: this.$t('garageModel.property.price'),
                        rules: 'required|min_integer:1',
                        name: 'price',
                        input: 'text',
                        type: 'text',
                        value: garageModel.price,
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.price')
                        }
                    },
                    {
                        label: this.$t('garageModel.property.insurance'),
                        rules: 'required|min_integer:1',
                        name: 'insurance',
                        input: 'text',
                        type: 'text',
                        value: garageModel.insurance,
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.insurance')
                        }
                    },
                    {
                        label: this.$t('garageModel.property.tax'),
                        rules: 'required|min_integer:1',
                        name: 'tax',
                        input: 'text',
                        type: 'text',
                        value: garageModel.tax,
                        config: {
                            labelAdditionalText: this.$t('garageModel.additionalLabelText.tax')
                        }
                    },
                    // {
                    //     label: this.$t('garageModel.property.image'),
                    //     rules: 'required',
                    //     name: 'image',
                    //     input: 'image',
                    //     type: 'text',
                    //     value: garageModel.image,
                    //     config: {}
                    // },
                    {
                        label: this.$t('garageModel.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'text',
                        type: 'text',
                        value: garageModel.image,
                        config: {}
                    },
                ];

                this.modalSchemaUpdateGarageModel.form.idField = garageModel.id;

                this.$refs['updateGarageModelModal'].openModal();
            },
            updateGarageModel(response) {
                let garageModel = response.data.updateGarageModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.garageModel', { modelName: garageModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.garageModels.refresh();
            },
            deleteGarageModelModal(garageModel) {
                this.modalSchemaDeleteGarageModel.form.idField = garageModel.id;

                this.$refs['deleteGarageModelModal'].openModal();
            },
            deleteGarageModel(response) {
                let garageModel = response.data.deleteGarageModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.garageModel', { modelName: garageModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.garageModels.refresh();
            }
        },
        apollo: {
            garageModels: {
                query: GARAGE_MODELS_QUERY,
                variables() {
                    return { page: this.page, limit: this.garageModels.per_page }
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
