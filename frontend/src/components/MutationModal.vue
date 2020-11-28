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
            <ValidationObserver :ref="formRef" v-slot="{ handleSubmit }">
                <form @submit.prevent="handleSubmit(submitModal)">
                    <template v-for="(field, index) in modalSchema.form.fields">
                        <template v-if="field.input === 'text'">
                            <ValidationProvider :vid="field.name" :name="field.label" :rules="field.rules" v-slot="{ passed, failed, errors }" slot="inputs">
                                <md-field :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                                    <label>{{ field.label }}{{ field.rules.includes('required') ? ' *' : '' }}</label>
                                    <md-input v-model="form[field.name]" :type="field.type"></md-input>
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
                    </template>
                </form>
            </ValidationObserver>
        </template>

        <template slot="footer">
            <md-button class="md-danger md-simple" @click="modalHide">{{ modalSchema.cancelBtnTitle }}</md-button>
            <md-button :disabled="form.busy"  class="md-success md-simple" @click="submitModal">
                <md-progress-spinner v-if="form.busy" style="margin-right: 15px;" :md-diameter="20" :md-stroke="3" md-mode="indeterminate"></md-progress-spinner>
                {{ modalSchema.okBtnTitle }}
            </md-button>
        </template>
    </modal>
</template>

<script>
    import Form from "../form/Form";
    import { Modal } from "@/components";
    import { SlideYDownTransition } from "vue2-transitions";
    import { extend } from "vee-validate";
    import { required, email, min } from "vee-validate/dist/rules";
    import MdUuid from 'vue-material/src/core/utils/MdUuid'

    extend("email", email);
    extend("required", required);
    extend("min", min);

    export default {
        name: "MutationModal",
        props: {
            modalSchema: {
                type: Object,
                required: true
            },
        },
        components: {
            Modal,
            SlideYDownTransition
        },
        data() {
            return {
                form: null,
                fileName: '',
                showModal: false,
                formRef: 'form-' + MdUuid(),
            }
        },
        methods: {
            modalHide() {
                this.showModal = false;
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
                        this.$refs[this.formRef].setErrors(error.graphQLErrors[0].extensions.validation);
                    })
            },
            openModal() {
                let fields = {};
                for (let i = 0; i < this.modalSchema.form.fields.length; i++) {
                    let field = this.modalSchema.form.fields[i];
                    fields[field.name] = field.value;
                }
                for (let i = 0; i < this.modalSchema.form.hiddenFields.length; i++) {
                    let field = this.modalSchema.form.hiddenFields[i];
                    fields[field.name] = field.value;
                }
                this.form = new Form(fields);

                this.showModal = true;
            },
        }
    }
</script>
