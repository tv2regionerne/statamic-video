<template>

    <div class="video_addon-wrapper" :style="wrapperStyle">
        <video
            :src="sourceUrl"
            class="video_addon-preview w-full rounded-t bg-black"
            crossorigin="anonymous"
            ref="video"
            controls
            @loadeddata="loadedVideo"
            @seeked="seekedVideo" />
        <div v-if="!loading" class="flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center">
            <div class="video_addon-track video_text-track">
                <input
                    v-if="selectedItem"
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
                :class="{ 
                    'video_text-selected': item.id === selected,
                    'video_text-basic': config.mode === 'basic',
                    'video_text-advanced': config.mode === 'advanced',
                }"
                @click="selectItem(item)">
                <div class="video_text-item-timecode">
                    {{ timecode(item.start) }}
                </div>
                <div class="video_text-item-thumbnail">
                    <img :src="itemThumbnail(item)" />
                    <template v-if="selectedIndex === index">
                        <div v-if="!meta.id" class="text-gray-700">
                            {{ __('statamic::fieldtypes.assets.dynamic_folder_pending_save') }}
                        </div>
                        <button type="button" @click.stop="snapThumbnail()" v-if="meta.id">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600 cursor-pointer hover:text-blue-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                        </button>
                    </template>
                </div>
                <div class="video_text-item-input">
                    <text-input
                        class="video_text-item-text"
                        placeholder="Text"
                        :value="item.text"
                        @focus="selectItem(item)"
                        @input="updateItem({ text: $event })" />
                    <textarea-input
                        class="video_text-item-description"
                        placeholder="Description"
                        :value="item.description"
                        @focus="selectItem(item)"
                        @input="updateItem({ description: $event })" />
                    <button v-if="index !== 0 && selectedIndex === index" @click.stop="deleteItem()" type="button" class="text-gray-600 cursor-pointer px-2 hover:text-blue-500">
                        <span>×</span>
                    </button>
                </div>
                <div class="video_text-item-timecode">
                    {{ timecode(item.end) }}
                </div>
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
            skipNextSeek: false,
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

        wrapperStyle() {
            if (this.loading) {
                return {};
            }
            return {
                '--video-aspect-ratio': this.$refs.video.videoWidth / this.$refs.video.videoHeight,
            };
        },

    },

    methods: {

        loadedVideo() {
            this.loading = false;
            this.items = this.value;
            if (this.items.length) {
                this.selectItem(this.items[0]);
                this.syncItems();
            }
        },

        seekedVideo(ev) {
            if (!this.selectedItem) {
                return;
            }
            if (this.skipNextSeek) {
                this.skipNextSeek = false;
                return;
            }
            const seekTime = ev.target.currentTime * 1000;
            const item = this.items.find((item) => item.start <= seekTime && item.end >= seekTime);
            if (item) {
                this.selectItem(item, false);
            }
        },

        addItem() {
            const item = { 
                start: this.selectedItem
                    ? Math.min(this.selectedItem.start + 1000, this.duration)
                    : 0,
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
            this.skipNextSeek = true;
            this.$refs.video.pause();
            this.$refs.video.currentTime = value / 1000;
        },

        updateItemStart(value) {
            if (!this.selectedItem) {
                return;
            }
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

        selectItem(item, seek = true) {
            this.selected = item.id;
            if (seek) {
                this.seekVideo(this.selectedItem.start);
            }
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

        snapThumbnail() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = this.$refs.video.videoWidth;
            canvas.height = this.$refs.video.videoHeight;
            ctx.drawImage(this.$refs.video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg');
            canvas.remove();

            this.updateItem({ thumbnail: dataUrl });
        },

        itemThumbnail(item) {
            if (item.thumbnail?.startsWith('data:')) {
                return item.thumbnail;
            }
            if (this.meta.thumbnails[item.id]) {
                return this.meta.thumbnails[item.id];
            }
            return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'; // transparent gif
        },

    },

    watch: {

        items(value) {
            this.updateDebounced(value);
        },

    },

};
</script>