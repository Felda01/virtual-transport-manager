<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.user.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else-if="errorMessage">
            <div class="md-layout-item md-size-100">
                {{ errorMessage }}
            </div>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <tabs
                        :tab-name="[$t('user.subNav.info'), $t('truck.subNav.activity')]"
                        class="page-subcategories"
                        plain
                        :tab-content-center="true"
                        :tab-content-full-width="true"
                        color-button="success"
                >
                    <!-- here you can add your content for tab-content -->
                    <template slot="tab-pane-1">
                        <md-card>
                            <md-card-header>
                                <h4 class="title">{{ $t('user.subNav.info') }}</h4>
                            </md-card-header>
                            <md-card-content class="pb-0">
                                <md-table v-model="userTable" v-if="userTable">
                                    <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                        <md-table-cell md-label="">
                                            <div class="img-container table-profile-image">
                                                <img :src="user.image ? user.image : avatarPlaceholder" :alt="item.first_name + ' ' + item.last_name" />
                                            </div>
                                        </md-table-cell>
                                        <md-table-cell :md-label="$t('user.property.first_name')" class="td-name">{{ item.first_name }}</md-table-cell>
                                        <md-table-cell :md-label="$t('user.property.last_name')" class="td-name">{{ item.last_name }}</md-table-cell>
                                        <md-table-cell v-if="canShowSalary" :md-label="$t('user.property.salary')">{{ item.salary | currency(' ', 0, { thousandsSeparator: ' ' }) }}  {{ $t('user.property.salaryUnit') }}</md-table-cell>
                                        <md-table-cell :md-label="$t('user.searchFields.roles')">{{ rolesTitle(item.roles) }}</md-table-cell>
                                    </md-table-row>
                                </md-table>
                            </md-card-content>
                            <md-card-actions md-alignment="space-between">
                                <template v-if="currentUser.id === user.id">
                                    <md-button class="md-success md-simple" @click="updateUserModal"><md-icon>edit</md-icon>{{ $t('detail.btn.updateProfile') }}</md-button>
                                    <md-button class="md-success md-simple" @click="updateUserPasswordModal"><md-icon>edit</md-icon>{{ $t('detail.btn.updatePassword') }}</md-button>
                                </template>
                                <template v-if="hasPermission(constants.PERMISSION.MANAGE_SALARY)">
                                    <md-button class="md-success md-simple" @click="updateUserSalaryModal"><md-icon>edit</md-icon>{{ $t('detail.btn.updateSalary') }}</md-button>
                                </template>
                                <template v-if="hasPermission(constants.PERMISSION.MANAGE_PERSONS) && currentUser.id !== user.id">
                                    <md-button class="md-danger md-simple ml-auto" @click="deleteUserModal"><md-icon>close</md-icon>{{ $t('detail.btn.fire') }}</md-button>
                                </template>
                            </md-card-actions>
                        </md-card>
                    </template>
