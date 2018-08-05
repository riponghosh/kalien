<template>
  <div class="container-fulid header-slider">
    <div class="product-image">
      <div>
        <img 
        :src="this.product.trip_gallery_pic"
        style="width: 100%;" @error="imageRemove($event)" >
        
      </div>
      <div class="product-title">
        <span>{{product.title}}</span>
      </div>
    </div>
    <special-offer
    :trip_activity_tickets=this.trip_activity_tickets
    ></special-offer>
    <header-info
    :pdt-name=this.product.title
    :trip_activity_tickets=this.trip_activity_tickets
    :short-intros=this.product.trip_activity_short_intros
    :rule-info=this.product.rule_infos
    ></header-info>

    <hr>
    <package-section
    :trip_activity_tickets=this.product.trip_activity_tickets
    v-on:create-event-method="ceateEventAction"
    :create-event-id.sync="ceateEventId"
    :event-package-name.sync="eventPackageName"
    :min-participant-gp-activity.sync="minParticipantGpActivity"
    :max-participant-gp-activity.sync="maxParticipantGpActivity"
    ></package-section>
    <hr>
    <intro-section
    :desc=this.product.description
    :pdt-media=this.product.media
    ></intro-section>
    <hr>
    <customer-right-section
    :customer_rights=this.product.customer_rights
    ></customer-right-section>

    <hr>
    <location-section
    :address=this.product.map_address
    :tel-area-code=this.product.tel_area_code
    :tel=this.product.tel
    :opening-times=this.openingTimes
    ></location-section>

    <hr>
    <refund-section
    :rules=this.product.trip_activity_refund_rules
    :refund-forbidden-when-gp-achieved=this.refundForbiddenWhenGpAchieved
    ></refund-section>
    <hr>
    <faqs-section>
    </faqs-section>
    <bottombar-section 
    v-on:create-event-method="ceateEventAction"
    ></bottombar-section>
    <create-event-section
    v-show="createEvent"
    v-on:create-event-method="ceateEventAction"
    ref="createEventPage"
    ></create-event-section>
  </div>
</template>


<script>
  import HeaderInfo from './HeaderInfo.vue'
  import PackageSection from './package.vue'
  import IntroSection from './../GroupActivity/Intro.vue'
  import CustomerRightSection from './../GroupActivity/CustomerRightSection.vue'
  import LocationSection from './../GroupActivity/LocationSection.vue'
  import RefundSection from './../GroupActivity/RefundSection.vue'
  import FaqsSection from './../GroupActivity/FAQsSection.vue'
  import BottombarSection from './Bottombar.vue'
  import CreateEventSection from './CreateEvent.vue'
  import SpecialOffer from './SpecialOffer.vue'
  export default {
    props:['product','trip_activity_tickets'],
    components: {
      HeaderInfo,
      PackageSection,
      IntroSection,
      CustomerRightSection,
      LocationSection,
      RefundSection,
      FaqsSection,
      BottombarSection,
      SpecialOffer,
      CreateEventSection
    },
    created: function() {
      console.log('Product page ready.');
    },
    data() {
      return {
        ceateEventId:null,
        minParticipantGpActivity: null,
        maxParticipantGpActivity:null,
        eventPackageName:'',
        openingTimes: [
        {openTime: this.product.open_time, closeTime: this.product.close_time}
        ],
        createEvent:false
      }
    },
    watch: {
      /*
      ceateEventId: function(id){
        if (id) {
          this.setEventId(id);
        }
      },
      eventPackageName: function(name){
        this.setEventName(name);
      },
      minParticipantGpActivity:function(value){
        alert('ddd')
        this.setMinEventJoiners(value);
      },
      maxParticipantGpActivity: function(value){
        this.setMaxEventJoiners(value);
      },
      */
      createEvent:function(value){
        let bodyTag=document.getElementsByTagName("body")[0];
        if (!value) {
          this.ceateEventId=null;
          bodyTag.style.overflow='initial';
        }
        else{
          bodyTag.style.overflow='hidden';
        }
      }
    },
    methods: {
      imageRemove(event){
        event.target.outerHTML='<div style="height:235px;background:grey"></div>';
      },
      showCreateEventPage(value){
        let vm = this
        axios.post('/Authorization/Check', {}).then(function(res){
          if(res.data.success == false) return;
          if(!vm.ceateEventId) {
            vm.setEventId(vm.trip_activity_tickets[0].id);
            vm.setEventName(vm.trip_activity_tickets[0].name);
            vm.setMinEventJoiners(vm.trip_activity_tickets[0].min_participant_for_gp_activity);
            vm.setMaxEventJoiners(vm.trip_activity_tickets[0].max_participant_for_gp_activity);
          }else{
            vm.setEventId(vm.ceateEventId);
            vm.setEventName(vm.eventPackageName);
            vm.setMinEventJoiners(vm.minParticipantGpActivity);
            vm.setMaxEventJoiners(vm.maxParticipantGpActivity);
          }
          vm.createEvent=value;
        }).catch(function (error) {
          if (error.response.status==401) {
            vm.$modal.show('login-modal'
              ,{ task: $.Callbacks().add(vm.showCreateEventPage),vals: value }
            );
          }
        });
      },
      setEventId(id){
        this.$refs.createEventPage.tripActivityTicketId=id;
      },
      setEventName(name){
        this.$refs.createEventPage.packageName=name;
      },
      setMinEventJoiners(value){
        //this.$refs.createEventPage.minParticipantGpActivity=value;
        this.$refs.createEventPage.setMinEventJoiners(value)
      },
      setMaxEventJoiners(value){
        //this.$refs.createEventPage.maxParticipantGpActivity=value;
        this.$refs.createEventPage.setMaxEventJoiners(value)
      },
      ceateEventAction: function (newValue){
        if (this.trip_activity_tickets.length>1) {
          let elmnt = document.getElementById("content");
          elmnt.scrollIntoView({block: "center"});
          if (this.ceateEventId) {
            this.showCreateEventPage(newValue);
          }
        }
        else{
          this.showCreateEventPage(newValue);
        }
      }
    }
  }
</script>

<style scoped>
.header-slider{
  padding-top: 52px !important;
  background: #fff;
}
body {
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  height: 100%;
  width: 100%; 
  background: #FFF;
  font-family: 'Roboto', sans-serif;
  font-weight: 400;
  color: #000;
}
hr{
  border-width: 2px;
  /*border: 3px solid #f1f1f1;*/
  margin: 30px 0px 20px 0px;
  clear: both;
}
.product-image{
  position: relative;
  margin-bottom: 15px;
}
.product-title{
  font-size: 25px;
  padding: 20px;
  width: 100%;
  border-bottom: 1px solid #dce0e0;
}
</style>
