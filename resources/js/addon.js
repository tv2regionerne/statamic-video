import '../css/addon.css'

import VideoText from './components/fieldtypes/VideoText.vue'
import VideoTime from './components/fieldtypes/VideoTime.vue'

Statamic.booting(() => {
    Statamic.component('video_text-fieldtype', VideoText)
    Statamic.component('video_time-fieldtype', VideoTime)
})
