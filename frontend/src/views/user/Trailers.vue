<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.trailers.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="10" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100 mb-3">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <template v-if="trailers.data && trailers.data.length > 0">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="trailers.data" v-if="trailers && trailers.data">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item)" class="cursor-pointer-hover">
                                    <md-table-cell md-label="#">{{ index + trailers.from }}</md-table-cell>
                                    <md-table-cell md-label="">
                                        <div class="img-container">
                                            <img :src="item.trailerModel.image" :alt="item.trailerModel.name" />
                                        </div>
                                    </md-table-cell>
                                    <md-table-cell :md-label="$t('trailer.relations.trailerModel')" class="td-name">{{ item.trailerModel.name }}</md-table-cell>
                                    <md-table-cell :md-label="$t('trailer.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>
                                    <md-table-cell :md-label="$t('trailer.property.garage')">{{ item.garage.garageModel.name }} - {{ item.garage.location.name }} ({{ item.garage.location.country.short_name | uppercase }})</md-table-cell>
                                    <md-table-cell :md-label="$t('trailer.relations.truck')"><template v-if="item.truck">{{ item.truck.truckModel.brand }} {{ item.truck.truckModel.name }}</template><template v-else>{{ $t('trailer.relations.no_truck') }}</template></md-table-cell>
                                    <md-table-cell :md-label="$t('trailer.property.adr')">{{ $t('ADRs.' + item.trailerModel.adr) }}</md-table-cell>
                                </md-table-row>
                            </md-table>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="">
                                <p class="card-category">
                                    {{ $t('pagination.display', {from: trailers.from, to: trailers.to, total: trailers.total}) }}
                                </p>
                            </div>
                            <pagination class="pagination-no-border pagination-success"
                                        v-model="page"
                                        :per-page="trailers.per_page"
                                        :total="trailers.total"></pagination>
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
    import { TRAILERS_QUERY, GARAGES_SELECT_QUERY } from '@/graphql/queries/user';
    import { TRAILER_MODELS_SELECT_QUERY, STATUSES_QUERY } from "@/graphql/queries/common";
    import { Pagination, SearchForm } from "@/components";
    import EventBus from "../../event-bus";

    export default {
        title () {
            return this.$t('pages.trailers');
        },
        name: "Trailers",
        components: {
            Pagination,
            SearchForm
        },
        data() {
            return {
                trailers: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                trailerModelsOptions: [],
                trailerModels: [],
                garagesOptions: [],
                garages: [],
                statuses: [],
                statusesOptions: [],
                searchModel: {
                    status: [],
                    trailerModel: [],
                    garage: [],
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
                                    name: 'status',
                                    label: this.$t('trailer.property.status'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        translatableLabel: 'status.',
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
                                    name: 'trailerModel',
                                    label: this.$t('trailer.relations.trailerModel'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        groupBy: 'type',
                                        optgroupLabel: (optgroup) => {
                                            return optgroup.type;
                                        },
                                        optionLabel: (option) => {
                                            return option.name;
                                        },
                                        multiple: true
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'garage',
                                    label: this.$t('trailer.property.garage'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        groupBy: 'location.country.name',
                                        optgroupLabel: (optgroup) => {
                                            return optgroup.location.country.name;
                                        },
                                        optionLabel: (option) => {
                                            return option.garageModel.name + ' - ' + option.location.name;
                                        },
                                        multiple: true
                                    }
                                },
                            ],
                        },
                    ]
                },
            }
        },
        methods: {
            clickTableRow(item) {
                this.$router.push({
                    name: 'trailer',
                    params: {id: item.id}
                });
            }
        },
        mounted() {
            EventBus.$on('refreshQuery', function (payLoad) {
                if (payLoad.modelType === 'Trailer') {
                    this.$apollo.queries.trailers.refresh();
                }
                if (payLoad.modelType === 'Garage') {
                    this.$apollo.queries.garages.refresh();
                }
            });
        },
        apollo: {
            trailers: {
                query: TRAILERS_QUERY,
                variables() {
                    return {page: this.page, limit: this.trailers.per_page, filter: this.filters}
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            statuses: {
                query: STATUSES_QUERY,
                variables() {
                    return { model: 'trailer'}
                },
                result({ data, loading, networkStatus }) {
                    this.statusesOptions = data.statuses;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[0].config, 'options', this.statusesOptions);
                    });
                }
            },
            trailerModels: {
                query: TRAILER_MODELS_SELECT_QUERY,
                variables() {
                    return { page: 1, limit: -1}
                },
                result({ data, loading, networkStatus }) {
                    this.trailerModelsOptions = data.trailerModels.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[1].config, 'options', this.trailerModelsOptions);
                    });
                }
            },
            garages: {
                query: GARAGES_SELECT_QUERY,
                variables() {
                    return { page: 1, limit: -1}
                },
                result({ data, loading, networkStatus }) {
                    this.garagesOptions = data.garages.data;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[2].config, 'options', this.garagesOptions);
                    });
                }
            },
        }
    }
</script>

<style scoped>

</style>
