<template>
    <div class="md-layout">
        <div class="md-layout-item md-size-66 mt-4 md-small-size-100 md-small-hide">
            <order-map :location-from="locationFrom" :location-to="locationTo" :options-truck="optionsTruck" :options-path="optionsPath" :form="value"></order-map>
        </div>
        <div class="md-layout-item md-size-33 mt-4 md-small-size-100">
            <md-list class="md-double-line md-dense">
                <md-subheader>{{ $t('order.form.summary') }}</md-subheader>

                <md-list-item>
                    <div class="md-list-item-text">
                        <span>{{ $t('order.form.firstStep.truck.label') }}</span>
                        <span>{{ driversText(value) }}</span>
                    </div>
                </md-list-item>

                <md-list-item>
                    <div class="md-list-item-text">
                        <span>{{ $t('order.form.secondStep.path.label') }}</span>
                        <span>{{ pathText(value) }}</span>
                    </div>
                </md-list-item>
            </md-list>
        </div>
    </div>
</template>

<script>
    import { OrderMap } from "@/components";

    export default {
        name: "ThirdStep",
        components: {
            OrderMap
        },
        props: {
            mutation: String,
            value: {
                type: Object
            },
            optionsTruck: {
                type: Array
            },
            optionsPath: {
                type: Array
            },
            locationFrom: {
                type: Object
            },
            locationTo: {
                type: Object
            },
        },
        methods: {
            pathText(value) {
                if (value.path) {
                    let optionObject = JSON.parse(this.optionsPath[value.path - 1]);

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
                        time + ', ' + fee + " " + this.$t("order.form.secondStep.feeUnit");
                }
                return '';
            },
            driversText(value) {
                let result = [];
                let location = {};

                if (value.truck) {
                    let truck = this.lodash.find(this.optionsTruck, ['id', value.truck]);
                    for (let driver of truck.drivers) {
                        result.push(driver.first_name.charAt(0) + '. ' + driver.last_name)
                        location = driver.location;
                    }

                    return result.join(', ') + " - " + location.name + " (" + location.country.short_name.toUpperCase() + ")";
                }
                return '';
            },
            validate() {
                this.$emit("on-validated");
                return true;
            },
        }
    }
</script>

<style scoped>

</style>
