<template>
    <router-view :key="reRender"></router-view>
</template>

<script>
    import EventBus from "../event-bus";

    export default {
        name: "Layout",
        data() {
            return {
                reRender: 0
            }
        },
        mounted() {
            EventBus.$on('localeChanged', () => {
                this.reRender += 1;
            });
        }
    }
</script>
