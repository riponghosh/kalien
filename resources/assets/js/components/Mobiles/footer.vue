<template>
  <div class="container-fluid BG">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 margin-top-20 margin-bottom">
        <div class="col-md-6 col-sm-6 col-xs-6">
          <select id="inputState" class="form-control Rectangle-2" v-model="language" v-on:change="lanChnage">
              <option selected value="en">English</option>
              <option value="zh_tw">繁體中文</option>
              <option value="jp">日本語</option>
          </select>

        </div>
        <div class="col-md-6 col-sm-6 col-xs-6">
          <select id="inputState" class="form-control Rectangle-2" v-model="currency" v-on:change="CurrencyChnage">
              <option selected value="TWD">TWD</option>
              <option value="HKD">HKD</option>
              <option value="JPY">JPY</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 margin-top-20 margin-bottom">
        <div class="privacy">
          <span><a class="font-white" href="/privacyPolicy">{{ $t('privacyPolicy') }}</a></span>
          <span> | </span>
          <span>{{$t('Join Us')}}</span>

        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 margin-top-20 margin-bottom">
        <div class="social-icon">
          <span><a href="fb://profile/1998097573809329"><img src="/img/icon/facebook/facebook.png"></a></span>
          <span><img src="/img/icon/instagram/instagram.png"></span>

        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 margin-top-20 margin-bottom">
        <div class="privacy copyright">
          <span>© 2017 喵星人科技股份有限公司. All Rights Reserved.</span>

        </div>
      </div>
    </div>
    
  </div>
</template>


<script>
import {mapGetters, mapActions} from 'vuex'
export default {
    ready() {
      console.log('Footer ready.');
    },
    created: function() {
      this.currency=this.getCurrency();
    },
    data() {
        return {
            language: Vue.config.lang,
            currency: ''
        }
    },
    watch: {
       language:function(val) {
          window.location.href = '/changeLocaleLanguage/'+val
          this.language = val;
          Vue.config.lang = this.language; 
       }
    },
    methods: {
      lanChnage(){
        this.setLanguage(this.language);
      },
      CurrencyChnage(){
        window.location.href = '/changeCurrencyUnit/'+this.currency;
      },
      ...mapActions([
        'setLanguage'
      ]),
      ...mapGetters([
        'getCurrency'
      ])
        
    }
}
</script>

<style scoped>
  .font-white{
    color: #FFF;
  }
  .margin-top-20{
    margin-top: 20px;
  }
  .margin-bottom-20{
    margin-bottom: 20px;
  }
  .margin-left-10{
    margin-left: 10px;
  }
  select {
      -webkit-appearance: none;
      -moz-appearance: none;
      text-indent: 1px;
      text-overflow: '';
  }
  .BG {
    /*width: 320px;*/
    /*height: 201px;*/
    background-color: #1f2532;
    padding-bottom: 80px;
  }
  .Rectangle-2 {
      width: 136px;
    height: 37.1px;
      border-radius: 3px;
      background-color: #283042;
      margin:0 auto;
      color: white;
      border: none;
  }
  .Rectangle-2 > option {
      width: 46px;
    height: 17px;
    font-family: Helvetica;
      font-size: 14px;
        font-weight: normal;
        font-style: normal;
        font-stretch: normal;
        line-height: normal;
        letter-spacing: normal;
        text-align: left;
        color: #b1b6cb;
    }
    .privacy{
      color: white !important;
      margin-left: 5%;
    }
    .privacy>span{
      margin: 5px;
    }
    .social-icon{
      width:96px;
      margin: 0 auto;
    }
    .social-icon > span{
      margin: 0px 5px;
    }
    .copyright{
    font-family: Helvetica;
    font-weight: 300;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #b5b5b5;
    font-size: 10px;
    }
</style>