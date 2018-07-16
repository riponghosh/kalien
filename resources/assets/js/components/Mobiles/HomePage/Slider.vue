<template>
  <div class="container-fulid header-slider">
    <tiny-slider :mouse-drag="true" :autoplay-button-output="false" :items="1" :controls="false" :autoplay="true" :autoplay-timeout="4000" :gutter="20">
      <div>
        <img :src="'https://www.pneko.com/storage/img/trip_activity/image/269220af01e7323ee35d3e79cc7a516949b47b2f.jpeg'" style="width: 100%;" @error="imageRemove($event)">
      </div>
    </tiny-slider>
    <div class="over-text">
    	<h3>
    		強大的優惠
    	</h3>
    	<p>揪團聚餐人數越多，獎勵越多</p>
    </div>
  </div>
</template>


<script>
import VueTinySlider from 'vue-tiny-slider';
export default {
	components: {
    'tiny-slider': VueTinySlider
  },
  created: function() {
    console.log('Home page slider ready.');
  },
  data() {
      return {
        
      }
  },
  methods: {
    imageRemove(event){
   		event.target.outerHTML='<div style="height:235px;background:grey"></div>';
    }
    
  }
}
</script>

<style scoped>
  .header-slider{
    padding-top: 52px !important;
    background: #fff;
    position: relative;
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
  .over-text{
  	position: absolute;
  	bottom: 0;
  	color: white;
  	margin-left: 20px;
  }
</style>