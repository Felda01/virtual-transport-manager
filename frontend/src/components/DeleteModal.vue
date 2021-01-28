<template>
    <modal v-if="showModal" @close="modalHide">
        <template slot="header">
            <md-button
                    class="md-simple md-just-icon md-round modal-default-button"
                    @click="modalHide"
            >
                <md-icon>clear</md-icon>
            </md-button>
        </template>

        <template slot="body">
            <div class="alert alert-danger mb-3 mt-2" v-if="errorMessage">
                {{ errorMessage }}
            </div>

            <p>{{ modalSchema.message }}</p>
        </template>

        <template slot="footer">
            <md-button class="md-danger md-simple" @click="modalHide">{{ modalSchema.cancelBtnTitle }}</md-button>
            <md-button class="md-success md-simple" @click="submit">{{ modalSchema.okBtnTitle }}</md-button>
        </template>
    </modal>
</template>

<script>
    import { Modal } from "@/components";
    import { SlideYDownTransition } from "vue2-transitions";
    import Form from "../form/Form";

    export default {
        name: "DeleteModal",
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
                showModal: false,
                form: null,
                errorMessage: ''
            }
        },
        methods: {
            modalHide() {
                this.showModal = false;
            },
            submit() {
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

                        if (error.graphQLErrors && error.graphQLErrors[0]) {
                            errors = error.graphQLErrors[0].extensions.validation;
                        } else if (error.errors && error.errors[0]) {
                            errors = error.errors[0].extensions.validation;
                        }

                        for (let errorKey in errors) {
                            if (errors.hasOwnProperty(errorKey)) {
                                this.errorMessage = errors[errorKey][0];
                                break;
                            }
                        }

                    });
            },
            openModal() {
                let fields = {};
                this.errorMessage = '';

                if (this.modalSchema.form.hiddenFields && this.modalSchema.form.hiddenFields.length > 0) {
                    for (let i = 0; i < this.modalSchema.form.hiddenFields.length; i++) {
                        let field = this.modalSchema.form.hiddenFields[i];
                        fields[field.name] = field.value;
                    }
                }

                if (this.modalSchema.form.idField) {
                    fields['id'] = this.modalSchema.form.idField;
                }

                this.form = new Form(fields);

                this.showModal = true;
            }
        }
    }
</script>
