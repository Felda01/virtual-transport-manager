<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.scoreBoard.loading && firstLoad">
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
            <content-placeholders class="md-layout-item md-size-100">
                <content-placeholders-heading />
                <content-placeholders-text :lines="15" />
            </content-placeholders>
        </template>
        <template v-else>
            <template v-if="scoreBoard && scoreBoard.length > 0 && user">
                <div class="md-layout-item md-size-100">
                    <md-card>
                        <md-card-content class="pb-0">
                            <md-table v-model="scoreBoard" v-if="scoreBoard">
                                <md-table-row slot="md-table-row" slot-scope="{ item, index }">
                                    <md-table-cell md-label="#" :class="{'current-company': item.id === user.company.id}">{{ index + 1 }}</md-table-cell>
                                    <md-table-cell :md-label="$t('company.property.name')" :class="{'current-company': item.id === user.company.id}">{{ item.name }}</md-table-cell>
                                    <md-table-cell :md-label="$t('company.property.value')" :class="{'current-company': item.id === user.company.id}">{{ item.value | currency(' ', 2, { thousandsSeparator: ' ' }) }} {{ $t('company.property.valueUnit') }}</md-table-cell>
                                </md-table-row>
                            </md-table>
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
    import { Pagination } from "@/components";
    import { SCOREBOARD_QUERY } from "@/graphql/queries/user";
    import { mapGetters } from "vuex";

    export default {
        title () {
            return this.$t('pages.scoreBoard');
        },
        name: "ScoreBoard",
        components: {
            Pagination,
        },
        computed: {
            ...mapGetters([
                'user'
            ]),
        },
        data() {
            return {
                scoreBoard: [],
                page: 1,
                firstLoad: true,
            }
        },
        apollo: {
            scoreBoard: {
                query: SCOREBOARD_QUERY,
                result({data, loading, networkStatus}) {
                    this.firstLoad = false;
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .current-company {
        color: #4caf50;
        font-size: 20px;
    }
</style>
