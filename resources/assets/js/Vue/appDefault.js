require('.././bootstrap');
import Navbar from '.././components/NavBar/NavBar.vue'
import Init from '.././components/Init.vue'
import store from '.././store'


window.app = new Vue({
    el: '#app',
    store,
    components: { Navbar, Init},
    methods :{
    	fbLoginResponse: function(data){
    		this.$refs.login.fbLoginResponse(data)
    	}
    }
})

            

