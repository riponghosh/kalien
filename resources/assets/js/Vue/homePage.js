require('.././bootstrap');
import Slider from '.././components/Mobiles/HomePage/Slider.vue'
import cardcomponent from '.././components/Mobiles/HomePage/Card.vue'
import store from '.././store'

new Vue({
    el: '#app2',
    store,
    components: {Slider,cardcomponent}
})