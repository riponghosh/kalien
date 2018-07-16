<template>
  <div>
    <div class="container offer-border" v-if="JoinersPoints.length>0">
      <p class="special-offer">{{ $t('Sepcial Offer') }}</p>
      <ul class="nav nav-pills offer-tab  nav-justified">
        <li v-for="JoinersPoint in JoinersPoints" :class="[{active:JoinersPoint.width>=100}]">
          <a data-toggle="pill">
            <span class="offer-people">{{JoinersPoint.people}}p</span><br>
            <span class="offer-name">{{JoinersPoint.name}}</span>
          </a>
        </li>
      </ul>

      <div class="tab-content">
        
        <div class="row ps-bg" style="border-bottom:0;margin-top: 0px">
          <div v-for="JoinersPoint in JoinersPoints" v-bind:class="['ps-bg-step',JoinersPoint.status]" :style="{width:100/(JoinersPoints.length)+'%',float:'left'}">
            <div v-if="JoinersPoint.joinersPosition" v-bind:class="['ps-bg-stepnum current-joiners']" :style="{'width':JoinersPoint.width+'%'}">{{joiners}}</div>
            <div v-bind:class="['text-center ps-bg-stepnum',JoinersPoint.width==100 ? JoinersPoint.color : '']">{{JoinersPoint.people}}</div>
            <div class="progress" ><div v-bind:class="['progress-bar',JoinersPoint.bgcolor]" :style="{width:JoinersPoint.width+'%'}"></div></div>
            <a href="#" v-bind:class="['ps-bg-dot', JoinersPoint.width==100 ? JoinersPoint.bgcolor : '',{finished:JoinersPoint.finish}]"></a>
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
      },
      participants:{
        type:Array
      }
    },
    ready() {
    },
    created: function(){
      this.joiners=this.trip_activity_tickets.length;
      this.sepcialOffers=this.getSpectialOffer();
      this.sortSepcialOffers();
      this.setProgress();
    },
    data() {
      return {
        sepcialOffers:Array,
        JoinersPoints:[],
        joiners:0
      }
    },
    methods: {
      getSpectialOffer(){
        for(let i=0;i<this.trip_activity_tickets.length;i++){
          if(this.trip_activity_tickets[i].gp_buying_status.length >0){
            return this.trip_activity_tickets[i].gp_buying_status;
          }
        }
        return [];
      },
      sortSepcialOffers(){
        this.sepcialOffers=this.sepcialOffers.sort(function(a,b) {return a.people_amt - b.people_amt;});
      },
      setProgress(){
        let smallCheck=0;
        let lastPosition=0;
        let checkJoinersPosition=0;
        for(let j=0;j<this.sepcialOffers.length;j++){
          let widthValue=0;
          checkJoinersPosition=0;
          if(this.joiners<=this.sepcialOffers[j].people_amt){
            if (smallCheck==0) {
              widthValue=(100*this.joiners)/this.sepcialOffers[j].people_amt;
              if (widthValue>100) {
                widthValue=100;
              }
              smallCheck=1;
              if (this.joiners>0&&this.joiners!=this.sepcialOffers[j].people_amt) {
                checkJoinersPosition=1;
              }
            }
          }
          else{
            widthValue=100;
          }
          let obj={
            people: this.sepcialOffers[j].people_amt,
            name: this.sepcialOffers[j].name,
            status: 'complete', 
            bgcolor:'bg-success',
            color:'success',
            finish:1,
            width:widthValue,
            joinersPosition:checkJoinersPosition
          }
          this.JoinersPoints.push(obj);
        }
        
      }

    }
  }
</script>

<style scoped>
.offer-border{
  text-align: left;
  padding: 5px 12px;
  border-bottom: 2px solid #f1f1f1;
}
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
  color: rgb(138, 138, 138);
}
.offer-tab li a span{
  color: rgb(138, 138, 138);
}
.offer-tab > li.active > a, .offer-tab > li > a:focus,.offer-tab > li:hover > a{
  color: rgb(218, 47, 103);
  background: white;
}
.offer-tab > li.active > a >span, .offer-tab > li > a >span:focus,.offer-tab > li:hover > a >span{
  color: rgb(218, 47, 103);
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
  width: 100%;
  object-fit:cover;
}
.current-joiners{
  float:left;
  color:rgb(218, 47, 103);
  position:absolute;
  text-align:right;
}
.ps-bg {border-bottom: solid 1px #e0e0e0; padding: 0 15px 10px 15px;color: rgb(152, 152, 152);}
.ps-bg > .ps-bg-step {position: relative;}
.ps-bg > .ps-bg-step + .ps-bg-step {}
.ps-bg > .ps-bg-step .ps-bg-stepnum {font-size: 10px; margin-bottom: 5px;float: right;margin-right: 3px;}
.ps-bg > .ps-bg-step > .ps-bg-dot {position: absolute; width: 12px; height: 12px; display: block; top: 33px; right: 0%; margin-top: -15px; margin-left: -15px; border-radius: 50%;background-color: #dbdbdb} 
.ps-bg > .ps-bg-step > .ps-bg-dot:after {content: ' '; width: 7px; height: 7px; background: #fff; border-radius: 50px; position: absolute; top: 3px; left: 3px; } 
.ps-bg > .ps-bg-step > .progress {position: relative; border-radius: 0px; height: 3px; box-shadow: none; margin: 24px 0;}
.ps-bg > .ps-bg-step > .progress > .progress-bar {width:0px; box-shadow: none; -webkit-transition: width 1s ease-in-out;-moz-transition: width 1s ease-in-out;-o-transition: width 1s ease-in-out;transition: width 1s ease-in-out;}
.ps-bg > .ps-bg-step.complete > .progress > .progress-bar {width:100%;}
.ps-bg > .ps-bg-step.active > .progress > .progress-bar {width:50%;}
.ps-bg > .ps-bg-step:first-child.active > .progress > .progress-bar {width:0%;}
.ps-bg > .ps-bg-step:last-child.active > .progress > .progress-bar {width: 100%;}
.ps-bg > .ps-bg-step.disabled > .ps-bg-dot {background-color: #d8d8d8;}
.ps-bg > .ps-bg-step.disabled > .ps-bg-dot:after {opacity: 0;}
.ps-bg > .ps-bg-step.disabled a.ps-bg-dot{ pointer-events: none; }
.title-container{margin-top: 20px;margin-bottom: 20px;text-align: left;}
.title-bg {width: 58px;height: 19px;border-radius: 3px;background-color: #dbdbdb;padding: 5px 15px 5px 15px}
.title {width: 36px;height: 17px;font-size: 14px;font-weight: 300;font-style: normal;font-stretch: normal;line-height: normal;letter-spacing: normal;color: #ffffff}
.progress,.progress-bar{background-color: #dbdbdb}
.bg-success{background:rgb(208, 2, 27) !important}
.complete .success{color:rgb(208, 2, 27)}
.finished{height: 12px !important;width: 12px !important;}
.finished:after{height: 0px !important;width: 0px !important;}
</style>