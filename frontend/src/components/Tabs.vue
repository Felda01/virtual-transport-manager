<template>
  <md-card
    class="md-card-tabs"
    :class="[
      { 'flex-column': flexColumn },
      { 'nav-pills-icons': navPillsIcons },
      { 'md-card-plain': plain }
    ]"
  >
    <md-card-header>
      <slot name="header-title"></slot>
    </md-card-header>
    <md-card-content>
      <md-list class="nav-tabs">
        <md-list-item
          v-for="(item, index) in tabName"
          @click="switchPanel(tabName[index])"
          :key="item"
          :class="[
            { active: isActivePanel(tabName[index]) },
            { [getColorButton(colorButton)]: isActivePanel(tabName[index]) }
          ]"
        >
          {{ tabName[index] }}
          <md-icon v-if="navPillsIcons">{{ tabIcon[index] }}</md-icon>
        </md-list-item>
      </md-list>

      <transition name="fade" mode="out-in">
        <div class="tab-content">
          <template v-for="(item, index) in tabName">
            <template v-if="isActivePanel(tabName[index])">
              <div :class="getTabContentClass(index + 1)" :key="item">
                <slot :name="getTabContent(index + 1)">
                  This is the default text!
                </slot>
              </div>
            </template>
          </template>
        </div>
      </transition>
    </md-card-content>
  </md-card>
</template>

<script>
export default {
  props: {
    flexColumn: Boolean,
    navPillsIcons: Boolean,
    plain: Boolean,
    tabName: Array,
    tabIcon: Array,
    tabContentCenter: Boolean,
    tabContentFullWidth: Boolean,
    colorButton: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      activePanel: this.tabName[0]
    };
  },
  methods: {
    switchPanel(panel) {
      this.activePanel = panel;
    },
    isActivePanel(panel) {
      return this.activePanel == panel;
    },
    getColorButton: function(colorButton) {
      return "md-" + colorButton + "";
    },
    getTabContent(index) {
      return "tab-pane-" + index + "";
    },
    getTabContentClass(index) {
      let className = "";
      if (this.tabContentCenter) {
        className += " mx-auto";
      }
      if (this.tabContentFullWidth) {
        className += " width-full";
      }
      return "tab-pane-" + index + "" + className;
    }
  }
};
</script>

<style lang="css"></style>
