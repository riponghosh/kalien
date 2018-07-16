<template>
  <div class="">
    <section class="wrapper" style="margin-top: -35px;">
      <div class="container-fostrap">
        <div class="content">
          <div class="">
            <div class="">
              <div class="col-xs-12 col-sm-12">
                <div class="card">
                  <div class="card-read-more" style="width: 100%;padding-top: 10px;padding-bottom: 10px;border-bottom: 2px solid #f1f1f1;height: 80px;">
                    <div style="width: 30%;float: left;">
                      <img :src="tempOrganiserAvatar" @error="tempOrganiserAvatar='/img/icon/user_icon_bg.png'" class="img-circle" alt="Cinque Terre" style="height: 56px;width: 56px;"> 
                    </div>
                    <div style="width: 43%;float: left;text-align: left;">
                      <span class="name">{{ this.organiserName }}</span><br>
                      <span class="Event-Owner">{{ $t('Event Owner') }}</span>
                    </div>
                    <div style="width: 27%; float: left;text-align: left;">
                      <span class="Price">{{ $t('Activity Price') }}</span><br>
                      <span class="NT">{{ this.priceUnitPrint }}</span>
                      <span class="layer">{{ this.currentPrice }}</span>
                    </div>
                  </div>
                  <div class="card-content" style="padding: 5px 12px;border-bottom: 2px solid #f1f1f1;">
                    <span class="Activity-Name">{{ $t('Activity Name') }}</span>
                    <h4 class="card-title">
                      <span class="Language-Exchange-Di"> {{ this.activityName }}
                      </span>
                    </h4>
                  </div>
                  <special-offer
                  :trip_activity_tickets="this.trip_activity_tickets"
                  :participants="this.participants"
                  ></special-offer>
                  <div class="card-content" style="padding: 5px 12px;border-bottom: 2px solid #f1f1f1;">
                    <span class="Time-Place">{{ $t('Time & Place') }}</span>
                    <p class="" style="padding-top: 5px;">
                      <span class="icon-calendar calendar"></span>
                      <span class="pm">{{ this.startDate }}</span>
                      <span class="pm">{{ this.startDate+' '+ this.startTime | moment("h:mm a") }}</span>
                    </p>
                    <p class="" style="padding-top: 5px;">
                      <span class="icon-map calendar"></span>
                      <span class="address">{{ this.location}}</span>
                    </p>
                  </div>
                  <progress-bar 
                  :gp-is-achieved="this.gpIsAchieved" 
                  :gp-not-achieved-reason="this.gpNotAchievedReason"
                  ></progress-bar>
                  <div class="card-read-more" style="text-align: left;padding: 15px;">
                    <span class="title">{{ $t('Joiners') }}</span>
                    <br>
                    <img v-for="participant in temp_participants" :src="participant.sm_avatar" @error="participant.sm_avatar='/img/icon/user_icon_bg.png'" class="img-circle" alt="Cinque Terre" style="height: 32px;width: 32px;margin-right: 10px;margin-top: 5px;"> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script>
  import {mapGetters, mapActions} from 'vuex'
  import progressBar from './ProgressBar.vue'
  import SpecialOffer from './SpecialOffer.vue'
  export default {
    props: ['startDate','startTime','price','priceUnit','activityName','location','organiserName','organiserAvatar','participants','gp-is-achieved','gp-not-achieved-reason','trip_activity_tickets'],
    components:{
      'progress-bar':progressBar,
      'special-offer': SpecialOffer
    },
    created: function(){
      this.currencyUnit=this.getCurrency();
      this.currencyRate=this.getCurrencyRate();
      this.setCurrencyUnit();
      this.tempOrganiserAvatar=this.organiserAvatar;
      this.temp_participants=JSON.parse(JSON.stringify(this.participants))
    },
    data (){
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
      currentPrice:0,
      tempOrganiserAvatar:'',
      temp_participants:Object
    }
  },
  methods:{
    setCurrencyUnit(){
      this.priceUnitPrint = this.priceUnitPtr[this.currencyUnit];
      this.currentPrice=(this.price*this.currencyRate[this.currencyUnit]/this.currencyRate[this.priceUnit]).toFixed(0);
    },
    ...mapGetters([
      'getCurrency',
      'getCurrencyRate'
      ])

  }
}
</script>

<style scoped>
.wrapper {
  display: table;
  height: 100%;
  width: 100%;
}

.container-fostrap {
  display: table-cell;
  padding: 2px;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  width: 278px;
  height: 60px;
  font-family: PingFangHK;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
  font-family: Helvetica;
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
</style>