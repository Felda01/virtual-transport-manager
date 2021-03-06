<template>
    <div class="md-layout text-center">
        <div class="md-layout-item md-size-33 md-medium-size-50 md-small-size-70 md-xsmall-size-100">
            <h1>404</h1>
            <h3>{{ $t('notFound') }}</h3>
        </div>
    </div>
</template>

<script>
    export default {
        title () {
            return this.$t('pages.noFound');
        },
        name: "PageNotFound"
    }
</script>
