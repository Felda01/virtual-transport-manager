<template>
    <div class="md-layout text-center">
        <div class="md-layout-item md-size-50 mb-3">
            <h1 class="md-display-1">{{ $t('dashboard.title') }}</h1>
            <p class="md-subheading">{{ $t('dashboard.text') }}</p>
        </div>
    </div>
</template>

<script>
    export default {
        title () {
            return this.$t('pages.dashboard');
        },
        name: "Dashboard"
    }
</script>
