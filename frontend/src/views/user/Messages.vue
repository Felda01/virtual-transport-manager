<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.users.loading && firstLoad">
            <content-placeholders class="md-layout-item md-small-size-100 md-size-33">
                <content-placeholders-heading />
                <content-placeholders-text :lines="10" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-small-size-100 md-size-66">
                <content-placeholders-heading />
                <content-placeholders-text :lines="10" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-size-100">
                <md-app>
                    <md-app-toolbar class="md-primary">
                        <md-button class="md-just-icon md-primary" @click="toggleMenu" v-if="!menuVisible">
                            <md-icon>menu</md-icon>
                        </md-button>
                        <span class="md-title"><template v-if="selectedUser">{{ selectedUser.first_name }} {{ selectedUser.last_name }}</template></span>
                    </md-app-toolbar>

                    <md-app-drawer :md-active.sync="menuVisible" md-persistent="full">
                        <md-toolbar class="md-transparent" md-elevation="0">
                            <span>{{ $t('message.chats') }}</span>

                            <div class="md-toolbar-section-end">
                                <md-button class="md-just-icon md-simple" @click="toggleMenu">
                                    <md-icon>keyboard_arrow_left</md-icon>
                                </md-button>
                            </div>
                        </md-toolbar>

                        <md-list v-if="users && users.data && users.data.length > 0">
                            <md-list-item @click="selectConversation(user)" v-for="(user,index) in users.data" :key="index" >
                                <md-avatar>
                                    <img :src="user.image ? user.image : avatarPlaceholder" :alt="user.first_name + ' ' + user.last_name">
                                </md-avatar>

                                <span class="md-list-item-text position-relative">{{ user.first_name }} {{ user.last_name }}</span>
                            </md-list-item>
                        </md-list>
                    </md-app-drawer>

                    <md-app-content class="min-content-width messages-wrapper">
                        <div class="spinner-center" v-if="!selectedUser || ($apollo.queries.conversation.loading && conversationFirstLoad)">
                            <md-progress-spinner md-mode="indeterminate"></md-progress-spinner>
                        </div>
                        <template v-else>
                            <div class="messages-content" v-chat-scroll>
                                <div v-for="(message, index) in reversedMessages" class="message" :class="{'message-out': message.userFrom.id === user.id, 'message-in': message.userFrom.id !== user.id, 'mt-auto': index === 0 }">
                                    {{ message.message }}
                                </div>
                            </div>
                            <div class="messages-footer">
                                <md-field :mdClearable="true">
                                    <label>{{ $t('message.property.message') }}</label>
                                    <md-input v-model="form.message" type="text" v-on:keyup.enter="createMessageClick"></md-input>
                                </md-field>
                            </div>
                        </template>
                    </md-app-content>
                </md-app>
            </div>
        </template>
    </div>
</template>

<script>
    import { USERS_QUERY, CONVERSATION_QUERY } from "@/graphql/queries/user";
    import { CREATE_MESSAGE_MUTATION } from '@/graphql/mutations/user';
    import { mapGetters } from "vuex";

    export default {
        name: "Messages",
        data() {
            return {
                menuVisible: true,
                selectedConversation: 0,
                users: {
                    data: [],
                    per_page: 10,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                conversation: {
                    data: [],
                    per_page: 100,
                    current_page: 1,
                    from: 0,
                    to: 0
                },
                userPage: 1,
                conversationPage: 1,
                conversationFirstLoad: true,
                firstLoad: true,
                avatarPlaceholder: "/img/default-avatar.png",
                selectedUser: null,
                form: {
                    message: '',
                }
            }
        },
        computed: {
            ...mapGetters([
                'user'
            ]),
            reversedMessages() {
                return this.conversation.data ? this.conversation.data.reverse() : [];
            }
        },
        methods: {
            toggleMenu() {
                this.menuVisible = !this.menuVisible;
            },
            selectConversation(user) {
                this.selectedUser = user;
                this.conversationFirstLoad = true;
                this.form.message = '';
            },
            createMessageClick() {
                if (this.form.message) {
                    this.form.user1 = this.user.id;
                    this.form.user2 = this.selectedUser.id;

                    this.$apollo.mutate({
                        mutation: CREATE_MESSAGE_MUTATION,
                        variables: this.form
                    }).then(response => {
                        this.$apollo.queries.conversation.refresh();
                        this.form.message = '';
                    });
                }
            }
        },
        apollo: {
            users: {
                query: USERS_QUERY,
                variables() {
                    return { page: this.userPage, limit: this.users.per_page, filter: [] }
                },
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                    this.selectedUser = data.users.data[0];
                }
            },
            conversation: {
                query: CONVERSATION_QUERY,
                variables() {
                    return { page: this.conversationPage, limit: this.conversation.per_page, user1: this.user.id, user2: this.selectedUser.id }
                },
                result({data, loading, networkStatus}) {
                    this.conversationFirstLoad = false;
                },
                skip() {
                    return !this.selectedUser;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .md-app {
        min-height: calc(100vh - 155px);
        border: 1px solid rgba(#000, 0.12);
    }
    .min-content-width {
        min-width: 328px;
    }
    .position-relative {
        position: relative;
    }
    .spinner-center {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
    }
    .messages-footer {
        display: flex;
        flex-direction: row;
    }
    .messages-content {
        padding: 1em;
        overflow: auto;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .messages-wrapper {
        display: flex;
        flex-direction: column;
        position: relative;
        max-height: calc(100vh - 185px);
    }
    .message {
        border-radius: 10px;
        padding: .5em;
        margin-bottom: .5em;
        max-width: 60%;
    }
    .message-out {
        background: #407FFF;
        color: white;
        margin-left: auto;
    }
    .message-in {
        background: #F1F0F0;
        color: black;
        margin-right: auto;
    }
    .mt-auto {
        margin-top: auto;
    }
</style>
