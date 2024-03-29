<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.truckModels.loading && firstLoad">
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
            <template v-if="truckModels.data && truckModels.data.length > 0">
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-33 d-flex" v-for="(truckModel, index) in truckModels.data" :key="index" >
                    <product-card header-animation="false">
                        <img class="img" slot="imageHeader" :src="truckModel.image" />
                        <h4 slot="title" class="title mt-2 mb-2">
                            {{ truckModel.brand }} {{ truckModel.name }}
                        </h4>
                        <div slot="description" class="card-description">
                            <div class="md-layout md-alignment-center-space-between">
                                <div class="md-layout-item md-size-50 text-left">{{ $t('truckModel.property.engine_power') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ truckModel.engine_power }} {{ $t('truckModel.property.engine_powerUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('truckModel.property.chassis') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ truckModel.chassis }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('truckModel.property.load') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ truckModel.load }} {{ $t('truckModel.property.loadUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('truckModel.property.emission_class') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ $t('truckEmissionClasses.' + truckModel.emission_class) }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('truckModel.property.insurance') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ truckModel.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.insuranceUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('truckModel.property.tax') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ truckModel.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.taxUnit') }}</div>
                            </div>
                        </div>
                        <template slot="footer">
                            <div class="price">
                                <h4>{{ truckModel.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('truckModel.property.priceUnit') }}</h4>
                            </div>
                            <md-button class="md-primary md-simple" @click="addTruckModal(truckModel)"><md-icon>add</md-icon>{{ $t('shop.buy') }}</md-button>
                        </template>
                    </product-card>
                </div>
                <div class="md-layout-item md-size-100 d-flex justify-space-between">
                    <p>
                        {{ $t('pagination.display', {from: truckModels.from, to: truckModels.to, total: truckModels.total}) }}
                    </p>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="truckModels.per_page"
                                :total="truckModels.total"></pagination>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>

        <!-- Add truck modal-->
        <mutation-modal ref="addTruckModal" @ok="addTruck" :modalSchema="modalSchemaAddTruck" />
    </div>
</template>

<script>
    import { SearchForm, ProductCard, Pagination, MutationModal } from "@/components";
    import { TRUCK_MODELS_QUERY, TRUCK_BRANDS_QUERY, CHASSIS_QUERY, TRUCK_EMISSION_CLASSES_QUERY } from "@/graphql/queries/common";
    import { CREATE_TRUCK_MUTATION } from "@/graphql/mutations/user";
    import { AVAILABLE_GARAGES_QUERY } from "@/graphql/queries/user";

    export default {
        title () {
            return this.$t('pages.truckShop');
        },
        name: "TruckModels",
        components: {
            ProductCard,
            SearchForm,
            Pagination,
            MutationModal
        },
        data() {
            return {
                truckModels: {
                    data: [],
                    per_page: 6,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                availableGarages: {
                    data: [],
                },
                firstLoad: true,
                truckBrands: [],
                chassis: [],
                truckEmissionClasses: [],
                page: 1,
                searchModel: {
                    brand: [],
                    chassis: [],
                    emission_class: [],
                    engine_power: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    load: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    price: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    sort: ''
                },
                chassisOptions: [],
                truckBrandsOptions: [],
                truckEmissionClassesOptions: [],
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'brand',
                                    label: this.$t('truckModel.property.brand'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'chassis',
                                    label: this.$t('truckModel.property.chassis'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'emission_class',
                                    label: this.$t('truckModel.property.emission_class'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        translatableLabel: 'truckEmissionClasses.',
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'engine_power',
                                    labelFrom: this.$t('truckModel.property.engine_power') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('truckModel.property.engine_power') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'load',
                                    labelFrom: this.$t('truckModel.property.load') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('truckModel.property.load') + ' ' + this.$t('search.to'),
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
                                    labelFrom: this.$t('truckModel.property.price') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('truckModel.property.price') + ' ' + this.$t('search.to'),
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
                                            { id: 'price_asc', name: this.$t('truckModel.searchFields.price_asc') },
                                            { id: 'price_desc', name: this.$t('truckModel.searchFields.price_desc') },
                                            { id: 'engine-power_asc', name: this.$t('truckModel.searchFields.engine_power_asc') },
                                            { id: 'engine-power_desc', name: this.$t('truckModel.searchFields.engine_power_desc') },
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
                modalSchemaAddTruck: {
                    form: {
                        mutation: CREATE_TRUCK_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.truck'),
                    okBtnTitle: this.$t('modal.btn.buy'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
            }
        },
        methods: {
            addTruckModal(truckModel) {
                this.modalSchemaAddTruck.form.fields = [
                    {
                        input: 'staticText',
                        text: this.$t('truckModel.model') + ' ' + truckModel.brand + ' ' + truckModel.name,
                        class: 'text-left mb-4'
                    },
                    {
                        label: this.$t('truck.property.garage'),
                        rules: 'required',
                        name: 'garage',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.availableGarages.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            groupBy: 'location.country.name',
                            optgroupLabel: (optgroup) => {
                                return optgroup.location.country.name;
                            },
                            optionLabel: (option) => {
                                return option.location.name + ' - ' + option.garageModel.name;
                            }
                        }
                    },
                    {
                        input: 'staticText',
                        text: this.$options.filters.currency(truckModel.price, ' ', 2, { thousandsSeparator: ' ' }) + ' €',
                        class: 'text-right md-title'
                    },
                ];

                this.modalSchemaAddTruck.form.hiddenFields = [
                    {
                        name: 'truck_model',
                        value: truckModel.id
                    }
                ];

                this.$refs['addTruckModal'].openModal();
            },
            addTruck(response) {
                let truck = response.data.createTruck;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.truck', { modelName: truck.truckModel.brand + ' ' + truck.truckModel.name, location: truck.garage.location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
            },
        },
        apollo: {
            truckModels: {
                query: TRUCK_MODELS_QUERY,
                variables() {
                    return { page: this.page, limit: this.truckModels.per_page, filter: this.filters, sort: this.sort}
                },
                result({ data, loading, networkStatus }) {
                    this.firstLoad = false;
                }
            },
            truckBrands: {
                query: TRUCK_BRANDS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.truckBrandsOptions = data.truckBrands;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.truckBrandsOptions);
                    });
                },
            },
            chassis: {
                query: CHASSIS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.chassisOptions = data.chassis;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.chassisOptions);
                    });
                },
            },
            truckEmissionClasses: {
                query: TRUCK_EMISSION_CLASSES_QUERY,
                result({ data, loading, networkStatus }) {
                    this.truckEmissionClassesOptions = data.truckEmissionClasses;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[2].config, 'options', this.truckEmissionClassesOptions);
                    });
                },
            },
            availableGarages: {
                query: AVAILABLE_GARAGES_QUERY,
                variables() {
                    return { page: 1, limit: -1, type: 'truck' }
                },
            }
        }
    }
</script>

<style scoped>

</style>
