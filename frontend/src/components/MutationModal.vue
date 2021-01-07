<template>
    <modal v-if="showModal" @close="modalHide">
        <template slot="header">
            <h4 class="modal-title">{{ modalSchema.modalTitle }}</h4>
            <md-button
                    class="md-simple md-just-icon md-round modal-default-button"
                    @click="modalHide"
            >
                <md-icon>clear</md-icon>
            </md-button>
        </template>

        <template slot="body">
            <ValidationObserver :ref="formRef" v-slot="{ handleSubmit, errors }" :slim="true">
                <ValidationProvider name="General" v-slot="{ failed, errors }" tag="div">
                    <template v-if="failed">
                        <div class="alert alert-danger mb-3">
                            {{ errors[0] }}
                        </div>
                    </template>
                </ValidationProvider>

                <ValidationProvider name="Id" v-slot="{ failed, errors }" tag="div">
                    <template v-if="failed">
                        <div class="alert alert-danger mb-3">
                            {{ errors[0] }}
                        </div>
                    </template>
                </ValidationProvider>

                <form>
                    <template v-for="(field, index) in modalSchema.form.fields">
                        <template v-if="field.input === 'text'">
                            <ValidationProvider :name="fieldLabel(field.label, field.config)" :rules="field.rules" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ fieldLabel(field.label, field.config) }}{{ fieldAdditionalLabelText(field.config) }}{{ field.rules.includes('required') ? ' *' : '' }}</label>
                                    <template v-if="field.config && field.config.translatable">
                                        <md-input v-model="form[translatableKey][field.name][field.config.locale]" :type="field.type"></md-input>
                                    </template>
                                    <template v-else>
                                        <md-input v-model="form[field.name]" :type="field.type" :disabled="field.config.readOnly"></md-input>
                                    </template>

                                    <span class="md-error" v-show="failed">{{ errors[0] }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                        </template>
                        <template v-else-if="field.input === 'switch'">
                            <ValidationProvider :name="field.label" :rules="field.rules" v-slot="{ passed, failed, errors }" tag="div">
                                <md-switch class="md-primary" v-model="form[field.name]">{{ field.label }}{{ fieldAdditionalLabelText(field.config) }}</md-switch>
                            </ValidationProvider>
                        </template>
                        <template v-else-if="field.input === 'autocomplete'">
                            <ValidationProvider :name="field.label" :rules="field.rules" v-slot="{ passed, failed, errors }" tag="div">
                                <md-autocomplete v-model="form[field.name]" :md-options="field.config.options" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]" md-dense
                                                 @md-changed="getCustomers" @md-opened="getCustomers" @md-selected="selectCustomer">
                                    <label>{{ field.label }}{{ fieldAdditionalLabelText(field.config) }}{{ field.rules.includes('required') ? ' *' : '' }}</label>

                                    <template slot="md-autocomplete-empty" slot-scope="{ term }">
                                        {{ $t('validation.autocomplete', {term: term, model: field.config.autocompleteModel}) }}
                                    </template>

                                    <template slot="md-autocomplete-item" slot-scope="{ item }">{{ item.name }}</template>

                                    <span class="md-error" v-show="failed">{{ errors[0] }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-autocomplete>
                            </ValidationProvider>
                        </template>
                        <template v-else-if="field.input === 'select'">
                            <ValidationProvider :name="field.label" :rules="field.rules" v-slot="{ passed, failed, errors }" tag="div">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ field.label }}{{ fieldAdditionalLabelText(field.config) }}{{ field.rules.includes('required') ? ' *' : '' }}</label>
                                    <md-select v-model="form[field.name]" :name="field.label">
                                        <template v-if="field.config.groupBy">
                                            <md-optgroup v-for="(groups, groupIndex) in _.groupBy(field.config.options, field.config.groupBy)" :label="groups[0].country.name" :key="field.name + '-group-' + groupIndex">
                                                <md-option :value="field.config.optionValue(option)" v-for="option in groups" :key="field.config.optionValue(option)">{{ field.config.translatableLabel ? $t(field.config.translatableLabel + field.config.optionLabel(option)) : field.config.optionLabel(option) }}</md-option>
                                            </md-optgroup>
                                        </template>
                                        <template v-else>
                                            <md-option :value="field.config.optionValue(option)" v-for="option in field.config.options" :key="field.config.optionValue(option)">{{ field.config.translatableLabel ? $t(field.config.translatableLabel + field.config.optionLabel(option)) : field.config.optionLabel(option) }}</md-option>
                                        </template>
                                    </md-select>

                                    <span class="md-error" v-show="failed">{{ errors[0] }}</span>

                                    <slide-y-down-transition>
                                        <md-icon class="error" v-show="failed">close</md-icon>
                                    </slide-y-down-transition>
                                    <slide-y-down-transition>
                                        <md-icon class="success" v-show="passed">done</md-icon>
                                    </slide-y-down-transition>
                                </md-field>
                            </ValidationProvider>
                        </template>
                        <template v-else-if="field.input === 'image'">
                            <ValidationProvider :name="field.label" :rules="field.rules" v-slot="{ passed, failed, errors }" tag="div">
                                <div class="file-input md-field md-theme-default" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <div v-if="!form[field.name]" class="file-container">
                                        <div class="image-container">
                                            <img :src="imgPlaceholderSrc" />
                                        </div>
                                    </div>
                                    <div class="file-container" v-else>
                                        <div class="image-container">
                                            <img :src="form[field.name]" />
                                        </div>
                                    </div>
                                    <div class="button-container mb-2">
                                        <md-button class="md-danger md-round" @click="removeImage(field.name)" v-if="form[field.name]">
                                            <md-icon>close</md-icon>Remove
                                        </md-button>
                                        <md-button class="md-success md-round md-fileinput">
                                            <template v-if="!form[field.name]">Select image</template>
                                            <template v-else>Change</template>
                                            <input type="file" @change="onFileChange($event, field.name)" />
                                            <input type="hidden" v-model="form[field.name]">
                                        </md-button>
                                    </div>
                                    <div class="button-container" v-if="failed">
                                        <span class="md-error" v-show="failed">{{ errors[0] }}</span>
                                    </div>
                                </div>
                            </ValidationProvider>
                        </template>
                        <template v-else-if="field.input === 'staticText'">
                            <div :class="field.class">
                                {{ field.text }}
                            </div>
                        </template>
                    </template>
                </form>
            </ValidationObserver>
        </template>

        <template slot="footer">
            <md-button class="md-danger md-simple" @click="modalHide">{{ modalSchema.cancelBtnTitle }}</md-button>
            <md-button :disabled="form.busy"  class="md-success md-simple" @click="submitModalClick">
                <md-progress-spinner v-if="form.busy" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner>
                {{ modalSchema.okBtnTitle }}
            </md-button>
        </template>
    </modal>
