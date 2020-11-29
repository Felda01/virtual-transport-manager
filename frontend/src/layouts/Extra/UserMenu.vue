<template>
  <div class="user" v-if="me">
    <div class="photo" v-if="me.image">
      <img :src="me.image" :alt="name + ' avatar'" />
    </div>
    <div class="photo photo-letters">
      <span class="sidebar-mini">{{ letters }}</span>
    </div>
    <div class="user-info">
      <a
        data-toggle="collapse"
        :aria-expanded="!isClosed"
        @click.stop="toggleMenu"
        @click.capture="clicked"
      >
        <span>
          {{ name }}
          <b class="caret"></b>
        </span>
      </a>

      <collapse-transition>
        <div v-show="!isClosed">
          <ul class="nav">
            <slot>
              <li>
                <a href="#vue">
                  <span class="sidebar-mini">MP</span>
                  <span class="sidebar-normal">My Profile</span>
                </a>
              </li>
              <li>
                <a href="#vue">
                  <span class="sidebar-mini">EP</span>
                  <span class="sidebar-normal">Edit Profile</span>
                </a>
              </li>
              <li>
                <a href="#vue">
                  <span class="sidebar-mini">S</span>
                  <span class="sidebar-normal">Settings</span>
                </a>
              </li>
            </slot>
          </ul>
        </div>
      </collapse-transition>
    </div>
  </div>
</template>
<script>
  import { CollapseTransition } from "vue2-transitions";
  import { mapGetters } from 'vuex';
  import { ME_QUERY } from "../../graphql/queries/common";

  export default {
    components: {
      CollapseTransition
    },
    computed: {
      ...mapGetters([
        'user'
      ]),
      letters() {
        if (this.me) {
          return this.me.first_name[0].toUpperCase() + " " + this.me.last_name[0].toUpperCase();
        }
        return '';
      },
      name() {
        if (this.me) {
          return this.me.first_name + " " + this.me.last_name;
        }
        return '';
      }
    },
    data() {
      return {
        isClosed: true,
        me: null,
      };
    },
    methods: {
      clicked: function(e) {
        e.preventDefault();
      },
      toggleMenu: function() {
        this.isClosed = !this.isClosed;
      }
    },
    apollo: {
      me: {
        query: ME_QUERY
      }
    },
    watch: {
      me: function (newMe, oldMe) {
        this.$store.dispatch('setUser', {newMe});
      }
    }
  };
</script>
<style>
.collapsed {
  transition: opacity 1s;
}
</style>
