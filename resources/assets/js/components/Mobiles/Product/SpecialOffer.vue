<template>
  <div>
    <div class="container" v-if="sepcialOffers.length>0">
      <p class="special-offer">{{ $t('Sepcial Offer') }}</p>
      <ul class="nav nav-pills offer-tab  nav-justified">
        <li v-for="(sepcialOffer,index) in sepcialOffers" :class="[{active:index==0}]">
          <a data-toggle="pill" :href="'#tab'+index">
            <span class="offer-people">{{sepcialOffer.people_amt}}p</span><br>
            <span class="offer-name">{{sepcialOffer.name}}</span>
          </a>
        </li>
      </ul>

      <div class="tab-content">
        <div v-for="(sepcialOffer,index) in sepcialOffers" :id="'tab'+index" :class="['tab-pane fade in',{active:index==0}]">
          <p>{{sepcialOffer.name}}</p>
          <p>{{sepcialOffer.desc}}</p>
          <div class="row">
            <img v-if="sepcialOffer.media.length" :src="sepcialOffer.media[0].url" @error="imageRemove($event)" class="offer-image">
            <div v-else class="row" style="height:235px;background:grey"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
  export default {
    props: {
      trip_activity_tickets: {
        type: Array
      }
    },
    ready() {
    },
    created: function(){
      this.sepcialOffers=this.getSpectialOffer();
      this.sortSepcialOffers();

    },
    data() {
      return {
        sepcialOffers:Array
      }
    },
    methods: {
      sortSepcialOffers(){
        this.sepcialOffers=this.sepcialOffers.sort(function(a,b) {return a.people_amt - b.people_amt;});
      },
      getSpectialOffer(){
        for(let i=0;i<this.trip_activity_tickets.length;i++){
          if (this.trip_activity_tickets[i].gp_buying_status.length>0) {
            return this.trip_activity_tickets[i].gp_buying_status;
          }
        }
        return [];
      },
      imageRemove(event){
        event.target.outerHTML='<div class="row" style="height:235px;background:grey"></div>';
      }

    }
  }
</script>

<style scoped>
.special-offer{
  font-size: 18px;
  color: black;
  font-weight: bold;
}
.offer-tab{
  display: flex;
  flex-wrap: wrap;
}
.offer-tab > li{
  flex-grow: 1;
}
.offer-tab li a{
  color: rgb(138, 138, 138) !important;
}
.offer-tab li a span{
  color: rgb(138, 138, 138) !important;
}
.offer-tab > li.active > a, .offer-tab > li > a:focus,.offer-tab > li:hover > a{
  color: rgb(218, 47, 103) !important;
  background: white;
}
.offer-tab > li.active > a >span, .offer-tab > li > a >span:focus,.offer-tab > li:hover > a >span{
  color: rgb(218, 47, 103) !important;
  background: white;
}
.offer-people{
  font-size: 18px;
}
.offer-name{
  font-size: 10px;
}
.offer-image{
  height: 253px;
  object-fit:cover;
  width: 100%;
}
</style>