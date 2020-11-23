<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-100">
            <md-card>
                <md-card-header class="md-card-header-icon md-card-header-green">
                    <div class="card-icon">
                        <md-icon>satellite</md-icon>
                    </div>
                    <h4 class="title">{{ $t('pages.countries') }}</h4>
                </md-card-header>
                <md-card-content>
                    <md-table v-model="countries" v-if="countries">
                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                            <md-table-cell md-label="#">{{ item.id }}</md-table-cell>
                            <md-table-cell md-label="Name">{{ item.name }}</md-table-cell>
                            <md-table-cell md-label="Country">{{
                                item.country
                                }}</md-table-cell>
                            <md-table-cell md-label="City">{{ item.city }}</md-table-cell>
                            <md-table-cell md-label="Salary">{{ item.salary }}</md-table-cell>
                            <md-table-cell md-label="Actions" :class="getAlignClasses(item)">
                                <md-button
                                        class="md-just-icon"
                                        :class="getClass(item.icon1, item.id)"
                                ><md-icon>{{ item.icon1 }}</md-icon></md-button
                                >
                                <md-button
                                        class="md-just-icon"
                                        :class="getClass(item.icon2, item.id)"
                                ><md-icon>{{ item.icon2 }}</md-icon></md-button
                                >
                                <md-button
                                        class="md-just-icon"
                                        :class="getClass(item.icon3, item.id)"
                                ><md-icon>{{ item.icon3 }}</md-icon></md-button
                                >
                            </md-table-cell>
                        </md-table-row>
                    </md-table>
                </md-card-content>
            </md-card>
        </div>
    </div>
</template>

<script>
    import { COUNTRIES_QUERY } from '@/graphql/queries/admin';

    export default {
        title () {
            return this.$t('pages.countries');
        },
        name: "Countries",
        data() {
            return {
                countries: [],
                page: 0,
            }
        },
        apollo: {
            countries: {
                query: COUNTRIES_QUERY,
                variables() {
                    return {page: this.page, limit: 10}
                }
            },
        },
    }
</script>

<style scoped>

</style>
