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
                        <md-button class="md-primary" @click="addCountryModal">{{ $t('countries.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content>
                    <template v-if="$apollo.queries.countries.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="countries.data" v-if="countries && countries.data" class="table-striped">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>
                                <md-table-cell :md-label="$t('countries.property.name')">{{ item.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('countries.property.short_name')">{{ item.short_name }}</md-table-cell>
                                <md-table-cell :md-label="$t('countries.actions')" class="text-right">
                                    <md-button class="md-just-icon md-success md-simple"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                        <div class="table table-stats">
                            <div class="text-right">
                                <md-button class="md-primary" @click="addCountryModal">{{ $t('countries.new') }}</md-button>
                            </div>
                        </div>
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
        <mutation-modal ref="addCountryModal" @ok="addCountry" :modalSchema="modalSchemaAddCountry" />
    </div>
</template>

<script>
    import { COUNTRIES_QUERY } from '@/graphql/queries/admin';
    import { CREATE_COUNTRY_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination } from "@/components";

    export default {
        title () {
            return this.$t('pages.countries');
        },
        name: "Countries",
        components: {
            MutationModal,
            Pagination
        },
        data() {
            return {
                countries: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                },
                page: 1,
                modalSchemaAddCountry: {
                    form: {
                        mutation: CREATE_COUNTRY_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('countries.modal.title'),
                    okBtnTitle: this.$t('modal.add.btn'),
                    cancelBtnTitle: this.$t('modal.cancel.btn')
                },
            }
        },
        methods: {
            addCountryModal() {
                this.modalSchemaAddCountry.form.fields = [
                    {
                        label: this.$t('countries.property.name'),
                        rules: 'required',
                        name: 'name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('countries.property.short_name'),
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
                this.countries.data.push(country);
                this.$notify({
                    timeout: 5000,
                    message: this.$t('countries.response.success.created', { country: country.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.countries.refresh();
            },
        },
        apollo: {
            countries: {
                query: COUNTRIES_QUERY,
                variables() {
                    return { page: this.page, limit: this.countries.per_page }
                }
            },
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

        &.table-striped .td-price {
            border-bottom: 0;
        }

        .td-price {
            font-size: 26px;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .td-price,
        > div {
            flex: 0 0 100%;
            padding: 12px 8px;
        }
    }
</style>
