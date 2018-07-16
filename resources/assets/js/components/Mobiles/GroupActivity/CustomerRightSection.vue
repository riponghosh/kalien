<template>
	<div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 m-b-10">
        <span class="Line"></span>
        <span class="Product">{{ $t('Customer Rights') }}</span>
      </div>

      <div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
        <ul class="customer-right ul_list_item_dot">
          <li>成功付款不等於購買活動門票成功，狀態顯示 <span style="color: rgb(92, 184, 92);">成團</span> 才等於成功購票</li>
          <li>如已過成團期限時狀態不是顯示<span style="color: rgb(92, 184, 92);">成團</span>，代表「參加」失敗，費用全額退回。</li>
          <li>狀態顯示 <span style="color: #f0ad4e">審核中</span>，代表Pneko正在向商家申請購買活動內容，商家有任何理由拒絕售賣產品。如果購買失敗，代表「成團失敗」，費用全額退回。</li>
        </ul>
        <ul class="customer-right ul_list_item_dot">
          <li v-for="customer_right in customer_rights">{{customer_right.desc}}</li>
        </ul>
        
      </div>

    </div>
  </div>
</template>

<script>
export default{
  props: ['customer_rights'],
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