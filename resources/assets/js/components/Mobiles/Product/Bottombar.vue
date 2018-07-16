<template>
  <div class="container-fulid bottom-bg group_activity_bottom_bar">
     <button @click="showCreateEventPage" type="button" class="btn btn-danger bottom-button">{{ $t('Create Group Event') }}</button>
  </div>
</template>


<script>
export default {
    ready() {
    },
    data() {
        return {
        }
    },
    methods: {
    	showCreateEventPage(){
    		this.$emit('create-event-method', true);
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
