<template>

    <div>
        <video
            :src="sourceUrl"
            class="w-full rounded mb-2"
            ref="video"
            controls
            @loadeddata="loadedVideo" />
        <div v-if="!loading" class="video-vtt-controls flex">
            <input
                type="range"
                class="w-full"
                min="0"
                max="1"
                step="any"
                :value="pos"
                @input="updatePos($event.target.value)" />
            <button
                class="btn-xs btn ml-2"
                @click="addChapter">
                Add
            </button>
        </div>
        <div v-if="!loading" class="video-vtt-chapters">
            <div
                v-for="chapter, index in chapters"
                class="flex mt-2 cursor-pointer rounded flex items-center p-1"
                :class="{
                    'bg-gray-400': index !== selected,
                    'bg-blue text-white': index === selected,
                }"
                @click="selectChapter(index)">
                <div
                    type="text"
                    isReadOnly
                    class="w-32 px-2">
                    {{ chapter.start }}
                </div>
                <input
                    type="text"
                    class="grow px-2 py-1 rounded bg-gray-100 text-gray-800"
                    placeholder="Title"
                    :value="chapter.title"
                    @input="updateChapter(index, { title: $event.target.value })" />
            </div>
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
            pos: 0,
            selected: 0,
            chapters: this.value.chapters ?? [],
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

        time() {
            return Math.round(this.duration * this.pos);
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

        updatePos(pos) {
            this.pos = pos;
            this.seekVideo(this.pos);
            this.updateChapter(this.selected, { start: this.time });
        },

        updateTime(time) {
            this.updatePos(time / this.duration);
        },

        addChapter() {
            this.chapters = [
                ...this.chapters,
                { 
                    start: this.time,
                    title: null,
                }
            ];
            this.selectChapter(this.chapters.length - 1);
        },

        selectChapter(index) {
            this.selected = index;
            this.updateTime(this.chapters[index].start);
        },

        updateChapter(index, value) {
            this.chapters = [
                ...this.chapters.slice(0, index),
                { ...this.chapters[index], ...value },
                ...this.chapters.slice(index + 1),
            ];
        },

        updateValue() {
            this.update({
                chapters: this.chapters,
            });
        },

    },

    watch: {
        
        chapters() {
            this.updateValue();
        },

    },

};
</script>