<template>
  <ul class="nav nav-mobile-menu">
    <li>
      <a v-if="money !== null">
        <md-icon>euro</md-icon>
        <p>{{ money | currency(' ', 2, { thousandsSeparator: ' ' }) }} €</p>
      </a>
    </li>
    <li>
      <a>
        <md-icon>query_builder</md-icon>
        <p><custom-time></custom-time></p>
      </a>
    </li>
    <li>
      <a href="#" data-toggle="dropdown" class="dropdown-toggle" @click.prevent="logout">
        <md-icon>logout</md-icon>
        <p>Log out</p>
      </a>
    </li>
  </ul>
</template>
<script>
  import { CustomTime } from "@/components";
  import { mapGetters } from "vuex";
  export default {
    data() {
      return {

      };
    },
    computed: {
      ...mapGetters([
        'money',
      ]),
    },
    components: {
      CustomTime
    },
    methods: {
      logout() {
        this.$store.dispatch('logout', {fullPath: this.$router.currentRoute.fullPath});
      }
    }
  };
</script>
