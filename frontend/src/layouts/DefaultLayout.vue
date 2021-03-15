<template>
    <div
            class="wrapper"
            :class="[
      { 'nav-open': $sidebar.showSidebar },
    ]"
    >
        <notifications></notifications>
        <side-bar
                :active-color="sidebarBackground"
                :background-image="sidebarBackgroundImage"
                :data-background-color="sidebarBackgroundColor"
                :title="'Transport manager'"
                logo="/img/truck.png"
                :logo-url="generatePath('dashboard')"
        >
            <user-menu></user-menu>
            <mobile-menu></mobile-menu>
            <template slot="links">
                <sidebar-item :link="{ name: $t('navigation.companyEmployees'), icon: 'group' }">
                    <sidebar-item :link="{ name: $t('pages.users'), path: generatePath('users') }"
                    ></sidebar-item>
                    <sidebar-item :link="{ name: $t('pages.drivers'), path: generatePath('drivers') }"
                    ></sidebar-item>
                </sidebar-item>
                <sidebar-item :link="{ name: $t('navigation.companyEquipmentData'), icon: 'local_shipping' }">
                    <sidebar-item :link="{ name: $t('pages.garages'), path: generatePath('garages') }"
                    ></sidebar-item>
                    <sidebar-item :link="{ name: $t('pages.trucks'), path: generatePath('trucks') }"
                    ></sidebar-item>
                    <sidebar-item :link="{ name: $t('pages.trailers'), path: generatePath('trailers') }"
                    ></sidebar-item>
                </sidebar-item>
                <sidebar-item :link="{ name: $t('navigation.shop'), icon: 'shopping_cart' }">
                    <sidebar-item
                            :link="{ name: $t('pages.garageShop'), path: generatePath('garageShop') }"
                    ></sidebar-item>
                    <sidebar-item
                            :link="{ name: $t('pages.truckShop'), path: generatePath('truckShop') }"
                    ></sidebar-item>
                    <sidebar-item
                            :link="{ name: $t('pages.trailerShop'), path: generatePath('trailerShop') }"
                    ></sidebar-item>
                    <sidebar-item :link="{ name: $t('pages.recruitmentAgencyDrivers'), path: generatePath('recruitmentAgencyDrivers') }"
                    ></sidebar-item>
                </sidebar-item>
                <sidebar-item :link="{ name: $t('navigation.orders'), icon: 'work' }">
                    <sidebar-item
                            :link="{ name: $t('pages.orderOffers'), path: generatePath('orderOffers') }"
                    ></sidebar-item>
                    <sidebar-item
                            :link="{ name: $t('pages.orders'), path: generatePath('orders') }"
                    ></sidebar-item>
                    <sidebar-item
                            :link="{ name: $t('pages.doneOrders'), path: generatePath('doneOrders') }"
                    ></sidebar-item>
                </sidebar-item>
                <sidebar-item :link="{ name: $t('navigation.economy'), icon: 'trending_up' }">
                    <sidebar-item
                            :link="{ name: $t('pages.transactions'), path: generatePath('transactions') }"
                    ></sidebar-item>
                    <sidebar-item
                            :link="{ name: $t('pages.bankLoans'), path: generatePath('bankLoans') }"
                    ></sidebar-item>
                    <sidebar-item
                            :link="{ name: $t('pages.scoreBoard'), path: generatePath('scoreBoard') }"
                    ></sidebar-item>
                </sidebar-item>
            </template>
        </side-bar>
        <div class="main-panel">
            <top-navbar></top-navbar>
            <div
                    :class="{ content: !$route.meta.hideContent }"
                    @click="toggleSidebar"
            >
                <zoom-center-transition :duration="200" mode="out-in">
                    <!-- your content here -->
                    <router-view :key="$route.path"></router-view>
                </zoom-center-transition>
            </div>
            <content-footer v-if="!$route.meta.hideFooter"></content-footer>
        </div>
    </div>
</template>

