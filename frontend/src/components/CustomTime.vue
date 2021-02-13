<template>
    <span v-if="currentTime">{{ currentTime }}</span>
</template>

<script>
    import constants from "../constants";
    import moment from 'moment-timezone';

    export default {
        name: "CustomTime",
        data() {
            return {
                time: null,
                currentTime: null,
                intervalTimer: null,
            }
        },
        mounted() {
            let start = moment(constants.TIME.START, 'DD-MM-YYYY HH:mm:ss').tz("Europe/Bratislava");
            let diff = moment().tz("Europe/Bratislava").diff(start, 'seconds');
            let gameDiff = diff * constants.TIME.ACCELERATION;
            this.time = start.add(gameDiff, 'seconds');
            this.currentTime = this.time.format('D.M.YYYY HH:mm');
            let interval = parseInt(60 / constants.TIME.ACCELERATION * 1000);
            let self = this;
            this.intervalTimer = setInterval(() => {
                self.time = self.time.add(1, 'm');
                self.currentTime = self.time.format('D.M.YYYY HH:mm');
            }, interval);
        },
        beforeDestroy() {
            clearInterval(this.intervalTimer);
        }
    }
</script>

<style scoped>

</style>
