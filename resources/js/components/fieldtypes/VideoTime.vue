<template>

    <div class="">
        <video
            :src="sourceUrl"
            class="w-full rounded-t"
            ref="video"
            controls
            @loadeddata="loadedVideo" />
        <div v-if="!loading" class="flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center">
            <div class="w-10 shrink-0 text-center text-xs">{{ times.start }}s</div>
            <div class="video_time-track">
                <input
                    type="range"
                    class="w-full"
                    min="0"
                    max="1"
                    step="any"
                    :value="positions.start"
                    @input="updatePosition('start', $event.target.value)" />
                <div v-if="isRange" :style="{
                    '--start': positions.start,
                    '--end': positions.end,
                }"></div>
                <input
                    v-if="isRange"
                    type="range"
                    class="w-full"
                    min="0"
                    max="1"
                    step="any"
                    :value="positions.end"
                    @input="updatePosition('end', $event.target.value)" />
            </div>
            <div v-if="isRange" class="w-10 shrink-0 text-center text-xs">{{ times.end }}s</div>
            <div v-else></div>
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
            console.log(this.value);
            this.positions = (this.value.start !== null || this.value.end !== null) ? {
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
            this.updateDebounced(this.config.null_limits ? {
                start: this.times.start > 0 ? this.times.start : null,
                end: this.times.end < this.duration ? this.times.end : null,
            } : this.times);
        },

    },

};
</script>
<style>
.video_time-track {
    height: 1rem;
    position: relative;
    flex-grow: 1;
    background-color: rgba(255 255 255 / 0.15);
    border-radius: 0.25rem;
    & input[type="range"] {
        -webkit-appearance: none;
        position: absolute;
        inset: 0;
        width: 100%;
        height: 1rem;
        background-color: transparent;
        pointer-events: none;
        z-index: 1;
    }
    & input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 11px;
        height: 1rem;
        background: repeating-linear-gradient(
            to right,
            transparent,
            transparent 1px,
            white 1px,
            white 2px
        );
        border: 3px solid white;
        cursor: grab;
        pointer-events: all;
    }
    & input[type="range"]:first-child::-webkit-slider-thumb {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    & input[type="range"]:last-child::-webkit-slider-thumb {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    & div {
        position: absolute;
        height: 1rem;
        border: solid white;
        border-width: 1px 0;
        left: calc((100% - 0.75rem) * var(--start));
        right: calc((100% - 0.75rem) * (1 - var(--end)));
        pointer-events: none;
        background-color: rgba(255 255 255 / 0.3);
        border-radius: 0.25rem;
        z-index: 10;
    }
}
</style>