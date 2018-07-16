<template>
  <div class="container-fulid header-slider">
    <tiny-slider :mouse-drag="true" :autoplay-button-output="false" :items="1" :controls="false" :autoplay="true" :autoplay-timeout="4000" :gutter="20">
      <!--url:this.product.trip_gallery_pic-->
      <div>
        <img 
        :src="this.product.trip_gallery_pic"
        style="width: 100%;" @error="imageRemove($event)" >
      </div>
    </tiny-slider>

    <header-section
      :organiser-name=this.organiser.name
      :start-date=this.groupActivity.start_date
      :start-time=this.groupActivity.start_time
      :price=this.groupActivity.joining_fee
      :price-unit=this.groupActivity.joining_fee_unit
      :activity-name=this.groupActivity.activity_title
      :location=this.product.map_address
      :organiser-avatar=this.organiser.sm_avatar
      :participants=this.groupActivity.participants
      :gp-is-achieved=this.groupActivity.is_achieved
      :gp-not-achieved-reason=this.groupActivity.forbidden_reason
      :trip_activity_tickets=this.product.trip_activity_tickets
    ></header-section>

    <hr>
    <product-section
      :pdt-name=this.product.title
      :pdt-package-name=this.groupActivity.product_name
      :short-intros=this.product.trip_activity_short_intros
      :rule-info=this.product.rule_infos
    ></product-section>

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
  </div>
</template>


<script>
import VueTinySlider from 'vue-tiny-slider';
import HeaderSection from './GroupActivity/HeaderSection.vue'
import ProductSection from './GroupActivity/ProductSection.vue'
import IntroSection from './GroupActivity/Intro.vue'
import CustomerRightSection from './GroupActivity/CustomerRightSection.vue'
import LocationSection from './GroupActivity/LocationSection.vue'
import RefundSection from './GroupActivity/RefundSection.vue'
import FAQsSection from './GroupActivity/FAQsSection.vue'
export default {
  components: {
    'tiny-slider': VueTinySlider,
    'header-section': HeaderSection,
    'product-section': ProductSection,
    'intro-section': IntroSection,
    'customer-right-section': CustomerRightSection,
    'location-section': LocationSection,
    'refund-section': RefundSection,
    'faqs-section': FAQsSection
  },
  props: {
    groupActivity: {
      type: Object
    },
    product: {
      type: Object
    },
    organiser: {
      type: Object
    },
    refundForbiddenWhenGpAchieved: {
      type: Boolean
    }
  },
  created: function() {
    console.log('Group Activity ready.');
    console.log(this.groupActivity.activity_title)
  },
  data() {
      return {
        openingTimes: [
          {openTime: this.product.open_time, closeTime: this.product.close_time}
        ]
      }
  },
  methods: {
    imageRemove(event){
      console.log(event.target.outerHTML='<div style="height:235px;background:grey"></div>')
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
</style>