<!--                    <template slot="tab-pane-2">-->
<!--                        <md-card>-->
<!--                            <md-card-header>-->
<!--                                <h4 class="title">{{ $t('truck.subNav.drivers') }}</h4>-->
<!--                            </md-card-header>-->
<!--                            <md-card-content>-->
<!--                                <md-table v-model="user.activities.data" v-if="user.activities">-->
<!--                                    <md-table-row v-if="user.activities && truck.drivers.length > 0" slot="md-table-row" slot-scope="{ item, index }" @click.native="clickTableRow(item, 'driver')" class="cursor-pointer-hover">-->
<!--                                        <md-table-cell md-label="#">{{ index + 1 }}</md-table-cell>-->
<!--                                        <md-table-cell md-label="">-->
<!--                                            <div class="img-container table-profile-image">-->
<!--                                                <img :src="item.image" :alt="item.first_name + ' ' + item.last_name" />-->
<!--                                            </div>-->
<!--                                        </md-table-cell>-->
<!--                                        <md-table-cell :md-label="$t('driver.property.full_name')" class="td-name">{{ driver(item) }}</md-table-cell>-->
<!--                                        <md-table-cell :md-label="$t('driver.property.status')">{{ $t('status.' + item.status) }}</md-table-cell>-->
<!--                                        <md-table-cell :md-label="$t('driver.property.location')">{{ item.location.name }} ({{ item.location.country.short_name | uppercase }})</md-table-cell>-->
<!--                                        <md-table-cell :md-label="$t('driver.property.adr')">{{ $t('ADRsShort.' + item.adr) }}</md-table-cell>-->
<!--                                        <md-table-cell md-label="" v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES) && hasPermission(constants.PERMISSION.MANAGE_DRIVERS)">-->
<!--                                            <md-button class="md-danger md-simple md-full-text" @click.native.stop="unassignDriverFromTruckModal(item)"><md-icon>close</md-icon>{{ $t('detail.btn.unassign')}}</md-button>-->
<!--                                        </md-table-cell>-->
<!--                                    </md-table-row>-->
<!--                                    <md-table-empty-state>-->
<!--                                        {{ $t('truck.relations.no_drivers') }}-->
<!--                                    </md-table-empty-state>-->
<!--                                </md-table>-->
<!--                                <template v-if="hasPermission(constants.PERMISSION.MANAGE_VEHICLES) && hasPermission(constants.PERMISSION.MANAGE_DRIVERS)">-->
<!--                                    <div class="text-center mt-3" v-if="!truck.drivers || truck.drivers.length < 2">-->
<!--                                        <template v-if="this.availableDriversInGarage && this.availableDriversInGarage.data && this.availableDriversInGarage.data.length > 0">-->
<!--                                            <md-button class="md-success md-simple" @click="assignDriverToTruckModal"><md-icon>add</md-icon>{{ $t('detail.btn.assign') }}</md-button>-->
<!--                                        </template>-->
<!--                                        <template v-else>-->
<!--                                            {{ $t('truck.relations.no_available_drivers') }}-->
<!--                                        </template>-->
<!--                                    </div>-->
<!--                                </template>-->
<!--                            </md-card-content>-->
<!--                        </md-card>-->
<!--                    </template>-->
                </tabs>
            </div>
        </template>

        <template v-if="user">
            <template v-if="currentUser.id === user.id">
                <!-- Update user modal-->
                <mutation-modal ref="updateUserModal" @ok="updateUser" :modalSchema="modalSchemaUpdateUser" />

                <!-- Update user password modal-->
                <mutation-modal ref="updateUserPasswordModal" @ok="updateUserPassword" :modalSchema="modalSchemaUpdateUserPassword" />
            </template>

            <template v-if="hasPermission(constants.PERMISSION.MANAGE_SALARY)">
                <!-- Update user salary / roles modal-->
                <mutation-modal ref="updateUserSalaryModal" @ok="updateUserSalary" :modalSchema="modalSchemaUpdateUserSalary" />
            </template>

            <template v-if="hasPermission(constants.PERMISSION.MANAGE_PERSONS) && currentUser.id !== user.id">
                <!-- Delete user modal-->
                <delete-modal ref="deleteUserModal" @ok="deleteUser" :modalSchema="modalSchemaDeleteUser" />
            </template>
        </template>
    </div>
</template>

