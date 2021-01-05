<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.user.loading">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25" v-for="index in 8" :key="index">
                <content-placeholders-heading />
                <content-placeholders-text :lines="4" />
            </content-placeholders>
        </template>
        <template v-else-if="errorMessage">
            <div class="md-layout-item md-size-100">
                {{ errorMessage }}
            </div>
        </template>
        <template v-else>

        </template>
    </div>
</template>

<script>
    import { USER_QUERY } from "@/graphql/queries/user";

    export default {
        title () {
            return this.$t('pages.user');
        },
        name: "User",
        data() {
            return {
                user: null,
                routeId: this.$route.params.id
            }
        },
        apollo: {
            user: {
                query: USER_QUERY,
                variables() {
                    return {id: this.routeId}
                },
                error(error, vm, key, type, options) {
                    this.setErrorMessage(error);
                }
            },
        }
    }
</script>

<style scoped>

</style>
