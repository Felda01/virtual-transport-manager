<template>
    <div class="md-layout text-center">
        <div class="md-layout-item md-size-50 mb-5">
            <h1 class="md-display-1">{{ $t('dashboard.title') }}</h1>
            <p class="md-subheading">{{ $t('dashboard.text') }}</p>
        </div>
        <div class="md-layout-item md-size-100">
            <div class="md-layout">
                <template v-if="firstLoad">
                    <content-placeholders class="md-layout-item md-size-50 md-medium-size-100">
                        <content-placeholders-heading />
                        <content-placeholders-text :lines="15" />
                    </content-placeholders>
                    <content-placeholders class="md-layout-item md-size-50 md-medium-size-100">
                        <content-placeholders-heading />
                        <content-placeholders-text :lines="15" />
                    </content-placeholders>
                </template>
                <template v-else>
                    <div class="md-layout-item md-size-50 md-medium-size-100">
                        <chart-card
                                header-animation="false"
                                :chart-data="orderCountChartData"
                                :chart-options="orderCountChart.options"
                                chart-type="Line"
                                header-icon
                                chart-inside-content
                                no-footer
                                background-color="green"
                        >
                            <template slot="chartInsideHeader">
                                <div class="card-icon">
                                    <md-icon>timeline</md-icon>
                                </div>
                                <h4 class="title">
                                    {{ $t('dashboard.orderCount') }}
                                </h4>
                            </template>
                        </chart-card>
                    </div>
                    <div class="md-layout-item md-size-50 md-medium-size-100">
                        <chart-card
                                header-animation="false"
                                :chart-data="orderSumChartData"
                                :chart-options="orderSumChart.options"
                                chart-type="Line"
                                header-icon
                                chart-inside-content
                                no-footer
                                background-color="green"
                        >
                            <template slot="chartInsideHeader">
                                <div class="card-icon">
                                    <md-icon>timeline</md-icon>
                                </div>
                                <h4 class="title">
                                    {{ $t('dashboard.orderSum') }}
                                </h4>
                            </template>
                        </chart-card>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import { DASHBOARD_QUERY } from '@/graphql/queries/user';
    import { ChartCard } from "@/components";

    export default {
        title () {
            return this.$t('pages.dashboard');
        },
        name: "Dashboard",
        data() {
            return {
                dashboard: [],
                orderCountChart: {
                    options: {
                        lineSmooth: this.$Chartist.Interpolation.cardinal({
                            tension: 10
                        }),
                        axisY: {
                            showGrid: true,
                            offset: 30
                        },
                        axisX: {
                            showGrid: false
                        },
                        low: 0,
                        high: 15,
                        showPoint: true,
                        height: "300px"
                    }
                },
                orderSumChart: {
                    options: {
                        lineSmooth: this.$Chartist.Interpolation.cardinal({
                            tension: 10
                        }),
                        axisY: {
                            showGrid: true,
                            offset: 60
                        },
                        axisX: {
                            showGrid: false
                        },
                        low: 0,
                        high: 20000,
                        showPoint: true,
                        height: "300px"
                    }
                },
                firstLoad: true,
                orderCountData: null,
                orderSumData: null,
            }
        },
        computed: {
            orderCountChartData() {
                return this.orderCountData ? {labels: this.orderCountData.map(p => {return p.date }), series:  [this.orderCountData.map(p => {return p.total })]} : {labels: [], series: []};
            },
            orderSumChartData() {
                return this.orderSumData ? {labels: this.orderSumData.map(p => {return p.date }), series:  [this.orderSumData.map(p => {return p.price })]} : {labels: [], series: []};
            },
        },
        components: {
            ChartCard
        },
        apollo: {
            dashboard: {
                query: DASHBOARD_QUERY,
                result({data, loading, networkStatus}) {
                    this.orderCountData = JSON.parse(data.dashboard[0]);
                    this.orderSumData = JSON.parse(data.dashboard[1]);

                    let maxCount = this._.maxBy(this.orderCountData, 'total');
                    let maxSum = this._.maxBy(this.orderSumData, function(o) { return parseInt(o.price); });

                    this.orderCountChart.options.high = maxCount.total + 3;
                    this.orderSumChart.options.high = parseInt(maxSum.price) + 4000;

                    this.firstLoad = false;
                }
            }
        }
    }
</script>
