<template>
  <div>
    <div class="row-bg" v-for="group_activitie in temp_group_activities">
      <div v-if="group_activitie.gp_cards.length>0">
          
        <h5 class="row-name" style="margin: 0px;">{{group_activitie.title}}</h5>
        <tiny-slider :mouse-drag="true" :items="1" :controls="false" :loop="false" :gutter=10 :fixed-width=290 :edge-padding=20>
          <div v-for="gp_card in group_activitie.gp_cards">
              <section class="wrapper" style="width: 290px">
                <div class="container-fostrap">
                    <div class="content">
                        <div class="" @click="windowRedirect(gp_card.gp_activity_id)">
                            <div class="">
                                <div class="" >
                                    <div class="card-image">
                                      <img :src="gp_card.product_gallery_image" style="width: 100%;height: 157px;object-fit: cover;">
                                      <div class="card-image-over-text">
                                        <p>
                                          {{gp_card.product_name}}
                                        </p>
                                      </div>
                                      <div class="card-image-over-day-bg">
                                        <p class="card-image-over-day">
                                          {{ gp_card.start_datetime | moment("ddd") }}
                                        </p>
                                      </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-read-more" style="width: 100%;padding-top: 10px;padding-bottom: 10px;height: 50px;">
                                            <div style="width: 30%;float: left;margin-top: -40px;overflow: scroll;padding-bottom: 5px;">
                                              <img :src="gp_card.host.sm_avatar" @error="gp_card.host.sm_avatar='/img/icon/user_icon_bg.png'" class="img-circle error-validate avater-img" alt="Cinque Terre" style="height: 56px;width: 56px;">
                                            </div>
                                            <div style="width: 43%;float: left;text-align: left;">
                                              <span class="name">{{gp_card.host.name}}</span>
                                            </div>
                                        </div>
                                        <div class="card-content" style="padding: 0px 12px;">
                                            <h4 class="card-title" style="margin-bottom: 0px;">
                                                <span class="Language-Exchange-Di">  {{gp_card.activity_title == '' ? gp_card.product_name : gp_card.activity_title}}
                                              </span>
                                            </h4>
                                        </div>
                                        <div class="card-content" style="padding: 5px 12px;">                                
                                          <span class="icon-calendar calendar"></span>
                                          <span class="pm">{{ gp_card.start_datetime | moment("ddd MM.DD.YY | h:mm") }}</span>
                                        </div>
                                        <div class="card-read-more" style="text-align: left;padding: 15px;">
                                            
                                            <img v-for="(participant,key) in gp_card.participants" v-if="key<5" :src="participant.sm_avatar" @error="participant.sm_avatar='/img/icon/user_icon_bg.png'" class="img-circle error-validate" alt="Cinque Terre" style="height: 28px;width: 28px;margin-right: 10px;margin-top: 5px;"> 
                                            <span class="title">{{gp_card.participants.length}} {{ $t('Joiners') }}</span>
                                        </div>
                                        <div class="card-content" style="padding: 5px 12px;">
                                            <h4 class="card-title">
                                                <span class="Language-Exchange-Di" style="color: #1b1b1b;font-size: 16px;">{{priceUnitPrint}} {{setCurrencyPrice(gp_card.price,gp_card.price_unit)}}
                                              </span>
                                              <span style="float: right;font-size: 14px;color: #9b9b9b;margin-top: 8px"> <span class="icon-location-pin calendar"></span> {{addressLimited(gp_card.location)}}</span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
          </div>
        </tiny-slider>
      </div>
    </div>
  </div>
</template>


<script>
import {mapGetters, mapActions} from 'vuex'
import VueTinySlider from 'vue-tiny-slider';
export default {
  props:['group_activities'],
	components: {
    'tiny-slider': VueTinySlider
  },
  created: function() {
    console.log('Home page card ready.');
    this.temp_group_activities=JSON.parse(JSON.stringify(this.group_activities))
    this.temp_group_activities[1].gp_cards.push(this.temp_group_activities[1].gp_cards[0])
    // this.temp_group_activities[1].gp_cards.push(this.temp_group_activities[1].gp_cards[0])
    // this.temp_group_activities[1].gp_cards.push(this.temp_group_activities[1].gp_cards[0])
    this.currencyUnit=this.getCurrency();
    this.currencyRate=this.getCurrencyRate();
    this.setCurrencyUnit();
    this.imgErrorHandle();
  },
  data() {
    return {
      priceUnitPtr : {
        TWD: 'NT$',
        HKD: 'HK$',
        USD: 'US$',
        JPY: 'JP¥'
      },
      priceUnitPrint:'',
      currencyUnit:'',
      currencyRate:Object,
        temp_group_activities:Object
    }
  },
  methods: {
    setCurrencyUnit(){
      this.priceUnitPrint = this.priceUnitPtr[this.currencyUnit];
    },
    setCurrencyPrice(price,priceUnit){
      return (price*this.currencyRate[this.currencyUnit]/this.currencyRate[priceUnit]).toFixed(0);
    },
    addressLimited(location){
      if ( location.length > 6 ) {
        return location.substring(0,6) + '...'
      } else {
        return location
      }
    },
    imgErrorHandle(){
      setTimeout(function(){
        let image = document.getElementsByClassName("error-validate");
        for(let i=0;i<image.length;i++){
          if(image[i].src == window.location){
            image[i].src="/img/icon/user_icon_bg.png"
          }
        }
      },1000)
    },
    windowRedirect(url){
      window.location.href='/group_events/'+url
    },
    ...mapGetters([
      'getCurrency',
      'getCurrencyRate'
    ])
    
  }
}
</script>

