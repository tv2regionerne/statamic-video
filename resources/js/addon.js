import VideoVtt from './components/fieldtypes/VideoVtt.vue'
import VideoTrim from './components/fieldtypes/VideoTrim.vue'

Statamic.booting(() => {
    Statamic.component('video_vtt-fieldtype', VideoVtt)
    Statamic.component('video_trim-fieldtype', VideoTrim)
})