<script>
    import { USER_QUERY } from "@/graphql/queries/user";
    import { ROLES_QUERY } from "@/graphql/queries/common";
    import { DELETE_USER_MUTATION, UPDATE_USER_PASSWORD_MUTATION, UPDATE_USER_MUTATION, UPDATE_USER_SALARY_MUTATION } from "@/graphql/mutations/user";
    import { Tabs, ProductCard, MutationModal, DeleteModal } from "@/components";
    import constants from "../../constants";
    import { mapGetters } from "vuex";

    export default {
        title () {
            return this.$t('pages.user');
        },
        name: "User",
        components: {
            Tabs,
            ProductCard,
            MutationModal,
            DeleteModal
        },
        computed: {
            ...mapGetters({
                hasPermission: 'hasPermission',
                currentUser: 'user'
            }),
            userTable() {
                return this.user ? [this.user] : [];
            },
            canShowSalary() {
                return this.user ? this.currentUser.id === this.user.id || this.hasPermission(constants.PERMISSION.MANAGE_SALARY) : false
            }
        },
        data() {
            return {
                user: null,
                id: this.$route.params.id,
                firstLoad: true,
                roles: [],
                modalSchemaDeleteUser: {
                    message: '',
                    form: {
                        mutation: DELETE_USER_MUTATION,
                        idField: null,
                    },
                    okBtnTitle: this.$t('modal.btn.fire'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateUserPassword: {
                    form: {
                        mutation: UPDATE_USER_PASSWORD_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.userPassword'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateUserSalary: {
                    form: {
                        mutation: UPDATE_USER_SALARY_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.userSalary'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                modalSchemaUpdateUser: {
                    form: {
                        mutation: UPDATE_USER_MUTATION,
                        fields: [],
                        hiddenFields: [],
                        idField: null
                    },
                    modalTitle: this.$t('model.modal.title.update.user'),
                    okBtnTitle: this.$t('modal.btn.update'),
                    cancelBtnTitle: this.$t('modal.btn.cancel')
                },
                avatarPlaceholder: "/img/default-avatar.png",
                constants: constants
            }
        },
        methods: {
            rolesTitle(roles) {
                return roles.map(role => this.$t('role.' + role.name)).join(", ");
            },
            deleteUserModal() {
                this.modalSchemaDeleteUser.message = this.$t('model.modal.title.delete.user');

                this.modalSchemaDeleteUser.form.idField = this.id;

                this.$refs['deleteUserModal'].openModal();
            },
            deleteUser(response) {
                let user = response.data.deleteUser;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.deleted.user', { modelName: user.first_name + " " + user.last_name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
                this.$router.push({
                    name: 'users',
                    params: {locale: this.$i18n.locale}
                });
            },
            updateUserPasswordModal() {
                this.modalSchemaUpdateUserPassword.form.fields = [
                    {
                        label: this.$t('user.property.password'),
                        rules: 'required',
                        name: 'password',
                        input: 'text',
                        type: 'password',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('user.property.new_password'),
                        rules: 'required|min:8',
                        name: 'new_password',
                        input: 'text',
                        type: 'password',
                        value: '',
                        config: {}
                    },
                    {
                        label: this.$t('user.property.new_password_confirmation'),
                        rules: 'required|password:@' + this.$t('user.property.new_password'),
                        name: 'new_password_confirmation',
                        input: 'text',
                        type: 'password',
                        value: '',
                        config: {}
                    }
                ];

                this.modalSchemaUpdateUserPassword.form.idField = this.id;

                this.$refs['updateUserPasswordModal'].openModal();
            },
            updateUserPassword(response) {
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.userPassword'),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });
            },
            updateUserModal() {
                this.modalSchemaUpdateUser.form.fields = [
                    {
                        label: this.$t('user.property.first_name'),
                        rules: 'required',
                        name: 'first_name',
                        input: 'text',
                        type: 'text',
                        value: this.user.first_name,
                        config: {}
                    },
                    {
                        label: this.$t('user.property.last_name'),
                        rules: 'required',
                        name: 'last_name',
                        input: 'text',
                        type: 'text',
                        value: this.user.last_name,
                        config: {}
                    },
                    {
                        label: this.$t('user.property.email'),
                        rules: 'required|email',
                        name: 'email',
                        input: 'text',
                        type: 'text',
                        value: this.user.email,
                        config: {}
                    },
                    {
                        label: this.$t('user.property.image'),
                        rules: '',
                        name: 'image',
                        input: 'image',
                        type: 'text',
                        value: this.user.image,
                        config: {}
                    },
                ];

                this.modalSchemaUpdateUser.form.idField = this.id;

                this.$refs['updateUserModal'].openModal();
            },
            updateUser(response) {
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.user'),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.user.refresh();
            },
            updateUserSalaryModal() {
                this.modalSchemaUpdateUserSalary.form.fields = [
                    {
                        label: this.$t('user.property.salary'),
                        rules: 'required|min:0',
                        name: 'salary',
                        input: 'text',
                        type: 'text',
                        value: this.user.salary,
                        config: {}
                    },
                    {
                        label: this.$t('user.searchFields.roles'),
                        rules: 'required',
                        name: 'roles',
                        input: 'select',
                        type: 'select',
                        value: this._.map(this.user.roles, 'id'),
                        config: {
                            options: this.roles,
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

                this.modalSchemaUpdateUserSalary.form.idField = this.id;

                this.$refs['updateUserSalaryModal'].openModal();
            },
            updateUserSalary(response) {
                let user = response.data.updateUserSalary;
                this.$notify({
                    timeout: 5000,
                    message: this.$t('model.response.success.updated.userSalary', { modelName: user.first_name + " " + user.last_name }),
                    icon: "add_alert",
                    horizontalAlign: 'right',
                    verticalAlign: 'top',
                    type: 'success'
                });

                this.$apollo.queries.user.refresh();
            }
        },
        apollo: {
            user: {
                query: USER_QUERY,
                variables() {
                    return {id: this.id}
                },
                error(error, vm, key, type, options) {
                    this.setErrorMessage(error);
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
            roles: {
                query: ROLES_QUERY,
            }
        }
    }
</script>

<style scoped>

</style>
