<template>
  <div class="user" v-if="user">
    <div class="photo" v-if="user.image">
      <img :src="user.image" :alt="name + ' avatar'" />
    </div>
    <div class="photo" v-else>
      <img :src="avatarPlaceholder" :alt="name + ' avatar'" />
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
                <router-link :to="{name: 'user', params: {id: user.id, locale: $i18n.locale}}">
                  <span class="sidebar-mini">{{ $t('navigation.user.mini') }}</span>
                  <span class="sidebar-normal">{{ $t('navigation.user.normal') }}</span>
                </router-link>
              </li>
              <li>
                <router-link :to="{name: 'messages'}">
                  <span class="sidebar-mini">{{ $t('navigation.messages.mini') }}</span>
                  <span class="sidebar-normal">{{ $t('navigation.messages.normal') }}</span>
                </router-link>
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

  export default {
    components: {
      CollapseTransition
    },
    computed: {
      ...mapGetters([
        'user'
      ]),
      letters() {
        if (this.user) {
          return this.user.first_name[0].toUpperCase() + " " + this.user.last_name[0].toUpperCase();
        }
        return '';
      },
      name() {
        if (this.user) {
          return this.user.first_name + " " + this.user.last_name;
        }
        return '';
      }
    },
    data() {
      return {
        isClosed: true,
        avatarPlaceholder: "/img/default-avatar.png",
      };
    },
    methods: {
      clicked: function(e) {
        e.preventDefault();
      },
      toggleMenu: function() {
        this.isClosed = !this.isClosed;
      }
    }
  };
</script>
<style>
.collapsed {
  transition: opacity 1s;
}
</style>
