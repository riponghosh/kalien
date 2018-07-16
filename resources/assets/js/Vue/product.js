require('.././bootstrap');
import product from '.././components/Mobiles/Product/product.vue'
import store from '.././store'

new Vue({
    el: '#app2',
    store,
    components: {product}
})
