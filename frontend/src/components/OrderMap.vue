<template>
    <l-map
            :ref="mapRef"
            style="width: 100%; height: 340px; min-height: 340px;"
            :zoom.sync="zoom"
            :options="mapOptions"
            :min-zoom="minZoom"
            :max-zoom="maxZoom"
            @ready="mapReady"
    >
        <l-marker :lat-lng="marker" v-for="(marker, index) in markers" :key="index"></l-marker>
        <l-tile-layer :url="url"></l-tile-layer>
        <l-polyline v-if="pathLine" :lat-lngs="pathLine" color="blue" />
        <l-control-scale :imperial="imperial" />
    </l-map>
</template>

<script>
    import "leaflet/dist/leaflet.css";
    import { LMap, LTileLayer, LMarker, LControlZoom, LControlScale, LPolyline } from 'vue2-leaflet';
    import MdUuid from 'vue-material/src/core/utils/MdUuid';

    export default {
        name: "OrderMap",
        props: {
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
            form: {
                type: Object
            }
        },
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LControlZoom,
            LControlScale,
            LPolyline
        },
        data() {
            return {
                map: null,
                mapOptions: {zoomControl: true, attributionControl: true, zoomSnap: true},
                url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                zoom: 4,
                minZoom: 3,
                maxZoom: 8,
                zoomPosition: 'topleft',
                imperial: false,
                locationFromPoint: [51.476483373501964, -0.14668464136775586],
                locationToPoint: [51.52948330894063, -0.019140238291583955],
                markers: [],
                pathLine: null,
                mapRef: 'map-' + MdUuid(),
            }
        },
        watch: {
            form: {
                deep: true,
                handler() {
                    this.updateMarkers();
                    this.updatePathLine();
                }
            }
        },
        methods: {
            mapReady() {
                this.$refs[this.mapRef].mapObject._onResize();
                this.$refs[this.mapRef].mapObject.fitBounds(this.markers);
                if (this.form.path) {
                    this.updatePathLine();
                }
                if (this.form.truck) {
                    this.updateMarkers();
                }
            },
            updateMarkers() {
                if (this.$refs[this.mapRef] && this.$refs[this.mapRef].mapObject) {
                    if (this.form.truck) {
                        let truck = this.lodash.find(this.optionsTruck, ['id', this.form.truck]);
                        let driver = truck.drivers && truck.drivers.length > 0 ? truck.drivers[0] : null;
                        if (driver) {
                            this.$refs[this.mapRef].mapObject.fitBounds([this.locationFromPoint, this.locationToPoint, [driver.location.lat, driver.location.lng]]);
                            this.markers = [this.locationFromPoint, this.locationToPoint, [driver.location.lat, driver.location.lng]];
                            return;
                        }
                    }

                    this.$refs[this.mapRef].mapObject.fitBounds([this.locationFromPoint, this.locationToPoint]);
                    this.markers = [this.locationFromPoint, this.locationToPoint];
                }
            },
            updatePathLine() {
                if (this.form.path && this.optionsPath) {
                    let optionObject = JSON.parse(this.optionsPath[this.form.path - 1]);
                    this.pathLine = optionObject['pathModels'].map(p => { return [p.lat, p.lng]});
                }
            }
        },
        mounted() {
            this.locationFromPoint = [this.locationFrom.lat, this.locationFrom.lng];
            this.locationToPoint = [this.locationTo.lat, this.locationTo.lng];
            this.markers = [this.locationFromPoint, this.locationToPoint];
        }
    }
</script>

<style scoped>

</style>
