<template>
    <ValidationObserver ref="formElement">
        <form @submit.prevent="validate">
            <div class="md-layout">
                <div class="md-layout-item md-size-66 mt-4 md-small-size-100">
                    <order-map :location-from="locationFrom" :location-to="locationTo" :options-truck="options" :form="value"></order-map>
                </div>
                <div class="md-layout-item md-size-33 mt-4 md-small-size-100">
                    <ValidationProvider :name="$t('order.form.firstStep.truck.label')" rules="required" v-slot="{ passed, errors, failed }" tag="div">
                        <md-field :mdClearable="true" :class="[{ 'md-error md-invalid': failed }, { 'md-valid': passed }]">
                            <label>{{ $t('order.form.firstStep.truck.label') }}</label>
                            <md-select v-model="value.truck" :name="$t('order.form.firstStep.truck.label')" :multiple="false">
                                <template v-if="options && options.length > 0">
                                        <md-option :value="optionValue(option)" v-for="option in options" :key="optionValue(option)">{{ optionLabel(option) }}</md-option>
                                </template>
                                <template v-else>
                                    <md-option :value="null" disabled>{{ emptyOption() }}</md-option>
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
                </div>
            </div>
        </form>
    </ValidationObserver>
</template>

<script>
    import { SlideYDownTransition } from "vue2-transitions";
    import { extend } from "vee-validate";
    import { required } from "vee-validate/dist/rules";
    import { OrderMap } from "@/components";

    extend("required", required);

    export default {
        name: "FirstStep",
        components: {
            SlideYDownTransition,
            OrderMap
        },
        props: {
            options: {
                type: Array
            },
            locationFrom: {
                type: Object
            },
            locationTo: {
                type: Object
            },
            value: {
                type: Object
            }
        },
        methods: {
            optionValue(option) {
                return option.id;
            },
            optionLabel(option) {
                let result = [];
                let location = {};

                if (!option.drivers || option.drivers.length === 0) {
                    return '';
                }

                for (let driver of option.drivers) {
                    result.push(driver.first_name.charAt(0) + '. ' + driver.last_name)
                    location = driver.location;
                }

                return result.join(', ') + " - " + location.name + " (" + location.country.short_name.toUpperCase() + ")";
            },
            emptyOption() {
                return this.$t('modal.select.emptyOption');
            },
            validate() {
                return this.$refs.formElement.validate().then(valid => {
                    if (valid) {
                        this.$emit("on-validated");
                        return true;
                    }
                });
            },
            setErrors(errors) {
                this.$refs['formElement'].setErrors(errors);
            },
        }
    }
</script>

<style scoped>

</style>
