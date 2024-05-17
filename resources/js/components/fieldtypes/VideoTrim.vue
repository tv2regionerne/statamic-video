<template>

    <div>
        <video
            :src="sourceUrl"
            class="w-full rounded mb-2"
            ref="video"
            controls
            @loadeddata="loadedVideo" />
        <div v-if="!loading" class="video-trim-controls text-blue">
            <input
                type="range"
                class="w-full"
                min="0"
                max="1"
                step="any"
                :value="startPos"
                @input="updateStartPos($event)" />
            <input
                type="range"
                class="w-full"
                min="0"
                max="1"
                step="any"
                :value="endPos"
                @input="updateEndPos($event)" />
            <div :style="{
                '--start': startPos,
                '--end': endPos,
            }"></div>
        </div>
    </div>

</template>

<script>
export default {

    mixins: [Fieldtype],

    inject: ['storeName'],

    data() {
        return {
            loading: true,
            startPos: 0,
            endPos: 100,
        };
    },

    computed: {
        
        sourceUrl() {
            return this.store.values[this.config.source];
        },
        
        store() {
            return this.$store.state.publish[this.storeName];
        },

        duration() {
            return this.$refs.video.duration;
        },

        startTime() {
            return Math.round(this.duration * this.startPos);
        },

        endTime() {
            return Math.round(this.duration * this.endPos);
        },

    },

    methods: {

        loadedVideo() {
            this.loading = false;
        },
        
        seekVideo(pos) {
            this.$refs.video.pause();
            this.$refs.video.currentTime = this.duration * pos;
        },

        updateStartPos(e) {
            this.startPos = e.target.value;
            this.endPos = Math.max(this.startPos, this.endPos);
            this.seekVideo(this.startPos);
        },

        updateEndPos(e) {
            this.endPos = e.target.value;
            this.startPos = Math.min(this.startPos, this.endPos);
            this.seekVideo(this.endPos);
        },

        updateValue() {
            this.updateDebounced({
                start: this.startTime,
                end: this.endTime,
            });
        },

    },

    watch: {
        
        startPos() {
            this.updateValue();
        },

        endPos() {
            this.updateValue();
        },

    },

};
</script>

<style>
/* .video-trim-controls {
    position: relative;
    height: 0;
    border: 1px solid red;
    input[type="range"] {
        appearance: none;
        background-color: transparent;
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
    }
    input[type="range"]::-moz-range-thumb,
    input[type="range"]::-webkit-slider-thumb {
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 100%;
        background-color: currentColor;
        cursor: pointer;
        pointer-events: all;
    }
    div {
        background-color: currentColor;
        opacity: 0.5;
        height: 6px;
        position: absolute;
        top: 5px;
        left: calc(100% * max(var(--start), 0));
        right: calc(100% * min((1 - var(--end)), 1));
    }
} */
</style>