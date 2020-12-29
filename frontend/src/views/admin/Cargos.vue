<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.cargos') }}</h4>
                        <md-button class="md-primary md-simple" @click="addCargoModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.cargos.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="cargos.data" v-if="cargos && cargos.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + cargos.from }}</md-table-cell>
                                <md-table-cell md-label="">
                                    <div class="img-container">
                                        <img :src="item.image" :alt="item.name" />
                                    </div>
                                </md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.name')" class="td-name">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.adr')">{{ $t('ADRs.' + item.adr) }}</md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.engine_power')">{{ item.engine_power }} {{ $t('cargo.property.engine_powerUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.chassis')">{{ item.chassis }}</md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.weight')">{{ item.weight | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('cargo.property.weightUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.min_price')">{{ item.min_price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('cargo.property.min_priceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('cargo.property.max_price')">{{ item.max_price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('cargo.property.max_priceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateCargoModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteCargoModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: cargos.from, to: cargos.to, total: cargos.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="cargos.per_page"
                                :total="cargos.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add cargo modal-->
        <mutation-modal ref="addCargoModal" @ok="addCargo" :modalSchema="modalSchemaAddCargo" />

        <!-- Update cargo modal-->
        <mutation-modal ref="updateCargoModal" @ok="updateCargo" :modalSchema="modalSchemaUpdateCargo" />

        <!-- Delete cargo modal-->
        <delete-modal ref="deleteCargoModal" @ok="deleteCargo" :modalSchema="modalSchemaDeleteCargo" />
    </div>
</template>

<script>
    import { CARGOS_QUERY } from '@/graphql/queries/admin';
    import { CREATE_CARGO_MUTATION, UPDATE_CARGO_MUTATION, DELETE_CARGO_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";
    import {ADRS_QUERY, CHASSIS_QUERY} from "../../graphql/queries/common";

    export default {
        title () {
            return this.$t('pages.cargos');
        },
        name: "Cargos",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                cargos: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                chassis: [],
                ADRs: [],
                page: 1,
                modalSchemaAddCargo: {
                    form: {
                        mutation: CREATE_CARGO_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add', { model: 'cargo' }),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateCargo: {
                    form: {
                        mutation: UPDATE_CARGO_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update', { model: 'cargo' }),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteCargo: {
                    message: this.$t('model.modal.message', { model: 'cargo' }),
                    form: {
                        mutation: DELETE_CARGO_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addCargoModal() {
                this.modalSchemaAddCargo.form.fields = [
                    {
                        label: this.$t('cargo.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('cargo.property.adr'),
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
                        label: this.$t('cargo.property.engine_power'),
                        rules: 'required|min_integer:1',
                        name: 'engine_power',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.engine_power')
                        }
                    },
                    {
                        label: this.$t('cargo.property.chassis'),
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
                        label: this.$t('cargo.property.weight'),
                        rules: 'required|min_integer:1',
                        name: 'weight',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.weight')
                        }
                    },
                    {
                        label: this.$t('cargo.property.min_price'),
                        rules: 'required|min_integer:0',
                        name: 'min_price',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.min_price')
                        }
                    },
                    {
                        label: this.$t('cargo.property.max_price'),
                        rules: 'required|min_integer:0',
                        name: 'max_price',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.max_price')
                        }
                    },

                    {
                        label: this.$t('cargo.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'image',
                        type: 'image',
                        value: '',
                        config: {}
                    },
                ];

                this.$refs['addCargoModal'].openModal();
            },
            addCargo(response) {
                let cargo = response.data.createCargo;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created', { model: 'cargo', modelName: cargo.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.cargos.refresh();
            },
            updateCargoModal(cargo) {
                this.modalSchemaUpdateCargo.form.fields = [
                    {
                        label: this.$t('cargo.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: cargo.name,
                        config: {}
                    },
                    {
                        label: this.$t('cargo.property.adr'),
                        rules: 'required',
                        name: 'adr',
                        input: 'select',
                        type: 'select',
                        value: cargo.adr,
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
                        label: this.$t('cargo.property.engine_power'),
                        rules: 'required|min_integer:1',
                        name: 'engine_power',
                        input: 'text',
                        type: 'text',
                        value: cargo.engine_power,
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.engine_power')
                        }
                    },
                    {
                        label: this.$t('cargo.property.chassis'),
                        rules: 'required',
                        name: 'chassis',
                        input: 'select',
                        type: 'select',
                        value: cargo.chassis,
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
                        label: this.$t('cargo.property.weight'),
                        rules: 'required|min_integer:1',
                        name: 'weight',
                        input: 'text',
                        type: 'text',
                        value: cargo.weight,
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.weight')
                        }
                    },
                    {
                        label: this.$t('cargo.property.min_price'),
                        rules: 'required|min_integer:0',
                        name: 'min_price',
                        input: 'text',
                        type: 'text',
                        value: cargo.min_price,
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.min_price')
                        }
                    },
                    {
                        label: this.$t('cargo.property.max_price'),
                        rules: 'required|min_integer:0',
                        name: 'max_price',
                        input: 'text',
                        type: 'text',
                        value: cargo.max_price,
                        config: {
                            labelAdditionalText: this.$t('cargo.additionalLabelText.max_price')
                        }
                    },

                    {
                        label: this.$t('cargo.property.image'),
                        rules: 'required',
                        name: 'image',
                        input: 'image',
                        type: 'image',
                        value: cargo.image,
                        config: {}
                    },
                ];

                this.modalSchemaUpdateCargo.form.idField = cargo.id;

                this.$refs['updateCargoModal'].openModal();
            },
            updateCargo(response) {
                let cargo = response.data.updateCargo;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated', { model: 'cargo', modelName: cargo.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.cargos.refresh();
            },
            deleteCargoModal(cargo) {
                this.modalSchemaDeleteCargo.form.idField = cargo.id;

                this.$refs['deleteCargoModal'].openModal();
            },
            deleteCargo(response) {
                let cargo = response.data.deleteCargo;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted', { model: 'cargo', modelName: cargo.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.cargos.refresh();
            }
        },
        apollo: {
            cargos: {
                query: CARGOS_QUERY,
                variables() {
                    return { page: this.page, limit: this.cargos.per_page }
                }
            },
            ADRs: {
                query: ADRS_QUERY,
            },
            chassis: {
                query: CHASSIS_QUERY,
            },
        },
    }
</script>

<style lang="scss" scoped>
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
