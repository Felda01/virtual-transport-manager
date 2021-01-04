<template>
    <div class="md-layout">
        <template v-if="$apollo.queries.adminDashboard.loading">
            <content-placeholders class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25" v-for="index in 8" :key="index">
                <content-placeholders-heading />
                <content-placeholders-text :lines="2" />
            </content-placeholders>
        </template>
        <template v-else>
            <div class="md-layout-item md-medium-size-50 md-xsmall-size-100 md-size-25" v-for="(item, index) in adminDashboard" :key="index">
                <stats-card header-color="green">
                    <template slot="header">
                        <div class="card-icon">
                            <md-icon>{{ adminDashboardIcons[index] }}</md-icon>
                        </div>
                        <p class="category">{{ adminDashboardTitles[index] }}</p>
                        <h3 class="title">
                            <animated-number :value="item"></animated-number>
                        </h3>
                    </template>
                </stats-card>
            </div>
        </template>
    </div>
</template>

<script>
    import { StatsCard, AnimatedNumber } from "@/components";
    import { ADMIN_DASHBOARD_QUERY } from '@/graphql/queries/admin';

    export default {
        title () {
            return this.$t('pages.adminDashboard');
        },
        name: "Dashboard",
        components: {
            StatsCard,
            AnimatedNumber
        },
        data() {
            return {
                adminDashboard: [],
                adminDashboardTitles: [
                    this.$t('adminDashboard.countries'),
                    this.$t('adminDashboard.locations'),
                    this.$t('adminDashboard.routes'),
                    this.$t('adminDashboard.bankLoanTypes'),
                    this.$t('adminDashboard.garageModels'),
                    this.$t('adminDashboard.trailerModels'),
                    this.$t('adminDashboard.truckModels'),
                    this.$t('adminDashboard.cargos')
                ],
                adminDashboardIcons: [
                    'equalizer',
                    'equalizer',
                    'equalizer',
                    'equalizer',
                    'equalizer',
                    'equalizer',
                    'equalizer',
                    'equalizer',
                ]
            }
        },
        apollo: {
            adminDashboard: {
                query: ADMIN_DASHBOARD_QUERY,
            }
        }
    }
</script>