<style scoped>
  .row-name{
    /*width: 109px;*/
    height: 25px;
    font-size: 18px;
    font-weight: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
    padding: 15px 0px 35px 10px;
  }
  .avater-img{
    border: 2px solid #fff;
    border-radius: 52%;
    box-shadow: 0px 2px 0px rgba(199, 194, 194, 0.8);
  }
  .row-bg{
    background: #fff;
  }
  .card-image{
    position: relative;
  }
  .card-image-over-text{
    position: absolute;
    top: 0;
    /*width: 123px;*/
    height: 19px;
    font-size: 18px;
    font-weight: bold;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #ffffff;
    margin: 13px;
  }
  .card-image-over-day-bg{
    position: absolute;
    right: 10px;
    bottom:10px;
    border-radius: 3px;
    background-color: #e35d88;
  }
  .card-image-over-day{
    /*width: 25px;*/
    color: #ffffff;
    margin:0 auto;
    padding: 2px 9px 2px 9px;
    font-size: 12px;
    font-weight: 300;
  }

  /*card section*/
  .wrapper {
    display: table;
    height: 100%;
    width: 100%;
  }

  .container-fostrap {
    //display: table-cell;
    //padding: 2px;
    text-align: center;
    vertical-align: middle;
  }

  h1.heading {
    color: #fff;
    font-size: 1.15em;
    font-weight: 900;
    margin: 0 0 0.5em;
    color: #505050;
  }
  @media (min-width: 450px) {
    h1.heading {
      font-size: 3.55em;
    }
  }
  @media (min-width: 760px) {
    h1.heading {
      font-size: 3.05em;
    }
  }
  @media (min-width: 900px) {
    h1.heading {
      font-size: 3.25em;
      margin: 0 0 0.3em;
    }
  } 
  .card {
    display: block; 
    margin-bottom: 20px;
    line-height: 1.42857143;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); 
    transition: box-shadow .25s; 
    position: relative;
  }
  .card:hover {
    box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
  }

  .card-content {
    padding:15px;
    text-align:left;
  }
  .card-title {
    margin-top:0px;
    font-weight: 700;
    font-size: 1.65em;
  }
  .card-title a {
    color: #000;
    text-decoration: none !important;
  }
  .card-read-more a {
    text-decoration: none !important;
    padding:10px;
    font-weight:600;
    text-transform: uppercase
  }
  /*===============================*/
  .name{
    width: 84px;
    height: 19px;
    font-size: 16px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
  }
  .Event-Owner {
    width: 70px;
    height: 14px;
    opacity: 0.5;
    font-size: 12px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
  }
  .Price {
    width: 23px;
    height: 12px;
    font-size: 10px;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
  }
  .NT {
    width: 19px;
    height: 12px;
    font-size: 10px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    vertical-align: super;
    color: #d0021b;
  }
  .layer {
    width: 34px;
    height: 24px;
    font-size: 22px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #d0021b;
  }
  .Activity-Name {
    width: 62px;
    height: 12px;
    font-size: 10px;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
  }
  .Language-Exchange-Di {
    //width: 278px;
    height: 60px;
    font-size: 22px;
    font-weight: 500;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
  }
  .Time-Place {
    width: 59px;
    height: 12px;
    font-size: 10px;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
  }
  .calendar {
    width: 18px;
    height: 18px;
  }
  .pm {
    width: 56px;
    height: 17px;
    font-size: 14px;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
    margin: 10px;
  }
  .address {
    width: 183px;
    height: 17px;
    font-size: 14px;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
    margin: 10px;
  }
  .title {
    width: 46px;
    height: 17px;
    font-size: 14px;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #4a4a4a;
    margin-bottom: 5px;
  }
  .tns-slide-active{
    //width: 290px !important;
  }
</style>