</template>

<script>
    import Form from "../form/Form";
    import { TRANSLATABLE_KEY } from "../form/Form";
    import { Modal } from "@/components";
    import { SlideYDownTransition } from "vue2-transitions";
    import { extend } from "vee-validate";
    import { required, email, min, regex } from "vee-validate/dist/rules";
    import MdUuid from 'vue-material/src/core/utils/MdUuid';
    import i18n from '../lang';

    extend("email", email);
    extend("required", required);
    extend("min", min);
    extend("regex", regex);
    extend("min_integer", {
        params: ['other'],
        validate(value, { other }) {
            return parseInt(value) >= parseInt(other);
        },
        message: (_, values) =>  {
            return i18n.t('validation.min_integer', { attribute: values._field_, min: values.other })
        }
    });
    extend("latitude", value => {
        let latitudeRegex = /^-?([1-8]?[1-9]|[1-9]0)\.\d{1,6}$/;
        if (latitudeRegex.test(value)) {
            return true;
        }

        return i18n.t('validation.latitude');
    });
    extend("longitude", value => {
        let longitudeRegex = /^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.\d{1,6}$/;
        if (longitudeRegex.test(value)) {
            return true;
        }

        return i18n.t('validation.longitude');
    });
    extend('different', {
        params: ['target', 'other'],
        validate(value, { target }) {
            return value !== target;
        },
        message: (_, values) =>  {
            return i18n.t('validation.different', { attribute: values._field_, other: values.other })
        }
    });

    export default {
        name: "MutationModal",
        props: {
            modalSchema: {
                type: Object,
                required: true
            },
            locales: {
                type: Array
            }
        },
        components: {
            Modal,
            SlideYDownTransition
        },
        computed: {
            translatableKey() {
                return TRANSLATABLE_KEY;
            }
        },
        data() {
            return {
                form: null,
                fileName: '',
                showModal: false,
                formRef: 'form-' + MdUuid(),
                imgPlaceholderSrc: '/img/image_placeholder.jpg'
            }
        },
        methods: {
            modalHide() {
                this.showModal = false;
            },
            submitModalClick() {
                this.$refs[this.formRef].validate().then(valid => {
                    if (valid) {
                        this.submitModal();
                    }
                });
            },
            submitModal() {
                this.form.submit(this.$apollo, this.modalSchema.form.mutation)
                    .then(response => {
                        this.$emit('ok', response);

                        this.$nextTick(() => {
                            this.showModal = false;
                            this.form.reset();
                        });
                    })
                    .catch(error => {
                        let errors = {};
                        let finalErrors = {};

                        if (error.graphQLErrors && error.graphQLErrors[0]) {
                            errors = error.graphQLErrors[0].extensions.validation;
                        } else if (error.errors && error.errors[0]) {
                            errors = error.errors[0].extensions.validation;
                        }

                        for (let errorKey in errors) {
                            if (errors.hasOwnProperty(errorKey)) {
                                if (errorKey.includes('translations')) {
                                    let attributeKey = errorKey.split('.');
                                    let attributeName = attributeKey[0];
                                    attributeName = attributeName.replace('_translations', '');

                                    let attributeLocale = attributeKey[1];

                                    attributeName = this.transformErrorKey(attributeName);
                                    attributeName = attributeName + " " + this.locales[attributeLocale].toUpperCase();

                                    finalErrors[attributeName] = errors[errorKey];
                                } else {
                                    let attributeName = this.transformErrorKey(errorKey);
                                    finalErrors[attributeName] = errors[errorKey];
                                }
                            }
                        }

                        this.$refs[this.formRef].setErrors(finalErrors);
                    });
            },
            openModal() {
                let fields = {};
                for (let i = 0; i < this.modalSchema.form.fields.length; i++) {
                    let field = this.modalSchema.form.fields[i];
                    if (field.config && field.config.translatable) {
                        if (!fields.hasOwnProperty(TRANSLATABLE_KEY)) {
                            fields[TRANSLATABLE_KEY] = {};
                        }
                        if (!fields[TRANSLATABLE_KEY].hasOwnProperty(field.name)) {
                            fields[TRANSLATABLE_KEY][field.name] = {};
                        }

                        fields[TRANSLATABLE_KEY][field.name][field.config.locale] = field.value;
                    } else  {
                        fields[field.name] = field.value;
                    }
                }
                for (let i = 0; i < this.modalSchema.form.hiddenFields.length; i++) {
                    let field = this.modalSchema.form.hiddenFields[i];
                    fields[field.name] = field.value;
                }

                if (this.modalSchema.form.idField) {
                    fields['id'] = this.modalSchema.form.idField;
                }

                this.form = new Form(fields);

                this.showModal = true;
            },
            fieldLabel(name, config) {
                if (config && config.translatable) {
                    return name + ' ' + config.locale.toUpperCase();
                }
                return name;
            },
            fieldAdditionalLabelText(config){
                if (config && config.labelAdditionalText) {
                    return ' ' + config.labelAdditionalText;
                }
                return '';
            },
            transformErrorKey(errorKey) {
                let result = errorKey;
                result = result.replaceAll('_', ' ');

                if (result === 'location1') {
                    result = 'location 1';
                } else if (result === 'location2') {
                    result = 'location 2';
                }
                return result[0].toUpperCase() + result.substring(1);
            },
            removeImage(name) {
                this.form[name] = '';
            },
            onFileChange(event, name) {
                let files = event.target.files || event.dataTransfer.files;
                if (!files.length) return;

                let file = files[0];
                let reader = new FileReader();
                let vm = this;

                reader.onload = e => {
                    vm.form[name] = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
</script>

<style lang="scss">
    .md-menu-content,
    .md-select-menu {
        z-index: 10000!important;
    }
    .md-menu.md-select:not(.md-disabled) .md-icon {
        margin-right: 25px;
    }
    .file-container {
        display: flex;
        justify-content: center;
    }
</style>
