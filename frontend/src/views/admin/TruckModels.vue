<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.truckModels') }}</h4>
                        <md-button class="md-primary md-simple" @click="addTruckModelModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.truckModels.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="truckModels.data" v-if="truckModels && truckModels.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + truckModels.from }}</md-table-cell>
                                <md-table-cell md-label="">
                                    <div class="img-container">
                                        <img :src="item.image" :alt="item.name" />
                                    </div>
                                </md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.name')" class="td-name">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.brand')">{{ item.brand }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.engine_power')">{{ item.engine_power }} {{ $t('truckModel.property.engine_powerUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.chassis')">{{ item.chassis }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.load')">{{ item.load | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.loadUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.emission_class')">{{ $t('truckEmissionClasses.' + item.emission_class) }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.km')">{{ item.km | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.kmUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.price')">{{ item.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.priceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.insurance')">{{ item.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.insuranceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('truckModel.property.tax')">{{ item.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.taxUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateTruckModelModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteTruckModelModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: truckModels.from, to: truckModels.to, total: truckModels.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="truckModels.per_page"
                                :total="truckModels.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add truck model modal-->
        <mutation-modal ref="addTruckModelModal" @ok="addTruckModel" :modalSchema="modalSchemaAddTruckModel" />

        <!-- Update truck model modal-->
        <mutation-modal ref="updateTruckModelModal" @ok="updateTruckModel" :modalSchema="modalSchemaUpdateTruckModel" />

        <!-- Delete truck model modal-->
        <delete-modal ref="deleteTruckModelModal" @ok="deleteTruckModel" :modalSchema="modalSchemaDeleteTruckModel" />
    </div>
</template>

<script>
    import { TRUCK_MODELS_QUERY, TRUCK_BRANDS_QUERY, CHASSIS_QUERY, TRUCK_EMISSION_CLASSES_QUERY } from "@/graphql/queries/common";
    import { CREATE_TRUCK_MODEL_MUTATION, UPDATE_TRUCK_MODEL_MUTATION, DELETE_TRUCK_MODEL_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";

    export default {
        title () {
            return this.$t('pages.truckModels');
        },
        name: "TruckModels",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                truckModels: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                truckBrands: [],
                chassis: [],
                truckEmissionClasses: [],
                page: 1,
                modalSchemaAddTruckModel: {
                    form: {
                        mutation: CREATE_TRUCK_MODEL_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add', { model: 'truck model' }),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateTruckModel: {
                    form: {
                        mutation: UPDATE_TRUCK_MODEL_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update', { model: 'truck model' }),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteTruckModel: {
                    message: this.$t('model.modal.message', { model: 'truck model' }),
                    form: {
                        mutation: DELETE_TRUCK_MODEL_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addTruckModelModal() {
                this.modalSchemaAddTruckModel.form.fields = [
                    {
                        label: this.$t('truckModel.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('truckModel.property.brand'),
                        rules: 'required',
                        name: 'brand',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.truckBrands,
                            optionValue: (option) => {
                                return option;
                            },
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('truckModel.property.engine_power'),
                        rules: 'required|min_integer:1',
                        name: 'engine_power',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.engine_power')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.chassis'),
                        rules: 'required',
                        name: 'chassis',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.chassis,
                            optionValue: (option) => {
                                return option;
                            },
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('truckModel.property.load'),
                        rules: 'required|min_integer:1',
                        name: 'load',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.load')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.emission_class'),
                        rules: 'required',
                        name: 'emission_class',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.truckEmissionClasses,
                            optionValue: (option) => {
                                return option;
                            },
                            translatableLabel: 'truckEmissionClasses.',
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('truckModel.property.price'),
                        rules: 'required|min_integer:1',
                        name: 'price',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.price')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.km'),
                        rules: 'required|min_integer:0',
                        name: 'km',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.km')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.insurance'),
                        rules: 'required|min_integer:1',
                        name: 'insurance',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.insurance')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.tax'),
                        rules: 'required|min_integer:1',
                        name: 'tax',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.tax')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'image',
                        type: 'image',
                        value: '',
                        config: {}
                    },
                ];

                this.$refs['addTruckModelModal'].openModal();
            },
            addTruckModel(response) {
                let truckModel = response.data.createTruckModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created', { model: 'truck model', modelName: truckModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.truckModels.refresh();
            },
            updateTruckModelModal(truckModel) {
                this.modalSchemaUpdateTruckModel.form.fields = [
                    {
                        label: this.$t('truckModel.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: truckModel.name,
                        config: {}
                    },
                    {
                        label: this.$t('truckModel.property.brand'),
                        rules: 'required',
                        name: 'brand',
                        input: 'select',
                        type: 'select',
                        value: truckModel.brand,
                        config: {
                            options: this.truckBrands,
                            optionValue: (option) => {
                                return option;
                            },
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('truckModel.property.engine_power'),
                        rules: 'required|min_integer:1',
                        name: 'engine_power',
                        input: 'text',
                        type: 'text',
                        value: truckModel.engine_power,
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.engine_power')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.chassis'),
                        rules: 'required',
                        name: 'chassis',
                        input: 'select',
                        type: 'select',
                        value: truckModel.chassis,
                        config: {
                            options: this.chassis,
                            optionValue: (option) => {
                                return option;
                            },
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('truckModel.property.load'),
                        rules: 'required|min_integer:1',
                        name: 'load',
                        input: 'text',
                        type: 'text',
                        value: truckModel.load,
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.load')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.emission_class'),
                        rules: 'required',
                        name: 'emission_class',
                        input: 'select',
                        type: 'select',
                        value: truckModel.emission_class,
                        config: {
                            options: this.truckEmissionClasses,
                            optionValue: (option) => {
                                return option;
                            },
                            translatableLabel: 'truckEmissionClasses.',
                            optionLabel: (option) => {
                                return option;
                            }
                        }
                    },
                    {
                        label: this.$t('truckModel.property.price'),
                        rules: 'required|min_integer:1',
                        name: 'price',
                        input: 'text',
                        type: 'text',
                        value: truckModel.price,
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.price')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.km'),
                        rules: 'required|min_integer:0',
                        name: 'km',
                        input: 'text',
                        type: 'text',
                        value: truckModel.km,
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.km')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.insurance'),
                        rules: 'required|min_integer:1',
                        name: 'insurance',
                        input: 'text',
                        type: 'text',
                        value: truckModel.insurance,
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.insurance')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.tax'),
                        rules: 'required|min_integer:1',
                        name: 'tax',
                        input: 'text',
                        type: 'text',
                        value: truckModel.tax,
                        config: {
                            labelAdditionalText: this.$t('truckModel.additionalLabelText.tax')
                        }
                    },
                    {
                        label: this.$t('truckModel.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'image',
                        type: 'image',
                        value: truckModel.image,
                        config: {}
                    },
                ];

                this.modalSchemaUpdateTruckModel.form.idField = truckModel.id;

                this.$refs['updateTruckModelModal'].openModal();
            },
            updateTruckModel(response) {
                let truckModel = response.data.updateTruckModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated', { model: 'truck model', modelName: truckModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.truckModels.refresh();
            },
            deleteTruckModelModal(truckModel) {
                this.modalSchemaDeleteTruckModel.form.idField = truckModel.id;

                this.$refs['deleteTruckModelModal'].openModal();
            },
            deleteTruckModel(response) {
                let truckModel = response.data.deleteTruckModel;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted', { model: 'trailer model', modelName: truckModel.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.truckModels.refresh();
            }
        },
        apollo: {
            truckModels: {
                query: TRUCK_MODELS_QUERY,
                variables() {
                    return { page: this.page, limit: this.truckModels.per_page }
                }
            },
            truckBrands: {
                query: TRUCK_BRANDS_QUERY
            },
            chassis: {
                query: CHASSIS_QUERY
            },
            truckEmissionClasses: {
                query: TRUCK_EMISSION_CLASSES_QUERY
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
