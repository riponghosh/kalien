require('.././bootstrap');
import gcompomnent from '.././components/Mobiles/GroupActivity.vue'
import Bottombar from '.././components/Mobiles/Bottombar.vue'
import store from '.././store'

new Vue({
    el: '#app2',
    store,
    components: { gcompomnent,Bottombar}
})