<template>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
export default {
  props: ['auth','lang','curUnit','curRate','userAvatar'],
  created: function (){
    this.setLanguage(this.lang);
    Vue.config.lang=this.lang;
    this.setCurrency(this.curUnit);
    this.setUserIcon(this.userAvatar);
    this.setCurrencyRate(JSON.parse(this.curRate));
      if(this.auth == true){
        this.actionAuthChecked();
        
      }
  },
    methods: mapActions([
      'actionAuthChecked',
      'setLanguage',
      'setCurrency',
      'setCurrencyRate',
      'setUserIcon'
    ]),
  }
</script>
