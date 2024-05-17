import Subtitles from './components/fieldtypes/Subtitles.vue'

Statamic.booting(() => {
    Statamic.component('subtitles-fieldtype', Subtitles)
})
