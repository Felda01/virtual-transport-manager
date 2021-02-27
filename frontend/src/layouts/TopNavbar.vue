<template>
  <md-toolbar
    md-elevation="0"
    class="md-transparent"
    :class="{
      'md-toolbar-absolute md-white md-fixed-top': $route.meta.navbarAbsolute
    }"
  >
    <div class="md-toolbar-row">
      <div class="md-toolbar-section-start">
        <h3 class="md-title">{{ $route.meta.title }}</h3>
      </div>
      <div class="md-toolbar-section-end">
        <md-button
          class="md-just-icon md-round md-simple md-toolbar-toggle"
          :class="{ toggled: $sidebar.showSidebar }"
          @click="toggleSidebar"
        >
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </md-button>

        <div class="md-collapse">
          <md-list>
            <li class="md-list-item" v-if="user && money !== null">
              <div class="md-list-item-content">
                <animated-number :currency="true" :duration="1200" class="md-list-item-container" :value="money"></animated-number>
              </div>
            </li>
            <li class="md-list-item" v-if="user && money !== null">
              <div class="md-list-item-content">
                <custom-time class="md-list-item-container"></custom-time>
              </div>
            </li>

            <md-list-item href="#" @click.prevent="logout">
              <md-icon class="mr-0">logout</md-icon>
            </md-list-item>
          </md-list>
        </div>
      </div>
    </div>
  </md-toolbar>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { AnimatedNumber, CustomTime } from "@/components";

  export default {
    data() {
      return {

      };
    },
    computed: {
      ...mapGetters([
        'user',
        'money',
      ]),
    },
    components: {
      AnimatedNumber,
      CustomTime
    },
    methods: {
      toggleSidebar() {
        this.$sidebar.displaySidebar(!this.$sidebar.showSidebar);
      },
      minimizeSidebar() {
        if (this.$sidebar) {
          this.$sidebar.toggleMinimize();
        }
      },
      logout() {
        this.$store.dispatch('logout', {fullPath: this.$router.currentRoute.fullPath});
      }
    }
  };
</script>
