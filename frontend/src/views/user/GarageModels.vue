<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.garageModels.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-33" v-for="index in 6" :key="index">
                <content-placeholders-heading />
                <content-placeholders-text :lines="10" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100 mb-5">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <template v-if="garageModels.data && garageModels.data.length > 0">
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-33" v-for="(garageModel, index) in garageModels.data" :key="index" >
                    <product-card header-animation="false">
                        <img class="img" slot="imageHeader" :src="garageModel.image" />
                        <h4 slot="title" class="title mt-2 mb-2">
                            {{ garageModel.name }}
                        </h4>
                        <div slot="description" class="card-description">
                            <div class="md-layout md-alignment-center-space-between">
                                <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.truck_count') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ garageModel.truck_count }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.trailer_count') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ garageModel.trailer_count }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.insurance') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ garageModel.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.insuranceUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('garageModel.property.tax') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ garageModel.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.taxUnit') }}</div>
                            </div>
                        </div>
                        <template slot="footer">
                            <div class="price">
                                <h4>{{ garageModel.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('garageModel.property.priceUnit') }}</h4>
                            </div>
                            <md-button class="md-primary md-simple" @click="addGarageModal(garageModel)"><md-icon>add</md-icon>{{ $t('shop.buy') }}</md-button>
                        </template>
                    </product-card>
                </div>

                <div class="md-layout-item md-size-100 d-flex justify-space-between">
                    <p>
                        {{ $t('pagination.display', {from: garageModels.from, to: garageModels.to, total: garageModels.total}) }}
                    </p>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="garageModels.per_page"
                                :total="garageModels.total"></pagination>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>

        <!-- Add garage modal-->
        <mutation-modal ref="addGarageModal" @ok="addGarage" :modalSchema="modalSchemaAddGarage" />
    </div>
</template>

<script>
    import { SearchForm, ProductCard, Pagination, MutationModal } from "@/components";
    import { GARAGE_MODELS_QUERY, AVAILABLE_LOCATIONS_QUERY } from "@/graphql/queries/common";
    import { CREATE_GARAGE_MUTATION } from "@/graphql/mutations/user";

    export default {
        title () {
            return this.$t('pages.garageShop');
        },
        name: "GarageModels",
        components: {
            ProductCard,
            SearchForm,
            Pagination,
            MutationModal
        },
        data() {
            return {
                garageModels: {
                    data: [],
                    per_page: 6,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                firstLoad: true,
                availableLocations: {
                    data: [],
                },
                page: 1,
                searchModel: {
                    truck_count: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    trailer_count: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    price: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                },
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'truck_count',
                                    labelFrom: this.$t('garageModel.property.truck_count') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('garageModel.property.truck_count') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'trailer_count',
                                    labelFrom: this.$t('garageModel.property.trailer_count') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('garageModel.property.trailer_count') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'price',
                                    labelFrom: this.$t('garageModel.property.price') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('garageModel.property.price') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                }
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
                                            { id: 'price_asc', name: this.$t('garageModel.searchFields.price_asc') },
                                            { id: 'price_desc', name: this.$t('garageModel.searchFields.price_desc') },
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
                    ]
                },
                modalSchemaAddGarage: {
                    form: {
                        mutation: CREATE_GARAGE_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.garage'),
                    okBtnTitle: this.$t('modal.btn.buy'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
            }
        },
        methods: {
            addGarageModal(garageModel) {
                this.modalSchemaAddGarage.form.fields = [
                    {
                        input: 'staticText',
                        text: this.$t('garageModel.model') + ' ' + garageModel.name,
                        class: 'text-left mb-4'
                    },
                    {
                        label: this.$t('garage.property.location'),
                        rules: 'required',
                        name: 'location',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.availableLocations.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            groupBy: 'country.name',
                            optgroupLabel: (optgroup) => {
                                return optgroup.country.name;
                            },
                            optionLabel: (option) => {
                                return option.name;
                            }
                        }
                    },
                    {
                        input: 'staticText',
                        text: this.$options.filters.currency(garageModel.price, ' ', 2, { thousandsSeparator: ' ' }) + ' €',
                        class: 'text-right md-title'
                    },
                ];

                this.modalSchemaAddGarage.form.hiddenFields = [
                    {
                        name: 'garage_model',
                        value: garageModel.id
                    }
                ];

                this.$refs['addGarageModal'].openModal();
            },
            addGarage(response) {
                let garage = response.data.createGarage;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.garage', { modelName: garage.garageModel.name, location: garage.location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
            },
        },
        apollo: {
            garageModels: {
                query: GARAGE_MODELS_QUERY,
                variables() {
                    return {page: this.page, limit: this.garageModels.per_page, filter: this.filters, sort: this.sort}
                },
                result({ data, loading, networkStatus }) {
                    this.firstLoad = false;
                }
            },
            availableLocations: {
                query: AVAILABLE_LOCATIONS_QUERY,
                variables() {
                    return {page: 1, limit: -1}
                }
            }
        }
    }
</script>

<style scoped>

</style>
