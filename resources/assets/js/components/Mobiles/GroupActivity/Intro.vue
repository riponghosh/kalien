<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 m-b-10">
        <span class="Line"></span>
        <span class="Product">{{ $t('Product Intro')}}</span>
      </div>
      <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
        <p class="Lorem-ipsum-dolor-si">
          {{ this.desc }}
        </p>
      </div>
      <div v-for="media in pdtMedia">
        <div class="col-xs-12" v-if="!media.is_gallery_image&&media.url" style="margin: 0px 0px 10px 0px;">
         <img :src="media.url" @error="imageError($event,key)" style="width: 100%" />
       </div>
       <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
        <p class="Lorem-ipsum-dolor-si" style="color: #666666">{{media.description_zh_tw}}</p>
        
      </div>
    </div>

  </div>
</div>
</template>

<script>
  export default{
    props: {
      desc: {
        type: String
      },
      introImgs: {
        type: Object
      },
      pdtMedia: {
        type: Array
      }
    },
    data(){
      return{
      }
    },
    created: function(){
    },
    imageError(key,i){
      key.path[2].innerHTML=''
      this.pdtMedia[i].is_gallery_image=1
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
.Lorem-ipsum-dolor-si {
  font-family: Helvetica;
  font-size: 14px;
  font-weight: normal;
  font-style: normal;
  font-stretch: normal;
  line-height: 1.42;
  letter-spacing: normal;
  text-align: left;
}
</style>