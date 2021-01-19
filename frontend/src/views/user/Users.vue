<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.users.loading  && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25" v-for="index in 8" :key="index">
                <content-placeholders-heading />
                <content-placeholders-text :lines="4" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100 mb-3">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
            </div>
            <div class="md-layout-item md-size-100 mb-5">
                <md-button @click="addUserModal" class="md-success">
                    <md-icon>add</md-icon> {{ $t('user.register') }}
                </md-button>
            </div>
            <template v-if="users.data && users.data.length > 0">
                <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25 mb-4" v-for="(user, index) in users.data" :key="index">
                    <md-card class="md-card-profile" >
                        <div class="md-card-avatar">
                            <img class="img" :src="user.image ? user.image : avatarPlaceholder" />
                        </div>
                        <md-card-content>
                            <h6 class="category text-gray">{{ rolesTitle(user.roles) }}</h6>
                            <h4 class="card-title">{{ user.first_name }} {{ user.last_name }}</h4>
                            <md-button :to="{ name: 'user', params: { id: user.id }}" class="md-round md-success">
                                {{ $t('user.detail') }}
                            </md-button>
                        </md-card-content>
                    </md-card>
                </div>
                <div class="md-layout-item md-size-100 d-flex justify-space-between">
                    <p>
                        {{ $t('pagination.display', {from: users.from, to: users.to, total: users.total}) }}
                    </p>
                    <pagination class="pagination-no-border pagination-success"
                                v-model="page"
                                :per-page="users.per_page"
                                :total="users.total"></pagination>
                </div>
            </template>
            <template v-else>
                <div class="md-layout-item md-size-100 mb-5">
                    {{ $t('search.noResults') }}
                </div>
            </template>
        </template>

        <!-- Add user modal-->
        <mutation-modal ref="addUserModal" @ok="addUser" :modalSchema="modalSchemaAddUser" />
    </div>
</template>

<script>
    import { USERS_QUERY } from "@/graphql/queries/user";
    import { SearchForm, Pagination, MutationModal } from "@/components";
    import { ROLES_QUERY } from "@/graphql/queries/common";
    import { CREATE_USER_MUTATION } from "@/graphql/mutations/user"

    export default {
        title () {
            return this.$t('pages.users');
        },
        name: "Users",
        components: {
            SearchForm,
            Pagination,
            MutationModal
        },
        data() {
            return {
                users: {
                    data: [],
                    per_page: 8,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                roles: [],
                rolesOptions: [],
                filters: [],
                page: 1,
                firstLoad: true,
                avatarPlaceholder: "/img/default-avatar.png",
                modalSchemaAddUser: {
                    form: {
                        mutation: CREATE_USER_MUTATION,
                        fields: [],
                        hiddenFields: [],
                    },
                    modalTitle: this.$t('model.modal.title.add.user'),
                    okBtnTitle: this.$t('modal.btn.register'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                searchModel: {
                    first_name: '',
                    last_name: '',
                    roles: [],
                },
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'text',
                                    name: 'first_name',
                                    label: this.$t('user.property.first_name'),
                                    value: '',
                                    config: {}
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'text',
                                    input: 'text',
                                    name: 'last_name',
                                    label: this.$t('user.property.last_name'),
                                    value: '',
                                    config: {}
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-33'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'roles',
                                    label: this.$t('user.searchFields.roles'),
                                    value: [],
                                    config: {
                                        options: [],
                                        optionValue: (option) => {
                                            return option.id;
                                        },
                                        translatableLabel: 'role.',
                                        optionLabel: (option) => {
                                            return option.name;
                                        },
                                        multiple: true
                                    }
                                }
                            ]
                        }
                    ]
                },
            }
        },
        methods: {
            rolesTitle(roles) {
                return roles.map(role => this.$t('role.' + role.name).toUpperCase()).join(" / ");
            },
            addUserModal() {
                this.modalSchemaAddUser.form.fields = [
                    {
                        label: this.$t('user.property.first_name'),
                        rules: 'required',
                        name: 'first_name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('user.property.last_name'),
                        rules: 'required',
                        name: 'last_name',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('user.property.email'),
                        rules: 'required|email',
                        name: 'email',
                        input: 'text',
                        type: 'text',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('user.searchFields.roles'),
                        rules: 'required',
                        name: 'roles',
                        input: 'select',
                        type: 'select',
                        value: [],
                        config: {
                            options: this.rolesOptions,
                            optionValue: (option) => {
                                return option.id;
                            },
                            translatableLabel: 'role.',
                            optionLabel: (option) => {
                                return option.name;
                            },
                            multiple: true
                        }
                    },
                ];

                this.$refs['addUserModal'].openModal();
            },
            addUser(response) {
                let user = response.data.createUser;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.created.user', { modelName: user.first_name + ' ' + user.last_name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$apollo.queries.users.refresh();
            },
        },
        apollo: {
            users: {
                query: USERS_QUERY,
                variables() {
                    return { page: this.page, limit: this.users.per_page, filter: this.filters }
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            roles: {
                query: ROLES_QUERY,
                result({ data, loading, networkStatus }) {
                    this.rolesOptions = data.roles;
                    this.$nextTick( () => {
                        this.$set(this.searchSchema.groups[0].fields[2].config, 'options', this.rolesOptions);
                    });
                },
            }
        }
    }
</script>

<style scoped>
    .category {
        height: 36px;
    }
</style>
