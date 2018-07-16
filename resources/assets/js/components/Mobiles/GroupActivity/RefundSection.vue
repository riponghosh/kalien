<template>
  <div class="container-fluid">
    <div class="row m-b-20">
      <div class="col-xs-12">
        <span class="Line"></span>
        <span class="Product">{{ $t('About Refunds') }}</span>
      </div>
    </div>
    <div class="row">
      <p class="col-xs-12">已下退款方式用於活動狀態<span style="color:#5cb85c">成團</span></p>
      <div v-if="refundForbiddenWhenGpAchieved" class="col-xs-12">
        <ul class="customer-right ul_list_item_dot">
          <li><span style="color:#5cb85c">成團</span>後不能退改。</li>
        </ul>
      </div>
      <div v-else-if="rules.length > 0"class="col-xs-12" style="margin: 10px 0px 70px 0px;">
        <ul class="customer-right ul_list_item_dot" style="">
          <li v-for="rule in rules">{{getRefundRuleText(rule.refund_before_day,rule.refund_percentage,rule.purchase_at_any_time)}}</li>
        </ul>
      </div>
      <div v-else>
        <p>使用日期前可全額退票</p>
      </div>
    </div>
  </div>
</template>
<script>
import {mapGetters} from 'vuex'
export default{
  props: ['rules','refund-forbidden-when-gp-achieved'],
  data(){
    return{
      getRefundRuleTextObj: {
        '0':{
          tag: {
            en: function(val){
              return 'Refund ' + val +'% at use day'
            },
            zh_tw: function(val){
              return '在使用當天退款最高可退'+val+'%'
            }
          }
        },
        'purchase_at_any_time':{
          tag: {
            en: function(val){
              return 'The maximum refund amount is   ' + val + '% when U buy the ticket '
            },
            zh_tw: function(val){
              return '購買後退票最多只能退'+val+'%'
            }
          }
        },
        'other':{
          tag: {
            en: function(val,day){
              return 'Refund  ' + val + '% before ' + day +' days of use day'
            },
            zh_tw: function(val,day){
              return '活動使用日前'+day+'天最高能退'+val+'%'
            }
          }
        }
      },
      language:''
    }
  },
  created(){
    this.language=this.getLanguageStatus()
    this.rules.sort(function(a, b){return a.refund_before_day - b.refund_before_day});
    this.rules.sort(function(a, b){return b.purchase_at_any_time - a.purchase_at_any_time});
    
  },
  methods:{
    getRefundRuleText(refund_before_day, refund_percentage,purchase_at_any_time){
      //tag detect
      let tagIndex=''
      if (purchase_at_any_time) {
        tagIndex='purchase_at_any_time'
      }
      else if (refund_before_day==0) {
        tagIndex='0'
      }
      else{
        tagIndex='other'
      }
      //check language
      var tagLan = ''
      if(this.getRefundRuleTextObj[tagIndex].tag[this.language] != undefined){
        tagLan = this.language
      }else{
        let langDetectArr = ['en', 'zh_tw', 'jp']
        for(let i=0;i<=langDetectArr.length;i++){
          let lan=langDetectArr[i];
          if(this.getRefundRuleTextObj[tagIndex].tag[lan] != undefined){
            tagLan = lan;
            break;
          }
        }
      }
      return this.getRefundRuleTextObj[tagIndex].tag[tagLan](refund_percentage,refund_before_day)
    },
    ...mapGetters([
      'getLanguageStatus'
    ])
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
  .customer-right{
  }
  .customer-right >li{
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