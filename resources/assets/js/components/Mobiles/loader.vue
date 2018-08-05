<template>
  <div class="display-cover" id="display-cover" style="display: none;">
    <div id="square">
      <div id="loader"></div>     
      <div id="loader-text-position"><h5 id="loader-text">Loading</h5></div>
    </div>
  </div>
</template>


<script>

  export default {
    created: function() {
      console.log('Loader page ready.');
      
    },
    data() {
      return {
        timeid:'',
        previousScroll:false
      }
    },
    methods: {
      defaultShow(){
        this.timeid=setTimeout(this.processFail, 5000);
      },
      start(name){
        let bodyTag=document.getElementsByTagName("body")[0];
        if (bodyTag.style.overflow=='hidden') {
          this.previousScroll=true;
        }
        bodyTag.style.overflow='hidden';
        document.getElementById("display-cover").style.display = "block";
        if (name) {
          document.getElementById("loader-text").innerHTML = name;
        }
        this.defaultShow();
      },
      stop(){
        document.getElementById("display-cover").style.display = "none";
        window.clearTimeout(this.timeid);
        if (!this.previousScroll) {
          let bodyTag=document.getElementsByTagName("body")[0];
          bodyTag.style.overflow='initial';
        }
      },
      processFail(){
        let vm=this;
        Vue.prototype.$swal({
          title: "Processs Fail",
          text: "Do you want to reload the page ?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: 'btn-success waves-effect waves-light',
          confirmButtonText: Vue.t('Yes'), 
        })
        .then((response) => {
          window.location.reload();
        }, function(dismiss) {
          Vue.prototype.$loader.methods.stop();
        });
        
        
      }
      
    }
  }
</script>

<style lang="css" scoped>
.display-cover{
  position: fixed;
  height: 100%;
  width: 100%;
  background: rgba(0,0,0,.4);
  z-index: 999990;
}
#loader {
  margin: 0 auto;
  margin-top: 20px;
  width: 50px;
  height: 50px;
  border: 2px solid #f3f3f3;
  border-radius: 50%;
  border-top: 2px solid #3498db;
  width: 45px;
  height: 45px;
  -webkit-animation: spin 1.5s linear infinite;
  animation: spin 1s linear infinite;
}
#square{
  position: absolute;
  left: 50%;
  top: 40%;
  margin: 0 0 0 -55px;
  z-index: 1;
  background: #FFF;
  border-radius: 8px;
  width: 110px;
  height: 110px
}
#loader-text-position{
  margin: 0 auto;
  margin-top: 15px;
  width: 150px;
  height: 150px;
  width: 100px;
  height: 80px;
}
#loader-text{
  margin: 0 auto;
  text-align: center;
}
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

</style>