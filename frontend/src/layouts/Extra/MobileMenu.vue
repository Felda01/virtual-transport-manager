<template>
  <ul class="nav nav-mobile-menu">
    <li>
      <drop-down>
        <a slot="title" class="dropdown-toggle" data-toggle="dropdown">
          <i class="material-icons">notifications</i>
          <span class="notification">5</span>
          <p>Some Actions</p>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><a href="#">Mike John responded to your email</a></li>
          <li><a href="#">You have 5 new tasks</a></li>
          <li><a href="#">You're now friend with Andrew</a></li>
          <li><a href="#">Another Notification</a></li>
          <li><a href="#">Another One</a></li>
        </ul>
      </drop-down>
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
export default {
  data() {
    return {

    };
  },
  methods: {
    logout() {
      this.$store.dispatch('logout', {fullPath: this.$router.currentRoute.fullPath});
    }
  }
};
</script>
