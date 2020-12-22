<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.bankLoanTypes') }}</h4>
                        <md-button class="md-primary md-simple" @click="addBankLoanTypeModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content>
                    <template v-if="$apollo.queries.bankLoanTypes.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="bankLoanTypes.data" v-if="bankLoanTypes && bankLoanTypes.data">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + bankLoanTypes.from }}</md-table-cell>
                                <md-table-cell :md-label="$t('bankLoanType.property.value')">{{ item.value }} {{ $t('bankLoanType.property.valueUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('bankLoanType.property.payment')">{{ item.payment }} {{ $t('bankLoanType.property.paymentUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('bankLoanType.property.period')">{{ item.period }} {{ $tc('bankLoanType.property.periodUnit', item.period) }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')" class="text-right">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateBankLoanTypeModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteBankLoanTypeModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: bankLoanTypes.from, to: bankLoanTypes.to, total: bankLoanTypes.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="bankLoanTypes.per_page"
                                :total="bankLoanTypes.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add bank load type modal-->
        <mutation-modal ref="addBankLoanTypeModal" @ok="addBankLoanType" :modalSchema="modalSchemaAddBankLoanType" />

        <!-- Update bank load type modal-->
        <mutation-modal ref="updateBankLoanTypeModal" @ok="updateBankLoanType" :modalSchema="modalSchemaUpdateBankLoanType" />

        <!-- Delete bank load type modal-->
        <delete-modal ref="deleteBankLoanTypeModal" @ok="deleteBankLoanType" :modalSchema="modalSchemaDeleteBankLoanType" />
    </div>
</template>

<script>
    import { BANK_LOAN_TYPES_QUERY } from '@/graphql/queries/admin';
    import { CREATE_BANK_LOAN_TYPE_MUTATION, UPDATE_BANK_LOAN_TYPE_MUTATION, DELETE_BANK_LOAN_TYPE_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";
    import {LOCALES_QUERY} from "../../graphql/queries/common";

    export default {
        title () {
            return this.$t('pages.bankLoanTypes');
        },
        name: "BankLoadTypes",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                bankLoanTypes: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                },
                page: 1,
                modalSchemaAddBankLoanType: {
                    form: {
                        mutation: CREATE_BANK_LOAN_TYPE_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add', {model: 'bank loan type'}),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateBankLoanType: {
                    form: {
                        mutation: UPDATE_BANK_LOAN_TYPE_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update', {model: 'bank loan type'}),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteBankLoanType: {
                    message: this.$t('model.modal.message', {model: 'bank loan type'}),
                    form: {
                        mutation: DELETE_BANK_LOAN_TYPE_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addBankLoanTypeModal() {
                this.modalSchemaAddBankLoanType.form.fields = [
                    {
                        label: this.$t('bankLoanType.property.value'),
                        rules: 'required',
                        name: 'value',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('bankLoanType.additionalLabelText.value')
                        }
                    },
                    {
                        label: this.$t('bankLoanType.property.payment'),
                        rules: 'required',
                        name: 'payment',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('bankLoanType.additionalLabelText.payment')
                        }
                    },
                    {
                        label: this.$t('bankLoanType.property.period'),
                        rules: 'required',
                        name: 'period',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('bankLoanType.additionalLabelText.period')
                        }
                    },
                ];

                this.$refs['addBankLoanTypeModal'].openModal();
            },
            addBankLoanType(response) {
                let bankLoanType = response.data.createBankLoanType;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created', { model: 'bank loan type', modelName: bankLoanType.value + "€" }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.bankLoanTypes.refresh();
            },
            updateBankLoanTypeModal(bankLoanType) {
                this.modalSchemaUpdateBankLoanType.form.fields = [
                    {
                        label: this.$t('bankLoanType.property.value'),
                        rules: 'required',
                        name: 'value',
                        input: 'text',
                        type: 'text',
                        value: bankLoanType.value,
                        config: {
                            labelAdditionalText: this.$t('bankLoanType.additionalLabelText.value')
                        }
                    },
                    {
                        label: this.$t('bankLoanType.property.payment'),
                        rules: 'required',
                        name: 'payment',
                        input: 'text',
                        type: 'text',
                        value: bankLoanType.payment,
                        config: {
                            labelAdditionalText: this.$t('bankLoanType.additionalLabelText.payment')
                        }
                    },
                    {
                        label: this.$t('bankLoanType.property.period'),
                        rules: 'required',
                        name: 'period',
                        input: 'text',
                        type: 'text',
                        value: bankLoanType.period,
                        config: {
                            labelAdditionalText: this.$t('bankLoanType.additionalLabelText.period')
                        }
                    },
                ];

                this.modalSchemaUpdateBankLoanType.form.idField = bankLoanType.id;

                this.$refs['updateBankLoanTypeModal'].openModal();
            },
            updateBankLoanType(response) {
                let bankLoanType = response.data.updateBankLoanType;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated', { model: 'bank loan type', modelName: bankLoanType.value + "€" }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.bankLoanTypes.refresh();
            },
            deleteBankLoanTypeModal(bankLoanType) {
                this.modalSchemaDeleteBankLoanType.form.idField = bankLoanType.id;

                this.$refs['deleteBankLoanTypeModal'].openModal();
            },
            deleteBankLoanType(response) {
                let bankLoanType = response.data.deleteBankLoanType;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted', { model: 'bank loan type', modelName: bankLoanType.value + "€" }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.bankLoanTypes.refresh();
            }
        },
        apollo: {
            bankLoanTypes: {
                query: BANK_LOAN_TYPES_QUERY,
                variables() {
                    return { page: this.page, limit: this.bankLoanTypes.per_page }
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .text-right {
        display: flex;
    }
    .text-right .md-table-cell-container {
        display: flex!important;
        justify-content: flex-end;
    }
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
