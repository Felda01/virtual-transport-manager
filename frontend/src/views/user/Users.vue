<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.users.loading">
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
            <div class="md-layout-item md-size-100 mb-5">
                <search-form :search-schema="searchSchema" v-model="searchModel"></search-form>
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
                            <md-button class="md-round md-success">
                                {{ $t('user.detail') }}
                            </md-button>
                        </md-card-content>
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
    import { USERS_QUERY } from "@/graphql/queries/user";
    import { SearchForm } from "@/components";
    import { ROLES_QUERY } from "@/graphql/queries/common";

    export default {
        title () {
            return this.$t('pages.users');
        },
        name: "Users",
        components: {
            SearchForm
        },
        data() {
            return {
                users: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                roles: [],
                rolesOptions: [],
                filters: [],
                page: 1,
                avatarPlaceholder: "/img/default-avatar.png",
                searchModel: {
                    first_name: '',
                    last_name: '',
                    roles: [''],
                },
                searchSchema: {
                    groups: [
                        {
                            class: [''],
                            fields: [
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'text',
                                    input: 'text',
                                    name: 'first_name',
                                    label: this.$t('user.property.first_name'),
                                    value: '',
                                    config: {}
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'text',
                                    input: 'text',
                                    name: 'last_name',
                                    label: this.$t('user.property.last_name'),
                                    value: '',
                                    config: {}
                                },
                                {
                                    class: ['md-medium-size-50', 'md-xsmall-size-100' ,'md-size-25'],
                                    type: 'select',
                                    input: 'select',
                                    name: 'roles',
                                    label: this.$t('user.searchFields.roles'),
                                    value: [''],
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
            applySearch() {
                this.filters = this.processSearch(this.searchModel);
            },
        },
        created() {
            this.debounceFunction = this._.debounce(this.applySearch, 1000);
        },
        watch: {
            searchModel: {
                handler: function (val, oldVal) {
                    this.debounceFunction();
                },
                deep: true
            }
        },
        apollo: {
            users: {
                query: USERS_QUERY,
                variables() {
                    return { page: this.page, limit: this.users.per_page, filter: this.filters }
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
