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
      <router-link :to="{ name: $router.name, params: { locale: $i18n.locale === 'en' ? 'sk' : 'en'} }">
        <md-icon>language</md-icon>
        <p>{{ nextLanguage | uppercase }}</p>
      </router-link>
    </li>
    <li>
      <a href="#" data-toggle="dropdown" class="dropdown-toggle" @click.prevent="logout">
        <md-icon>logout</md-icon>
        <p>{{ $t('logout') }}</p>
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
      nextLanguage() {
        return this.$i18n.locale === 'en' ? 'sk' : 'en'
      }
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
