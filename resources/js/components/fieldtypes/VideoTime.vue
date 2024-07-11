<template>

    <div class="">
        <video
            :src="sourceUrl"
            class="video_addon-preview w-full rounded-t bg-black"
            ref="video"
            controls
            @loadeddata="loadedVideo" />
        <div v-if="!loading" class="flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center">
            <div class="w-12 shrink-0 text-center text-xs">
                {{ timecode(item.start) }}
            </div>
            <div class="video_addon-track video_time-track">
                <input
                    type="range"
                    class="w-full"
                    :min="0"
                    :max="duration"
                    :value="item.start"
                    @input="updateTime('start', $event.target.value)" />
                <div v-if="isRange" :style="{
                    '--start': item.start / duration,
                    '--end': item.end / duration,
                }"></div>
                <input
                    v-if="isRange"
                    type="range"
                    class="w-full"
                    :min="0"
                    :max="duration"
                    :value="item.end"
                    @input="updateTime('end', $event.target.value)" />
            </div>
            <div v-if="isRange" class="w-12 shrink-0 text-center text-xs">
                {{ timecode(item.end) }}
            </div>
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
            item: null,
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
            return Math.round(this.$refs.video.duration * 1000);
        },
        
        isSingle() {
            return this.config.mode === 'single';
        },

        isRange() {
            return this.config.mode === 'range';
        },

    },

    methods: {

        loadedVideo() {
            this.loading = false;
            this.item = (this.value.start !== null || this.value.end !== null) 
                ? this.value
                : { start: 0, end: this.duration };
        },
        
        seekVideo(value) {
            this.$refs.video.pause();
            this.$refs.video.currentTime = value / 1000;
        },

        updateTime(key, value) {
            let time;
            if (this.isRange) {
                const min = key === 'start' ? 0 : this.item.start;
                const max = key === 'start' ? this.item.end : this.duration;
                time = Math.min(max, Math.max(min, parseInt(value)));
            } else {
                time = parseInt(value);
            }
            this.updateItem({ [key]: time });
            this.seekVideo(time);
        },

        updateItem(value) {
            this.item = { ...this.item, ...value };
            this.updateValue();
        },

        updateValue() {
            this.updateDebounced(this.config.null_limits ? {
                start: this.item.start > 0 ? this.item.start : null,
                end: this.item.end < this.duration ? this.item.end : null,
            } : this.item);
        },

        timecode(value) {
            const seconds = Math.round(value / 1000);
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },

    },

};
</script>