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
                    :value="selectedItem.start"
                    @input="seekVideo($event.target.value)" 
                    @change="updateItemStart($event.target.value)" />
                <div class="video_text-chapters">
                    <div v-for="item in items" :style="{
                        '--start': item.start / duration,
                        '--end': item.end / duration,
                    }" v-tooltip="item.text || 'Unnamed'" @click="selectItem(item)"></div>
                </div>
            </div>
        </div>
        <div v-if="!loading">
            <div
                v-for="item, index in items"
                class="mt-2 cursor-pointer relative video_text-item"
                :class="{ 'video_text-selected': item.id === selected }"
                @click="selectItem(item)">
                <text-input
                    :prepend="timecode(item.start)"
                    :append="timecode(item.end)"
                    class="w-full"
                    placeholder="Text"
                    :value="item.text"
                    @focus="selectItem(item)"
                    @input="updateItem({ text: $event })" />
                <button v-if="index !== 0" @click.stop="deleteItem()" type="button" class="text-gray-600 cursor-pointer px-2 hover:text-blue-500">
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
            selected: null,
            items: [],
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
            return Math.round(this.$refs.video.duration * 1000);
        },

        selectedIndex() {
            return this.items.findIndex((item) => item.id === this.selected);
        },

        selectedItem() {
            return this.items[this.selectedIndex];
        },

    },

    methods: {

        loadedVideo() {
            this.loading = false;
            this.items = this.value.map((item) => ({
                ...item,
                id: uniqid(),
            }));
            this.selectItem(this.items[0]);
            this.syncItems();
        },

        addItem() {
            const item = { 
                start: Math.min(this.selectedItem.start + 1000, this.duration),
                end: null,
                text: null,
                id: uniqid(),
            };
            this.items = [
                ...this.items.slice(0, this.selectedIndex),
                item,
                ...this.items.slice(this.selectedIndex),
            ];
            this.syncItems();
            this.selectItem(item);
        },
        
        seekVideo(value) {
            this.$refs.video.pause();
            this.$refs.video.currentTime = value / 1000;
        },

        updateItemStart(value) {
            this.updateItem({ start: value });
            this.seekVideo(value);
        },

        updateItem(value) {
            this.items = [
                ...this.items.slice(0, this.selectedIndex),
                { ...this.items[this.selectedIndex], ...value },
                ...this.items.slice(this.selectedIndex + 1),
            ];
            this.syncItems();
        },

        deleteItem() {
            this.items = [
                ...this.items.slice(0, this.selectedIndex),
                ...this.items.slice(this.selectedIndex + 1),
            ]
            this.syncItems();
            this.selectItem(this.items[0]);
        },

        selectItem(item) {
            this.selected = item.id;
        },

        syncItems() {
            this.items = this.items
                .sort((a, b) => a.start - b.start)
                .map((item, index) => {
                    if (index === 0) {
                        item = { ...item, start: 0 }
                    }
                    if (index === this.items.length - 1 ) {
                        item = { ...item, end: this.duration }
                    } else {
                        item = { ...item, end: this.items[index + 1].start };
                    }
                    return item;
                });
        },

        timecode(value) {
            const seconds = Math.round(value / 1000);
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        },

    },

    watch: {

        selected() {
            this.seekVideo(this.selectedItem.start);
        },

        items(value) {
            this.updateDebounced(value);
        },

    },

};
</script>