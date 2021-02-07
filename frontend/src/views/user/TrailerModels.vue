<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.trailerModels.loading && firstLoad">
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
            <template v-if="trailerModels.data && trailerModels.data.length > 0">
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-33" v-for="(trailerModel, index) in trailerModels.data" :key="index" >
                    <product-card header-animation="false">
                        <img class="img" slot="imageHeader" :src="trailerModel.image" />
                        <h4 slot="title" class="title mt-2 mb-2">
                            {{ trailerModel.name }}
                        </h4>
                        <div slot="description" class="card-description">
                            <div class="md-layout md-alignment-center-space-between">
                                <div class="md-layout-item md-size-50 text-left">{{ $t('trailerModel.property.type') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ trailerModel.type }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('trailerModel.property.load') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ trailerModel.load }} {{ $t('trailerModel.property.loadUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('trailerModel.property.adr') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ $t('ADRs.' + trailerModel.adr) }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('trailerModel.property.km') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ trailerModel.km }} {{ $t('trailerModel.property.kmUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('trailerModel.property.insurance') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ trailerModel.insurance | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.insuranceUnit') }}</div>

                                <div class="md-layout-item md-size-50 text-left">{{ $t('trailerModel.property.tax') }}</div>
                                <div class="md-layout-item md-size-50 text-right">{{ trailerModel.tax | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.taxUnit') }}</div>
                            </div>
                        </div>
                        <template slot="footer">
                            <div class="price">
                                <h4>{{ trailerModel.price | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('trailerModel.property.priceUnit') }}</h4>
                            </div>
                            <md-button class="md-primary md-simple" @click="addTrailerModal(trailerModel)"><md-icon>add</md-icon>{{ $t('shop.buy') }}</md-button>
                        </template>
                    </product-card>
                </div>
                <div class="md-layout-item md-size-100 d-flex justify-space-between">
                    <p>
                        {{ $t('pagination.display', {from: trailerModels.from, to: trailerModels.to, total: trailerModels.total}) }}
                    </p>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="trailerModels.per_page"
                                :total="trailerModels.total"></pagination>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>

        <!-- Add trailer modal-->
        <mutation-modal ref="addTrailerModal" @ok="addTrailer" :modalSchema="modalSchemaAddTrailer" />
    </div>
</template>

<script>
    import { SearchForm, ProductCard, Pagination, MutationModal } from "@/components";
    import { TRAILER_MODELS_QUERY, TRAILER_TYPES_QUERY, ADRS_QUERY } from "@/graphql/queries/common";
    import { CREATE_TRAILER_MUTATION } from "@/graphql/mutations/user";
    import { AVAILABLE_GARAGES_QUERY } from "@/graphql/queries/user";

    export default {
        title () {
            return this.$t('pages.trailerShop');
        },
        name: "TrailerModels",
        components: {
            ProductCard,
            SearchForm,
            Pagination,
            MutationModal
        },
        data() {
            return {
                trailerModels: {
                    data: [],
                    per_page: 6,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                ADRs: [],
                trailerTypes: [],
                page: 1,
                firstLoad: true,
                searchModel: {
                    type: [],
                    adr: [],
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
                ADRsOptions: [],
                trailerTypesOptions: [],
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'type',
                                    label: this.$t('trailerModel.property.type'),
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
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'adr',
                                    label: this.$t('trailerModel.property.adr'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        translatableLabel: 'ADRs.',
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'load',
                                    labelFrom: this.$t('trailerModel.property.load') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('trailerModel.property.load') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'text',
                                    input: 'range',
                                    name: 'price',
                                    labelFrom: this.$t('trailerModel.property.price') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('trailerModel.property.price') + ' ' + this.$t('search.to'),
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
                                            { id: 'price_asc', name: this.$t('trailerModel.searchFields.price_asc') },
                                            { id: 'price_desc', name: this.$t('trailerModel.searchFields.price_desc') },
                                            { id: 'adr_asc', name: this.$t('trailerModel.searchFields.adr_asc') },
                                            { id: 'adr_desc', name: this.$t('trailerModel.searchFields.adr_desc') },
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
                modalSchemaAddTrailer: {
                    form: {
                        mutation: CREATE_TRAILER_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.trailer'),
                    okBtnTitle: this.$t('modal.btn.buy'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
            }
        },
        methods: {
            addTrailerModal(trailerModel) {
                this.modalSchemaAddTrailer.form.fields = [
                    {
                        input: 'staticText',
                        text: this.$t('trailerModel.model') + ' ' + trailerModel.name,
                        class: 'text-left mb-4'
                    },
                    {
                        label: this.$t('trailer.property.garage'),
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
                        text: this.$options.filters.currency(trailerModel.price, ' ', 2, { thousandsSeparator: ' ' }) + ' €',
                        class: 'text-right md-title'
                    },
                ];

                this.modalSchemaAddTrailer.form.hiddenFields = [
                    {
                        name: 'trailer_model',
                        value: trailerModel.id
                    }
                ];

                this.$refs['addTrailerModal'].openModal();
            },
            addTrailer(response) {
                let trailer = response.data.createTrailer;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.trailer', { modelName: trailer.trailerModel.name, location: trailer.garage.location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
            },
        },
        apollo: {
            trailerModels: {
                query: TRAILER_MODELS_QUERY,
                variables() {
                    return { page: this.page, limit: this.trailerModels.per_page, filter: this.filters, sort: this.sort }
                },
                result({ data, loading, networkStatus }) {
                    this.firstLoad = false;
                }
            },
            trailerTypes: {
                query: TRAILER_TYPES_QUERY,
                result({ data, loading, networkStatus }) {
                    this.trailerTypesOptions = data.trailerTypes;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.trailerTypesOptions);
                    });
                },
            },
            ADRs: {
                query: ADRS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.ADRsOptions = data.ADRs;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.ADRsOptions);
                    });
                },
            },
            availableGarages: {
                query: AVAILABLE_GARAGES_QUERY,
                variables() {
                    return { page: 1, limit: -1, type: 'trailer' }
                },
            }
        },
    }
</script>

<style scoped>

</style>