<script>
    import PerfectScrollbar from "perfect-scrollbar";
    import "perfect-scrollbar/css/perfect-scrollbar.css";

    import EventBus from "../event-bus";

    function hasElement(className) {
        return document.getElementsByClassName(className).length > 0;
    }

    function initScrollbar(className) {
        if (hasElement(className)) {
            new PerfectScrollbar(`.${className}`);
            document.getElementsByClassName(className)[0].scrollTop = 0;
        } else {
            // try to init it later in case this component is loaded async
            setTimeout(() => {
                initScrollbar(className);
            }, 100);
        }
    }

    function reinitScrollbar() {
        let docClasses = document.body.classList;
        let isWindows = navigator.platform.startsWith("Win");
        if (isWindows) {
            // if we are on windows OS we activate the perfectScrollbar function
            initScrollbar("sidebar");
            initScrollbar("sidebar-wrapper");
            initScrollbar("main-panel");

            docClasses.add("perfect-scrollbar-on");
        } else {
            docClasses.add("perfect-scrollbar-off");
        }
    }

    import TopNavbar from "./TopNavbar.vue";
    import ContentFooter from "./ContentFooter.vue";
    import MobileMenu from "./Extra/MobileMenu.vue";
    import UserMenu from "./Extra/UserMenu.vue";
    import { ZoomCenterTransition } from "vue2-transitions";
    import { mapGetters } from "vuex";

    export default {
        name: "DefaultLayout",
        components: {
            TopNavbar,
            ContentFooter,
            MobileMenu,
            UserMenu,
            ZoomCenterTransition
        },
        data() {
            return {
                sidebarBackgroundColor: "black",
                sidebarBackground: "green",
                sidebarBackgroundImage: "/img/sidebar-2.jpg",
                sidebarMini: true,
                sidebarImg: true
            };
        },
        methods: {
            toggleSidebar() {
                if (this.$sidebar.showSidebar) {
                    this.$sidebar.displaySidebar(false);
                }
            },
            minimizeSidebar() {
                if (this.$sidebar) {
                    this.$sidebar.toggleMinimize();
                }
            },
            generatePath(name) {
                let path = this.$router.resolve({
                    name: name,
                    params: { locale: this.$i18n.locale },
                });
                return path.href;
            }
        },
        updated() {
            reinitScrollbar();
        },
        mounted() {
            reinitScrollbar();
            this.$loadScript(process.env.VUE_APP_LARAVEL_ENDPOINT_SOCKET_IO)
            .then(() => {
                this.$echo.channel('transport_manager_database_company-' + this.user.company.id).listen('ProcessTransaction', (data) => {
                    this.$store.dispatch('getCompany');
                });
                this.$echo.channel('transport_manager_database_company-' + this.user.company.id).listen('RefreshQuery', (data) => {
                    EventBus.$emit('refreshQuery', data);
                });
                this.$echo.channel('transport_manager_database_market').listen('RefreshMarketQuery', (data) => {
                    EventBus.$emit('refreshMarket');
                });
                this.$echo.channel('transport_manager_database_message-' + this.user.id).listen('RefreshMessageQuery', (data) => {
                    EventBus.$emit('refreshMessage', data);
                });
            });
        },
        computed: {
            ...mapGetters([
                'user'
            ]),
        },
        watch: {
            sidebarMini() {
                this.minimizeSidebar();
            },
        }
    }
</script>

<style scoped lang="scss">
    $scaleSize: 0.95;
    @keyframes zoomIn95 {
        from {
            opacity: 0;
            transform: scale3d($scaleSize, $scaleSize, $scaleSize);
        }
        to {
            opacity: 1;
        }
    }
    .main-panel .zoomIn {
        animation-name: zoomIn95;
    }
    @keyframes zoomOut95 {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            transform: scale3d($scaleSize, $scaleSize, $scaleSize);
        }
    }
    .main-panel .zoomOut {
        animation-name: zoomOut95;
    }
</style>
