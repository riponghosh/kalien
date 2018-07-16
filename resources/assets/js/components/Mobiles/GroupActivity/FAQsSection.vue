<template>
  <div>
  <div class="container-fluid">
    <div class="row  m-b-20">
      <div class="col-xs-12">
        <span class="Line"></span>
        <span class="section-title">{{ $t('FAQs')}}</span>
      </div>
    </div>
    <ul>
      <li class="m-b-20">
        <p><b>Q.成功付費就等於已參加活動嗎？</b></p>
        <p>不是，成功付費只代表報名成功。只有當你參加的團狀態顯示為<span style="color:#5cb85c">成團</span>時才代表參加成功。</p>
      </li>
      <li class="m-b-20">
        <p><b>Q.成狀態顯示為<span style="color: rgb(240, 173, 78);">審核中</span>是甚麼意思？</b></p>
        <p>其中一個原因是，Pneko會把收集的參加費，正在向商家購買活動門票中，所以如果當天的活動場次額滿或其他理由商家拒絕銷售，代表此揪團審查不被通過，狀態會更改為<span style="color:#d9534f">成團失敗</span>，並全額退票。</p>
      </li>
      <li>
        <p><b>Q.如果最後<span style="color:#d9534f">成團失敗</span>會退費嗎？</b></p>
        <p>對，不成團會全額退費！<br>
            傳統揪團方式，如果活動不成團除了要負擔按金外，主揪人還要把款項退給參加者。
            但只要使用Pneko，會自動把款項退回參加者的付款帳戶。
        </p>
      </li>
    </ul>
  </div>
  </div>
</template>

<script>
export default{

}
</script>

<style scoped>
  .Line {
    width: 2px;
    height: 12px;
    border: solid 2px #81cfb3;
  }
  .section-title{
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
</style>

