<template>
    <div class="search-form">
        <md-card>
            <md-card-header>
                <div class="md-title">
                    <div class="md-layout">
                        <div class="md-layout-item">
                            {{ $t('search.title') }}
                        </div>
                    </div>
                </div>
            </md-card-header>

            <md-card-content>
                <div class="md-layout" v-for="(group, rowindex) in searchSchema.groups" :key="'group-' + rowindex" :class="group.class">
                    <div class="md-layout-item" v-for="(field, colindex) in group.fields" :key="'field-' + rowindex + '-' + colindex" :class="field.class">
                        <template v-if="field.input === 'text'">
                            <md-field :mdClearable="true">
                                <label>{{ field.label }}</label>
                                <md-input v-model="value[field.name]" :type="field.type"></md-input>
                            </md-field>
                        </template>
                        <template v-else-if="field.input === 'select'">
                            <md-field :mdClearable="true">
                                <label>{{ field.label }}</label>
                                <md-select v-model="value[field.name]" :name="field.label" :multiple="field.config.multiple ? field.config.multiple : false">
                                    <md-option :value="field.config.optionValue(option)" v-for="option in field.config.options" :key="field.config.optionValue(option)">{{ field.config.translatableLabel ? $t(field.config.translatableLabel + field.config.optionLabel(option)) : field.config.optionLabel(option) }}</md-option>
                                </md-select>
                            </md-field>
                        </template>
                        <template v-else-if="field.input === 'range'">
                            <div class="md-layout md-gutter">
                                <div class="md-layout-item md-size-50">
                                    <md-field :mdClearable="true">
                                        <label>{{ field.labelFrom }}</label>
                                        <md-input v-model="value[field.name]['min']" :type="field.type"></md-input>
                                    </md-field>
                                </div>
                                <div class="md-layout-item md-size-50">
                                    <md-field :mdClearable="true">
                                        <label>{{ field.labelTo }}</label>
                                        <md-input v-model="value[field.name]['max']" :type="field.type"></md-input>
                                    </md-field>
                                </div>
                            </div>
                        </template>
                        <template v-if="field.input === 'switch'">
                            <md-switch class="md-primary" v-model="value[field.name]">{{ field.label }}</md-switch>
                        </template>
                    </div>
                </div>
            </md-card-content>
        </md-card>
    </div>
</template>

<script>
    export default {
        name: "SearchForm",
        props: {
            searchSchema: {
                type: Object,
                required: true
            },
            value: {
                type: Object
            }
        },
    }
</script>

<style scoped>
    .md-menu.md-select:not(.md-disabled) >>> .md-icon {
        margin-right: 5px!important;
    }
</style>
