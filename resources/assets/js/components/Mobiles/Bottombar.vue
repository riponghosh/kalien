<template>
  <div class="container-fulid bottom-bg group_activity_bottom_bar">
     <button type="button" @click="join" class="btn btn-danger bottom-button"> {{ $t('JoinAndPay') }}</button>
  </div>
</template>


<script>
export default {
    props: ['gpActivityId'],
    ready() {
    },
    data() {
        return {
        }
    },
    methods: {

      join(){
        this.$swal({
          title: Vue.t('Join') + ' ？', //Join ?
          showCancelButton: true,
          confirmButtonClass: 'btn-success waves-effect waves-light',
          confirmButtonText: Vue.t('Yes'), //Go to payment
        })
        .then((response) => {
          this.$loader.methods.start();
          this.activity();
        });
        
      },
      activity(){
        let vm=this;
        axios.post('/api-web/v1/group_activity/apply_for_join_in', {
          gp_activity_id: this.gpActivityId,
          known_is_participant: true
        })
        .then(function (res) {
          vm.$loader.methods.stop();
          if(res.data.success){
            window.location.href="/payment";
          }else{
            if(res.data.msg != undefined && res.data.msg != null && res.data.msg != ''){
              alert(res.data.msg);
            }else{
              alert('參加失敗。');
            }
          }
        })
        .catch(function (error) {
          vm.$loader.methods.stop();
          if (error.response.status==401) {
            vm.$modal.show('login-modal',{ task: $.Callbacks().add(vm.activity),vals: {gp_activity_id: vm.gpActivityId} });
          }
        });
      
      }
      
    }
}
</script>

<style>
  .bottom-bg{
    background-color: #fff;
    position: fixed;
    height: 80px;
    bottom: 0;
    width: 100%;
    padding: 14px;
    box-shadow: 0px 0px 15px 0px #666;
  }
  .bottom-button{
    width: 100%;
    height: 50px;
    background: #DA2F66;
  }
</style>