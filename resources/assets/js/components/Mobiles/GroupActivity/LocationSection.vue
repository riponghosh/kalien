<template>
	<div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 m-b-10">
        <span class="Line"></span>
        <span class="Product">{{ $t('Locations & Contacts') }}</span>
      </div>

      <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
        <ul class="location">
          <li v-if="this.telAreaCode&&this.tel">
            <p class="Contacts">{{ $t('Call Contacts') }}</p>
            <ul class="loaction-right">
              <li><a :href="'tel: +' + this.telAreaCode + this.tel">{{ this.telAreaCode }} - {{ this.tel}}</a></li>
            </ul>
          </li>
          <li v-if="this.address">
            <p class="Contacts">{{ $t('Locations') }}</p>
            <ul class="loaction-right">
              <li>{{ this.address }}</li>
            </ul>
          </li>
          <li>
            <p class="Contacts">{{ $t('Opening Time')}}</p>
            <ul class="loaction-right">
              <li v-for="openingTime in openingTimes">{{$moment(openingTime.openTime,'HH:mm:ss').format('h:mm a')}} - {{$moment(openingTime.closeTime,'HH:mm:ss').format('h:mm a')}}</li>
            </ul>
          </li>
        </ul>
        
      </div>

    </div>
  </div>
</template>
<script>
export default{
  props: ['telAreaCode', 'tel','address', 'openingTimes'],
  created: function(){
  }
}
</script>

<style scoped>
	.Line {
	  width: 2px;
	  height: 12px;
	  border: solid 2px #81cfb3;
	}
	.Product {
	  width: 69px;
	  height: 22px;
	  font-family: Helvetica;
	  font-size: 18px;
	  font-weight: bold;
	  font-style: normal;
	  font-stretch: normal;
	  line-height: normal;
	  letter-spacing: normal;
	  text-align: left;
	  color: #000000;
	  margin-left: 5px;
	}
	.Contacts {
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
	.location{
	  list-style-type: none;
	  padding-left: 0px;
	}
	.loaction-right{
	}
	.loaction-right >li{
	  font-family: Helvetica;
	  font-size: 14px;
	  font-weight: normal;
	  font-style: normal;
	  font-stretch: normal;
	  line-height: 1.42;
	  letter-spacing: normal;
	  text-align: left;
	  margin-bottom: 10px;
	}
</style>