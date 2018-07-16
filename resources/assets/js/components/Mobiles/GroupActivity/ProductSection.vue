<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 m-b-10">
        <span class="Line"></span>
        <span class="Product">{{ $t('Products Intro') }}</span>
      </div>

      <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
        <p class="Frenc-Resturant">{{ this.pdtPackageName }}</p>
        <p class="Frenc-Resturant" style="font-size: 14px;color: #686868">{{ this.pdtName }}</p>
      </div>
      <!--
      <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
        <p class="Your-Product">{{ $t('Details') }}</p>
        <p class="Frenc-Resturant" style="font-size: 14px">1.Wine's price must under than $15.0</p>
        <p class="Frenc-Resturant" style="font-size: 14px">2.Non-smoking area</p>
      </div>
      -->
      <div class="clearfix"></div>
      <div v-show="ruleInfo.length > 0" class="col-xs-12" style="">
        <hr>
        <div style="" v-for="rule in ruleInfo">
          <div style="margin: 15px 0px 15px 0px">
            <span v-bind:class="getRuleInfoIcon(rule.info_id)"></span>
            <span class="Min-Participants-2">{{getRuleInfoText(rule.info_id,rule.info_value)}}</span>
          </div>
        </div>
        <hr>
      </div>
      <div class="col-xs-12">
        <div class="radio" v-for="shortIntro in shortIntros">
          <label class="-New-Restuant-5">
            <span class="Oval-4"><span class="Oval-3"></span></span>
            <span>{{ shortIntro.intro }}</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {mapGetters} from 'vuex'
export default{
  props: ['pdtName', 'pdtPackageName','shortIntros','ruleInfo'],
  data(){
    return{

      ruleInfoObj : {
          1:{
            icon: 'icon-user',
            valAmt: 1,
            tag: {
              en: function(val){
                return 'Min age: ' + val
              },
              zh_tw: function(val){
                return '須滿'+val+'歲'
              }
            }
          },
          2 :{
           icon: 'icon-user',
           valAmt: 1,
           tag: {
              en: function(val){
                return 'Max age: '+ val
              },
              zh_tw: function(val){
                return '最大年齡上限：' + val
              }
            }  
          },
          3 :{
           icon: 'icon-ban',
           valAmt: 1,
           tag: {
              en: function(val){
                return 'Refund Forbidden'
              },
              zh_tw: function(val){
                return '不能退款'
              }
            }  
          },
          4 :{
            icon: 'icon-action-undo',
            tag: {
              en: function(val){
                return 'Refund before '+val+' day'
              },
              zh_tw: function(val){
                return '活動日前'+val+'天可按規定退款'
              }
            }
          },
          5 :{
           icon: 'icon-people',
           valAmt: 1,
           tag: {
              en: function(val){
                return 'Minimum participants: '+val
              },
              zh_tw: function(val){
                return '最少成團人數：'+val
              }
            }  
          },
          6 :{
           icon: 'icon-people',
           valAmt: 1,
           tag: {
              en: function(val){
                return 'Max participants: '+val
              },
              zh_tw: function(val){
                return '人數上限：'+val
              }
            }  
          },
        },
        language:''
    }
  },
  created: function(){
    this.language=this.getLanguageStatus()
  },
  methods:{
    getRuleInfoText(tagId, tagValue){
      //val amt detect
      if(this.ruleInfoObj[tagId].valAmt > 1){
        if(count(tagValue) != this.ruleInfoObj[tagId].valAmt)return
      }else{
        if(tagValue == undefined) return
      }
      //check language
      var tagLan = ''
      if(this.ruleInfoObj[tagId].tag[this.language] != undefined){
        tagLan = this.language
      }else{
        let langDetectArr = ['en', 'zh_tw', 'jp']
        for(let i=0;i<=langDetectArr.length;i++){
          let lan=langDetectArr[i];
          if(this.ruleInfoObj[tagId].tag[lan] != undefined){
            tagLan = lan;
            break;
          }
        }
      }
      return this.ruleInfoObj[tagId].tag[tagLan](tagValue)
    },
    getRuleInfoIcon(tagId){
      return this.ruleInfoObj[tagId]['icon']
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

  .Your-Product {
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
  .Frenc-Resturant {
    height: 19px;
    font-family: Helvetica;
    font-size: 16px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #000000;
    margin: 7px 0px 7px 0px !important;
  }

  .Min-Participants-2 {
    width: 116px;
    height: 17px;
    font-family: Helvetica;
    font-size: 14px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #686868;
    margin-left: 10px;
  }

  .-New-Restuant-5 {
    font-family: Helvetica;
    font-size: 14px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: normal;
    letter-spacing: normal;
    text-align: left;
    color: #686868;

    position: relative;

  }

  .Lorem-ipsum-dolor-si {
    font-family: Helvetica;
    font-size: 12px;
    font-weight: normal;
    font-style: normal;
    font-stretch: normal;
    line-height: 1.42;
    letter-spacing: normal;
    text-align: left;

  }

  .Oval-3 {
    width: 7px;
    height: 7px;
    background-color: #4a90e2;
    border: solid 1px #2b90b9;
    border-radius: 50%;
    -moz-border-radius: 70px;
    top: 2px;
    left: 2px;
    position: absolute;
  }
  .Oval-4 {
    position: absolute;
    width: 13px;
    height: 13px;
    background-color: #ffffff;
    border: solid 1px #2b90b9;
    border-radius: 50%;
    top: 0;
    left: 0;
    margin-top: 3px; 
  }
</style>