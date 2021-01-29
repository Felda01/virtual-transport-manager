<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.recruitmentAgencyDrivers.loading  && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-33" v-for="index in 6" :key="index">
                <content-placeholders-heading />
                <content-placeholders-text :lines="4" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100 mb-5">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <template v-if="recruitmentAgencyDrivers.data && recruitmentAgencyDrivers.data.length > 0">
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-33 mb-4" v-for="(driver, index) in recruitmentAgencyDrivers.data" :key="index">
                    <md-card class="md-card-profile">
                        <div class="md-card-avatar">
                            <img class="img" :src="driver.image" :alt="driver.first_name + ' ' + driver.last_name" />
                        </div>
                        <md-card-content>
                            <h4 class="title mt-2 mb-2">
                                {{ driver.first_name }} {{ driver.last_name }}
                            </h4>
                            <div class="card-description">
                                <div class="md-layout md-alignment-center-space-between">
                                    <div class="md-layout-item md-size-50 text-left">{{ $t('driver.property.preferred_road_trips') }}</div>
                                    <div class="md-layout-item md-size-50 text-right">{{ $t('preferred_road_trips.' + driver.preferred_road_trips) }}</div>
                                </div>
                                <div class="md-layout md-alignment-center-space-between">
                                    <div class="md-layout-item md-size-50 text-left">{{ $t('driver.property.adr') }}</div>
                                    <div class="md-layout-item md-size-50 text-right">{{ $t('ADRsShort.' + driver.adr) }}</div>
                                </div>
                            </div>
                        </md-card-content>
                        <md-card-actions md-alignment="space-between">
                            <div class="price">
                                <h4>{{ driver.salary | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('driver.property.salaryUnit') }}</h4>
                            </div>
                            <md-button class="md-primary md-simple" @click="addDriverModal(driver)"><md-icon>add</md-icon>{{ $t('shop.hire') }}</md-button>
                        </md-card-actions>
                    </md-card>
                </div>
                <div class="md-layout-item md-size-100 d-flex justify-space-between">
                    <p>
                        {{ $t('pagination.display', {from: recruitmentAgencyDrivers.from, to: recruitmentAgencyDrivers.to, total: recruitmentAgencyDrivers.total}) }}
                    </p>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="recruitmentAgencyDrivers.per_page"
                                :total="recruitmentAgencyDrivers.total"></pagination>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>

        <!-- Add driver modal-->
        <mutation-modal ref="addDriverModal" @ok="addDriver" :modalSchema="modalSchemaAddDriver" />
    </div>
</template>

<script>
    import { RECRUITMENT_AGENCY_DRIVERS_QUERY, AVAILABLE_GARAGES_QUERY } from "@/graphql/queries/user";
    import { SearchForm, Pagination, MutationModal } from "@/components";
    import { CREATE_DRIVER_MUTATION } from "@/graphql/mutations/user";
    import { ADRS_QUERY, PREFERRED_ROAD_TRIPS_QUERY } from "@/graphql/queries/common";

    export default {
        title () {
            return this.$t('pages.recruitmentAgencyDrivers');
        },
        name: "RecruitmentAgencyDrivers",
        components: {
            SearchForm,
            Pagination,
            MutationModal
        },
        data() {
            return {
                recruitmentAgencyDrivers: {
                    data: [],
                    per_page: 6,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                page: 1,
                firstLoad: true,
                ADRsOptions: [],
                ADRs: [],
                preferredRoadTripsOptions: [],
                preferredRoadTrips: [],
                availableGarages: {
                    data: [],
                },
                searchModel: {
                    salary: {
                        type: 'range',
                        min: '',
                        max: ''
                    },
                    adr: [],
                    preferred_road_trips: [],
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
                                    name: 'salary',
                                    labelFrom: this.$t('driver.property.salary') + ' ' + this.$t('search.from'),
                                    labelTo: this.$t('driver.property.salary') + ' ' + this.$t('search.to'),
                                    value: {
                                        min: '',
                                        max: ''
                                    }
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'adr',
                                    label: this.$t('driver.property.adr'),
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
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'preferred_road_trips',
                                    label: this.$t('driver.property.preferred_road_trips'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option;
                                        },
                                        translatableLabel: 'preferred_road_trips.',
                                        optionLabel: (option) => {
                                            return option;
                                        },
                                        multiple: true
                                    }
                                }
                            ]
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
                                            { id: 'salary_asc', name: this.$t('driver.searchFields.salary_asc') },
                                            { id: 'salary_desc', name: this.$t('driver.searchFields.salary_desc') },
                                            { id: 'adr_asc', name: this.$t('driver.searchFields.adr_asc') },
                                            { id: 'adr_desc', name: this.$t('driver.searchFields.adr_desc') },
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
                modalSchemaAddDriver: {
                    form: {
                        mutation: CREATE_DRIVER_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.driver'),
                    okBtnTitle: this.$t('modal.btn.hire'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
            }
        },
        methods: {
            addDriverModal(driver) {
                this.modalSchemaAddDriver.form.fields = [
                    {
                        input: 'staticText',
                        text: this.$t('driver.model') + ' ' + driver.first_name + ' ' + driver.last_name,
                        class: 'text-left mb-4'
                    },
                    {
                        label: this.$t('driver.property.garage'),
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
                        text: this.$options.filters.currency(driver.salary, ' ', 2, { thousandsSeparator: ' ' }) + ' ' + this.$t('driver.property.salaryUnit'),
                        class: 'text-right md-title'
                    },
                ];

                this.modalSchemaAddDriver.form.hiddenFields = [
                    {
                        name: 'driver',
                        value: driver.id
                    }
                ];

                this.$refs['addDriverModal'].openModal();
            },
            addDriver(response) {
                let driver = response.data.createDriver;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.driver', { modelName: driver.first_name + ' ' + driver.last_name, location: driver.garage.location.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.recruitmentAgencyDrivers.refresh();
            },
        },
        apollo: {
            recruitmentAgencyDrivers: {
                query: RECRUITMENT_AGENCY_DRIVERS_QUERY,
                variables() {
                    return {page: this.page, limit: this.recruitmentAgencyDrivers.per_page, filter: this.filters, sort: this.sort}
                },
                result({ data, loading, networkStatus }) {
                    this.firstLoad = false;
                }
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
            preferredRoadTrips: {
                query: PREFERRED_ROAD_TRIPS_QUERY,
                result({ data, loading, networkStatus }) {
                    this.preferredRoadTripsOptions = data.preferredRoadTrips;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[2].config, 'options', this.preferredRoadTripsOptions);
                    });
                },
            },
            availableGarages: {
                query: AVAILABLE_GARAGES_QUERY,
                variables() {
                    return { page: 1, limit: -1, type: 'driver' }
                },
            }
        }
    }
</script>

<style scoped>
    .price h4 {
        margin: 0;
    }
    .md-card-profile >>> .md-card-actions {
        border-top: 1px solid #ddd;
    }
    .md-card-profile >>> .md-card-actions {
        flex-direction: row;
    }
</style>
