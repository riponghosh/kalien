require('.././bootstrap');
import Tab from '.././components/Mobiles/Ticket/Tab.vue'
import store from '.././store'

new Vue({
    el: '#app2',
    store,
    components: {Tab}
})