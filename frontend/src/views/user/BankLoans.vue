<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.bankLoanTypes.loading && $apollo.queries.bankLoans.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <md-card>
                    <md-card-header>
                        <h4 class="title">{{ $t('bankLoanType.offer') }}</h4>
                    </md-card-header>
                </md-card>
            </div>
            <template v-if="bankLoanTypes.data && bankLoanTypes.data.length > 0">
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25" v-for="(bankLoanType, index) in bankLoanTypes.data" :key="index">
                    <pricing-card icon-color="icon-success">
                        <h3 slot="title" class="title">{{ bankLoanType.value | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('bankLoanType.property.valueUnit') }}</h3>
                        <p slot="description" class="card-description">
                            {{ $t('bankLoanType.teaser.repayment') }}: {{ bankLoanType.period }} {{ $tc('bankLoanType.property.periodUnit', bankLoanType.period) }}<br>
                            {{ $t('bankLoanType.teaser.installment') }}: {{ bankLoanType.payment | currency(' ', 0, { thousandsSeparator: ' ' })}} {{ $t('bankLoanType.property.valueUnit')}}
                        </p>
                        <md-button slot="footer" @click="createBankLoanModal(bankLoanType)" class="md-success md-round" v-if="hasPermission(constants.PERMISSION.MANAGE_SALARY)">{{ $t('bankLoanType.teaser.btn') }}</md-button>
                    </pricing-card>
                </div>
                <div class="md-layout-item md-size-100 d-flex justify-space-between">
                    <p>
                        {{ $t('pagination.display', {from: bankLoanTypes.from, to: bankLoanTypes.to, total: bankLoanTypes.total}) }}
                    </p>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="bankLoanTypesPage"
                                :per-page="bankLoanTypes.per_page"
                                :total="bankLoanTypes.total"></pagination>
                </div>
            </template>
            <template v-if="bankLoans.data && bankLoans.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-header>
                            <h4 class="title">{{ $t('bankLoan.activeLoans') }}</h4>
                        </md-card-header>
                        <md-card-content class="pb-0">
                            <md-table v-model="bankLoans.data" v-if="bankLoans && bankLoans.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                    <md-table-cell md-label="#">{{ index + bankLoans.from }}</md-table-cell>
                                    <md-table-cell :md-label="$t('bankLoan.property.loan')" class="td-name">{{ item.bankLoanType.value | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('bankLoan.property.loanUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('bankLoan.property.left_to_repay')">{{ leftToRepay(item) | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('bankLoan.property.loanUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('bankLoan.property.duration')">{{ item.paid }} / {{ item.bankLoanType.period }}</md-table-cell>
                                    <md-table-cell :md-label="$t('bankLoan.property.installment')">{{ item.bankLoanType.payment }} {{ $t('bankLoan.property.installmentUnit') }}</md-table-cell>
                                    <md-table-cell v-if="hasPermission(constants.PERMISSION.MANAGE_SALARY)">
                                        <md-button class="md-success normal" @click="updateBankLoanModal(item)">{{ $t('bankLoan.property.repay') }}</md-button>
                                    </md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                    </md-card>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('bankLoan.noResults') }}
                </div>
            </template>
            <template v-if="hasPermission(constants.PERMISSION.MANAGE_SALARY)">
                <!-- Create bank loan modal-->
                <delete-modal ref="createBankLoanModal" @ok="createBankLoan" :modalSchema="modalSchemaCreateBankLoan" />

                <!-- Update bank loan modal-->
                <delete-modal ref="updateBankLoanModal" @ok="updateBankLoan" :modalSchema="modalSchemaUpdateBankLoan" />
            </template>
        </template>
    </div>
</template>

<script>
    import { DeleteModal, PricingCard, Pagination } from "@/components";
    import { BANK_LOAN_TYPES_QUERY } from '@/graphql/queries/common';
    import { BANK_LOANS_QUERY } from '@/graphql/queries/user';
    import { CREATE_BANK_LOAN_MUTATION, UPDATE_BANK_LOAN_MUTATION } from '@/graphql/mutations/user';
    import constants from "../../constants";
    import { mapGetters } from "vuex";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.bankLoans');
        },
        name: "BankLoans",
        components: {
            DeleteModal,
            PricingCard,
            Pagination
        },
        data() {
            return {
                bankLoanTypes: {
                    data: [],
                    per_page: 4,
                    current_page: 1,
                },
                bankLoans: {
                    data: [],
                    per_page: 5,
                    current_page: 1,
                },
                constants: constants,
                bankLoansPage: 1,
                bankLoanTypesPage: 1,
                firstLoad: true,
                modalSchemaCreateBankLoan: {
                    message: '',
                    form: {
                        mutation: CREATE_BANK_LOAN_MUTATION,
                        hiddenFields: [],
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.apply'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateBankLoan: {
                    message: '',
                    form: {
                        mutation: UPDATE_BANK_LOAN_MUTATION,
                        hiddenFields: [],
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.repay'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
            }
        },
        computed: {
            ...mapGetters([
                'hasPermission',
            ]),
        },
        methods: {
            leftToRepay(item) {
                return item.bankLoanType.payment * (item.bankLoanType.period -  item.paid);
            },
            createBankLoanModal(bankLoanType) {
                this.modalSchemaCreateBankLoan.form.hiddenFields = [
                    {
                        name: 'bank_loan_type',
                        value: bankLoanType.id
                    }
                ];

                let price = this.$options.filters.currency(bankLoanType.value, ' ', 2, { thousandsSeparator: ' ' }) + ' €';

                this.modalSchemaCreateBankLoan.message = this.$t('model.modal.title.add.bankLoan', {price: price})

                this.$refs['createBankLoanModal'].openModal();
            },
            createBankLoan(response) {
                let bankLoan = response.data.createBankLoan;
                let price = this.$options.filters.currency(bankLoan.bankLoanType.value, ' ', 2, { thousandsSeparator: ' ' }) + ' €';
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.bankLoan', {price: price}),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.bankLoans.refresh();
            },
            updateBankLoanModal(bankLoan) {
                this.modalSchemaUpdateBankLoan.form.hiddenFields = [
                    {
                        name: 'id',
                        value: bankLoan.id
                    }
                ];

                this.modalSchemaUpdateBankLoan.message = this.$t('model.modal.title.update.bankLoan');

                this.$refs['updateBankLoanModal'].openModal();
            },
            updateBankLoan(response) {
                let bankLoan = response.data.updateBankLoan;
                let price = this.$options.filters.currency(bankLoan.bankLoanType.value, ' ', 2, { thousandsSeparator: ' ' }) + ' €';
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.bankLoan', {price: price}),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.bankLoans.refresh();
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', (payLoad) => {
                if (payLoad.modelType === 'BankLoan') {
                    this.$apollo.queries.bankLoans.refresh();
                }
            });
        },
        apollo: {
            bankLoanTypes: {
                query: BANK_LOAN_TYPES_QUERY,
                variables() {
                    return { page: this.bankLoanTypesPage, limit: this.bankLoanTypes.per_page }
                }
            },
            bankLoans: {
                query: BANK_LOANS_QUERY,
                variables() {
                    return { page: this.bankLoansPage, limit: this.bankLoans.per_page }
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-table-cell-container .md-button.normal {
        margin: unset;
        padding: unset;
        height: unset;
        min-width: unset;
        width: unset;
        line-height: unset;
    }
</style>
