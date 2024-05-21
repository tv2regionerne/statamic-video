<template>

    <div class="">
        <video
            :src="sourceUrl"
            class="w-full rounded-t"
            ref="video"
            controls
            @loadeddata="loadedVideo" />
        <div v-if="!loading" class="flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center">
            <span class="w-10 shrink-0 text-center text-xs">{{ times.start }}s</span>
            <input
                type="range"
                class="w-full"
                min="0"
                max="1"
                step="any"
                :value="positions.start"
                @input="updatePosition('start', $event.target.value)" />
            <input
                v-if="isRange"
                type="range"
                class="w-full"
                min="0"
                max="1"
                step="any"
                :value="positions.end"
                @input="updatePosition('end', $event.target.value)" />
            <span v-if="isRange" class="w-10 shrink-0 text-center text-xs">{{ times.end }}s</span>
            <span v-else></span>
        </div>
    </div>

</template>

<script>
export default {

    mixins: [
        Fieldtype,
    ],

    inject: ['storeName'],

    data() {
        return {
            loading: true,
            positions: {},
        };
    },

    computed: {
        
        sourceUrl() {
            return this.config.source_url || this.store.values[this.config.source];
        },
        
        store() {
            return this.$store.state.publish[this.storeName];
        },

        duration() {
            return this.$refs.video.duration;
        },
        
        isSingle() {
            return this.config.mode === 'single';
        },

        isRange() {
            return this.config.mode === 'range';
        },

        times() {
            return {
                start: Math.round(this.duration * this.positions.start),
                end: Math.round(this.duration * this.positions.end),
            };
        },

    },

    methods: {

        loadedVideo() {
            this.loading = false;
            this.positions = this.value !== null ? {
                start: this.value.start / this.duration,
                end: this.value.end / this.duration,
            } : {
                start: 0,
                end: 1,
            };
        },
        
        seekVideo(pos) {
            this.$refs.video.pause();
            this.$refs.video.currentTime = this.duration * pos;
        },

        updatePosition(key, pos) {
            this.positions[key] = pos;
            if (key === 'start') {
                this.positions.end = Math.max(this.positions.end, pos);
            }
            if (key === 'end'){
                this.positions.start = Math.min(this.positions.start, pos);
            }
            this.seekVideo(pos);
            this.updateValue();
        },

        updateValue() {
            this.updateDebounced(this.times);
        },

    },

};
</script>
<style>
.video_time-fieldtype {
    & input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 0.5rem;
        background: rgba(255 255 255 / 0.3);
        border-radius: 0.25rem;
    }
    & input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 1rem;
        height: 1rem;
        background: rgba(255 255 255 / 1);
        border-radius: 50%;
        cursor: pointer;
    }
}
</style>