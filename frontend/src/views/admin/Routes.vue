<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <div class="title">
                        <h4>{{ $t('pages.routes') }}</h4>
                        <md-button class="md-primary md-simple" @click="addRouteModal"><md-icon>add</md-icon>{{ $t('model.new') }}</md-button>
                    </div>
                </md-card-header>
                <md-card-content class="pb-0">
                    <template v-if="$apollo.queries.routes.loading">
                        <content-placeholders class="mb-4">
                            <content-placeholders-heading />
                            <content-placeholders-text :lines="10" />
                        </content-placeholders>
                    </template>
                    <template v-else>
                        <md-table v-model="routes.data" v-if="routes && routes.data" style="flex-flow:nowrap; overflow:scroll;">
                            <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                <md-table-cell md-label="#">{{ index + routes.from }}</md-table-cell>
                                <md-table-cell :md-label="$t('route.property.location1')">{{ item.location1.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('route.property.location2')">{{ item.location2.name }}</md-table-cell>
                                <md-table-cell :md-label="$t('route.property.distance')">{{ item.distance | currency(' ', 0, { thousandsSeparator: ' ' }) }} {{ $t('route.property.distanceUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('route.property.time')">{{ item.time }} {{ $t('route.property.timeUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('route.property.fee')">{{ item.fee | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('route.property.feeUnit') }}</md-table-cell>
                                <md-table-cell :md-label="$t('route.property.type')">{{ item.type }}</md-table-cell>
                                <md-table-cell :md-label="$t('model.actions')" class="text-right">
                                    <md-button class="md-just-icon md-success md-simple" @click="updateRouteModal(item)"><md-icon>edit</md-icon></md-button>
                                    <md-button class="md-just-icon md-danger md-simple" @click="deleteRouteModal(item)"><md-icon>close</md-icon></md-button>
                                </md-table-cell>
                            </md-table-row>
                        </md-table>
                    </template>
                </md-card-content>
                <md-card-actions md-alignment="space-between">
                    <div class="">
                        <p class="card-category">
                            {{ $t('pagination.display', {from: routes.from, to: routes.to, total: routes.total}) }}
                        </p>
                    </div>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="routes.per_page"
                                :total="routes.total"></pagination>
                </md-card-actions>
            </md-card>
        </div>

        <!-- Add route modal-->
        <mutation-modal ref="addRouteModal" @ok="addRoute" :modalSchema="modalSchemaAddRoute" />

        <!-- Update route modal-->
        <mutation-modal ref="updateRouteModal" @ok="updateRoute" :modalSchema="modalSchemaUpdateRoute" />

        <!-- Delete route modal-->
        <delete-modal ref="deleteRouteModal" @ok="deleteRoute" :modalSchema="modalSchemaDeleteRoute" />
    </div>
</template>

<script>
    import { ROUTES_QUERY, LOCATIONS_QUERY } from '@/graphql/queries/admin';
    import { CREATE_ROUTE_MUTATION, UPDATE_ROUTE_MUTATION, DELETE_ROUTE_MUTATION } from '@/graphql/mutations/admin';
    import { MutationModal, Pagination, DeleteModal } from "@/components";
    import { LOCALES_QUERY } from "../../graphql/queries/common";

    export default {
        name: "Routes",
        components: {
            MutationModal,
            Pagination,
            DeleteModal
        },
        data() {
            return {
                routes: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                },
                locations: {
                    data: [],
                },
                page: 1,
                modalSchemaAddRoute: {
                    form: {
                        mutation: CREATE_ROUTE_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add', { model: 'route' }),
                    okBtnTitle: this.$t('modal.btn.add'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateRoute: {
                    form: {
                        mutation: UPDATE_ROUTE_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update', { model: 'route' }),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaDeleteRoute: {
                    message: this.$t('model.modal.message', { model: 'route' }),
                    form: {
                        mutation: DELETE_ROUTE_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.delete'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                }
            }
        },
        methods: {
            addRouteModal() {
                this.modalSchemaAddRoute.form.fields = [
                    {
                        label: this.$t('route.property.location1'),
                        rules: 'required|different:@Location 2,Location 2',
                        name: 'location1',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.locations.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.name;
                            }
                        }
                    },
                    {
                        label: this.$t('route.property.location2'),
                        rules: 'required|different:@Location 1,Location 1',
                        name: 'location2',
                        input: 'select',
                        type: 'select',
                        value: '',
                        config: {
                            options: this.locations.data,
                            optionValue: (option) => {
                                return option.id;
                            },
                            optionLabel: (option) => {
                                return option.name;
                            }
                        }
                    },
                    {
                        label: this.$t('route.property.distance'),
                        rules: '',
                        name: 'distance',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('route.additionalLabelText.location')
                        }
                    },
                    {
                        label: this.$t('route.property.time'),
                        rules: '',
                        name: 'time',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('route.additionalLabelText.time')
                        }
                    },
                    {
                        label: this.$t('route.property.fee'),
                        rules: 'required',
                        name: 'fee',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {
                            labelAdditionalText: this.$t('route.additionalLabelText.fee')
                        }
                    },
                ];

                this.$refs['addRouteModal'].openModal();
            },
            addRoute(response) {
                let route = response.data.createRoute;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created', { model: 'route', modelName: route.location1.name + ' - ' +  route.location2.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.routes.refresh();
            },
            updateRouteModal(route) {
                this.modalSchemaUpdateRoute.form.fields = [
                    {
                        label: this.$t('route.property.location1'),
                        rules: '',
                        name: 'location1',
                        input: 'text',
                        type: 'text',
                        value: route.location1.name,
                        config: {
                            readOnly: true
                        }
                    },
                    {
                        label: this.$t('route.property.location2'),
                        rules: '',
                        name: 'location2',
                        input: 'text',
                        type: 'text',
                        value: route.location2.name,
                        config: {
                            readOnly: true
                        }
                    },
                    {
                        label: this.$t('route.property.distance'),
                        rules: 'required',
                        name: 'distance',
                        input: 'text',
                        type: 'text',
                        value: route.distance,
                        config: {}
                    },
                    {
                        label: this.$t('route.property.time'),
                        rules: 'required',
                        name: 'time',
                        input: 'text',
                        type: 'text',
                        value: route.time,
                        config: {
                            labelAdditionalText: this.$t('route.additionalLabelText.timeUpdate')
                        }
                    },
                    {
                        label: this.$t('route.property.fee'),
                        rules: 'required',
                        name: 'fee',
                        input: 'text',
                        type: 'text',
                        value: route.fee,
                        config: {
                            labelAdditionalText: this.$t('route.additionalLabelText.fee')
                        }
                    },
                ];

                this.modalSchemaUpdateRoute.form.idField = route.id;

                this.$refs['updateRouteModal'].openModal();
            },
            updateRoute(response) {
                let route = response.data.updateRoute;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated', { model: 'route', modelName: route.location1.name + ' - ' +  route.location2.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.routes.refresh();
            },
            deleteRouteModal(route) {
                this.modalSchemaDeleteRoute.form.idField = route.id;

                this.$refs['deleteRouteModal'].openModal();
            },
            deleteRoute(response) {
                let route = response.data.deleteRoute;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted', { model: 'route', modelName: route.location1.name + ' - ' +  route.location2.name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.routes.refresh();
            }
        },
        apollo: {
            routes: {
                query: ROUTES_QUERY,
                variables() {
                    return { page: this.page, limit: this.routes.per_page }
                }
            },
            locations: {
                query: LOCATIONS_QUERY,
                variables() {
                    return { page: 1, limit: -1 }
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
    .md-table .md-table-head:last-child {
        text-align: right;
    }
</style>
