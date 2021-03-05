<template>
    <div class="md-layout">
        <template v-if="firstLoad">
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
            <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-50">
                <md-card>
                    <md-card-header class="md-card-header-text md-card-header-green">
                        <div class="card-text">
                            <h4 class="title">{{ $t('transaction.next_payment.title') }}</h4>
                            <p class="category">{{ $t('transaction.next_payment.text') }}</p>
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <md-table v-model="nextPaymentData" table-header-color="green" v-if="nextPayment">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell>{{ index + 1 }}</md-table-cell>
                                <md-table-cell :md-label="$t('transaction.next_payment.name')">{{ $t('transaction.next_payment.item.' + index) }}</md-table-cell>
                                <md-table-cell class="text-right">{{ item.value | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('transaction.next_payment.priceUnit') }}</md-table-cell>
                            </md-table-row>
                        </md-table>
                        <div class="table table-stats">
                            <div class="td-price">
                                <div class="td-total">
                                    {{ $t('transaction.next_payment.total') }}
                                </div>
                                <span>{{ totalNextPayment | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('transaction.next_payment.priceUnit') }}</span>
                            </div>
                        </div>
                    </md-card-content>
                </md-card>
            </div>
            <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-50">
                <chart-card
                        header-animation="false"
                        :chart-data="pieChartData"
                        :chart-options="pieChart.options"
                        chart-type="Pie"
                        header-icon
                        chart-inside-content
                        background-color="green"
                >
                    <template slot="chartInsideHeader">
                        <div class="card-icon">
                            <md-icon>pie_chart</md-icon>
                        </div>
                        <h4 class="title">
                            {{ $t('transaction.next_payment_chart.title') }}
                        </h4>
                    </template>
                    <template slot="footer">
                        <div class="md-layout">
                            <div class="md-layout-item md-size-100">
                                <h6 class="category">{{ $t('transaction.next_payment_chart.legend') }}</h6>
                            </div>
                            <div class="md-layout-item">
                                <span class="pr-3 pb-3"><span class="circle text-series-a"></span> {{ $t('transaction.next_payment_chart.series_a') }} </span>
                                <span class="pr-3 pb-3"><span class="circle text-series-b"></span> {{ $t('transaction.next_payment_chart.series_b') }}</span>
                                <span class="pr-3 pb-3"><span class="circle text-series-c"></span> {{ $t('transaction.next_payment_chart.series_c') }}</span>
                                <span class="pr-3 pb-3"><span class="circle text-series-d"></span> {{ $t('transaction.next_payment_chart.series_d') }}</span>
                                <span class="pr-3 pb-3"><span class="circle text-series-e"></span> {{ $t('transaction.next_payment_chart.series_e') }}</span>
                                <span class="pr-3 pb-3"><span class="circle text-series-f"></span> {{ $t('transaction.next_payment_chart.series_f') }}</span>
                            </div>
                        </div>
                    </template>
                </chart-card>
            </div>
            <div class="md-layout-item md-size-100 mb-3">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <template v-if="transactions.data && transactions.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="transactions.data" v-if="transactions && transactions.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                    <md-table-cell md-label="#">{{ index + transactions.from }}</md-table-cell>
                                    <md-table-cell :md-label="$t('transaction.property.activity')">{{ $t('transaction.activity.' + item.activity) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('transaction.property.value')">{{ item.value | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('transaction.property.valueUnit') }}</md-table-cell>
                                    <md-table-cell :md-label="$t('transaction.property.product')"><template v-if="item.productable">{{ transactionProduct(item.productable) }}</template><template v-else>-</template></md-table-cell>
                                    <md-table-cell :md-label="$t('transaction.property.user')"><template v-if="item.user">{{ item.user.first_name }} {{ item.user.last_name }}</template><template v-else>{{ $t('transaction.property.no_user') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('transaction.property.created_at')">{{ item.created_at }}</md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: transactions.from, to: transactions.to, total: transactions.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="transactions.per_page"
                                        :total="transactions.total"></pagination>
                        </md-card-actions>
                    </md-card>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>
    </div>
</template>

<script>
    import { Pagination, SearchForm, ChartCard } from "@/components";
    import { TRANSACTIONS_QUERY, USERS_SELECT_QUERY, NEXT_PAYMENT_QUERY } from "@/graphql/queries/user";

    export default {
        title () {
            return this.$t('pages.transactions');
        },
        name: "Transactions",
        components: {
            Pagination,
            SearchForm,
            ChartCard
        },
        data() {
            return {
                transactions: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                users: [],
                usersOptions: [],
                nextPayment: [],
                searchModel: {
                    user: [],
                    value: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    sort: ''
                },
                pieChart: {
                    options: {
                        height: "428px"
                    }
                },
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'user',
                                    label: this.$t('transaction.property.user'),
                                    value: '',
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.first_name + " " + option.last_name;
                                        },
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'value',
                                    labelFrom: this.$t('transaction.property.value') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('transaction.property.value') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                            ],
                        },
                        {
                            class: ['mt-2'],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'sort',
                                    label: this.$t('search.sortBy'),
                                    value: '',
                                    config: {
                                        options: [
                                            { id: 'price_asc', name: this.$t('transaction.searchFields.price_asc') },
                                            { id: 'price_desc', name: this.$t('transaction.searchFields.price_desc') },
                                            { id: 'created_at_asc', name: this.$t('transaction.searchFields.created_at_asc') },
                                            { id: 'created_at_desc', name: this.$t('transaction.searchFields.created_at_desc') },
                                        ],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        optionLabel: (option) => {
                                            return option.name;
                                        },
                                    }
                                },
                            ]
                        }
                    ],
                },
            }
        },
        computed: {
            nextPaymentData() {
                return this.nextPayment ? this.nextPayment.map(p => {return {value: p} }) : [];
            },
            totalNextPayment() {
                return this.nextPayment ? this.nextPayment.reduce((a, b) => a + b, 0) : 0;
            },
            pieChartData() {
                return this.nextPayment ? {labels: this.nextPaymentChartData.map(p => {return (p / this.totalNextPayment * 100).toFixed(0) + "%" }), series:  this.nextPaymentChartData} : {labels: [], series: []};
            },
            nextPaymentChartData() {
                return this.nextPayment ? [this.nextPayment[0], this.nextPayment[1], this.nextPayment[2] + this.nextPayment[3], this.nextPayment[4] + this.nextPayment[5], this.nextPayment[6] + this.nextPayment[7], this.nextPayment[8]] : [];
            }
        },
        methods: {
            transactionProduct(product) {
                if (!product) {
                    return "";
                }
                switch (product.__typename) {
                    case "Driver":
                        return product.first_name + " " + product.last_name + " - " + product.garage.location.name + " (" + product.garage.location.country.short_name.toUpperCase() + ")";
                    case "Garage":
                        return product.garageModel.name + " - " + product.location.name + " (" + product.location.country.short_name.toUpperCase() + ")";
                    case "Truck":
                        return product.truckModel.brand + " " + product.truckModel.name + " - " + product.garage.location.name + " (" + product.garage.location.country.short_name.toUpperCase() + ")";
                    case "Trailer":
                        return product.trailerModel.name + " - " + product.garage.location.name + " (" + product.garage.location.country.short_name.toUpperCase() + ")";
                    case "Order":
                        return product.market.cargo.name + " - " + product.market.locationFrom.name + " (" + product.market.locationFrom.country.short_name.toUpperCase() + ")" + " >>> " + product.market.locationTo.name + " (" + product.market.locationTo.country.short_name.toUpperCase() + ")";
                    case "BankLoan":
                        return product.bankLoanType.value + " € - " + this.$t('bankLoanType.teaser.repayment') + ": " + product.bankLoanType.period + " " + this.$tc('bankLoanType.property.periodUnit', product.bankLoanType.period);
                    default:
                        return "";
                }
            },
        },
        apollo: {
            transactions: {
                query: TRANSACTIONS_QUERY,
                fetchPolicy: 'no-cache',
                variables() {
                    return {page: this.page, limit: this.transactions.per_page, filter: this.filters, sort: this.sort}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            users: {
                query: USERS_SELECT_QUERY,
                variables() {
                    return {page: 1, limit: -1, filter: []}
                },
                result({data, loading, networkStatus}) {

                    this.usersOptions = data.users.data;
                    this.usersOptions = _.uniq(_.concat(this.usersOptions, {id: 'system', first_name: 'System', last_name: ''}))
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.usersOptions);
                    });
                }
            },
            nextPayment: {
                query: NEXT_PAYMENT_QUERY,
            }
        }
    }
</script>

<style lang="scss" scoped>
    .text-right .md-table-cell-container {
        display: flex;
        justify-content: flex-end;
    }
    .table-stats {
        display: flex;
        align-items: center;
        text-align: right;
        flex-flow: row wrap;

        .td-price .td-total {
            display: inline-flex;
            font-weight: 500;
            font-size: 1.0625rem;
            margin-right: 50px;
        }

        .td-price {
            border-bottom: 0;
        }

        .td-price {
            font-size: 26px;
            border-top: 1px solid #ddd;
        }

        .td-price,
        > div {
            flex: 0 0 100%;
            padding: 12px 8px;
        }
    }
    .circle {
        height: 15px;
        width: 15px;
        border-radius: 50%;
        display: inline-block;
        vertical-align: sub;
    }
    .text-series-a {
        background-color: #00bcd4;
    }
    .text-series-b {
        background-color: #f44336;
    }
    .text-series-c {
        background-color: #ff9800;
    }
    .text-series-d {
        background-color: #43a047;
    }
    .text-series-e {
        background-color: #4caf50;
    }
    .text-series-f {
        background-color: #9C9B99;
    }
</style>
