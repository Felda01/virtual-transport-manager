<template>
    <ValidationObserver ref="formElement">
        <form @submit.prevent="validate">
            <div class="md-layout">
                <div class="md-layout-item md-size-66 mt-4 md-small-size-100 md-small-hide">
                    Mapa
                </div>
                <div class="md-layout-item md-size-33 mt-4 md-small-size-100">
                    <ValidationProvider :name="$t('order.form.secondStep.path.label')" rules="required" class="radio-group" tag="div">
                        <template v-for="(option, index) in options">
                            <md-radio v-model="value.path" :value="index + 1" :name="$t('order.form.secondStep.path.label')" @change="updateMap">
                                {{ optionLabel(option) }}
                            </md-radio>
                        </template>
                    </ValidationProvider>
                    <p class="md-caption">{{ $t('order.form.secondStep.timeHelp') }}</p>
                    <p class="md-caption">{{ $t('order.form.secondStep.feesHelp') }}</p>
                </div>
            </div>
        </form>
    </ValidationObserver>
</template>

<script>
    import { extend } from "vee-validate";
    import { required } from "vee-validate/dist/rules";

    extend("required", required);

    export default {
        name: "SecondStep",
        props: {
            options: {
                type: Array
            },
            value: {
                type: Object
            }
        },
        methods: {
            optionLabel(option) {
                let optionObject = JSON.parse(option);

                let distance = this.$options.filters.currency(optionObject.distance, ' ', 0, { thousandsSeparator: ' ' });
                let timeMinutes = optionObject.time % 60;
                let timeHours = (optionObject.time - timeMinutes) / 60;
                let time = '';
                if (timeHours > 0) {
                    time += timeHours + " h ";
                }
                time += timeMinutes + " min";

                let fee = this.$options.filters.currency(optionObject.fee, ' ', 2, { thousandsSeparator: ' ' });

                return distance + " " + this.$t("order.form.secondStep.distanceUnit") + ', ' +
                    time + '*, ' + fee + " " + this.$t("order.form.secondStep.feeUnit") + "**";
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
            updateMap() {

            }
        }
    }
</script>

<style scoped>
    .radio-group {
        display: flex;
        flex-direction: column;
    }
</style>
