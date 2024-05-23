<template>

    <div>
        <video
            :src="sourceUrl"
            class="w-full rounded-t"
            ref="video"
            controls
            @loadeddata="loadedVideo" />
        <div v-if="!loading" class="flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center">
            <div class="video_addon-track video_text-track">
                <input
                    type="range"
                    class="w-full"
                    :min="0"
                    :max="duration"
                    :value="items[selected].start"
                    @input="updateItemStart(selected, $event.target.value)" />
                <div class="video_text-chapters">
                    <div v-for="item, index in items" :style="{
                        '--start': item.start / duration,
                        '--end': (items[index + 1]?.start || duration) / duration,
                    }" v-tooltip="item.text || 'Unnamed'" @click="selectItem(index)"></div>
                </div>
            </div>
        </div>
        <div v-if="!loading">
            <div
                v-for="item, index in items"
                class="mt-2 cursor-pointer relative video_text-item"
                :class="{ 'video_text-selected': index === selected }"
                @click="selectItem(index)">
                <text-input
                    :prepend="timecode(item.start)"
                    :append="timecode(item.end)"
                    class="w-full"
                    placeholder="Text"
                    :value="item.text"
                    @focus="selectItem(index)"
                    @input="updateItem(index, { text: $event })" />
                <button v-if="index !== 0" @click="deleteItem(index)" type="button" class="text-gray-600 cursor-pointer px-2 hover:text-blue-500">
                    <span>×</span>
                </button>
            </div>
            <button class="btn mt-2" @click="addItem">Add Chapter</button>
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
            selected: 0,
            items: {},
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
            return Math.round(this.$refs.video.duration);
        },

        positions() {
            return this.items.map(item => item.start / this.duration);
        },

    },

    methods: {

        loadedVideo() {
            this.loading = false;
            this.items = this.value;
            this.updateItem(this.items.length - 1, { end: this.duration });
        },

        addItem() {
            this.items = [
                ...this.items,
                { 
                    start: Math.min(this.items[this.items.length - 1].start + 1, this.duration),
                    text: null,
                }
            ];
            this.updateValue();
            this.selectItem(this.items.length - 1);
        },
        
        seekVideo(value) {
            this.$refs.video.pause();
            this.$refs.video.currentTime = value;
        },

        updateItemStart(index, value) {
            const min = index !== 0 ? (this.items[index - 1]?.start || 0) : 0;
            const max = index !== 0 ? (this.items[index + 1]?.start || this.duration) : 0;
            const time = Math.min(max, Math.max(min, parseInt(value)));
            this.updateItem(index, { start: time });
            if (index > 0) {
                this.updateItem(index - 1, { end: time });
            }
            this.seekVideo(time);
        },

        updateItem(index, value) {
            this.items = [
                ...this.items.slice(0, index),
                { ...this.items[index], ...value },
                ...this.items.slice(index + 1),
            ];
            this.updateValue();
        },

        updateValue() {
            this.update(this.items);
        },

        deleteItem(index) {
            this.items = [
                ...this.items.slice(0, index),
                ...this.items.slice(index + 1),
            ]
            this.updateValue();
            this.selectItem(0)
        },

        selectItem(index) {
            this.selected = index;
        },

        timecode(value) {
            const minutes = Math.floor(value / 60);
            const seconds = value % 60;
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },

    },

    watch: {

        selected() {
            const position = this.positions[this.selected];
            this.seekVideo(position);
        },

    },

};
</script>