<template>
	<div>
		<h3>{{ $t('Tickets') }}</h3>
		<div v-for="user_activity_ticket in temp_user_activity_tickets">
			<div class="panel panel-default">
				<div class="panel-body">
					<p class="activity-title">{{user_activity_ticket.sub_title}}</p>
					<p class="name">{{user_activity_ticket.name}}</p>
					<p v-if="user_activity_ticket.relate_gp_activity_id">
						<a :href="'/group_events/'+ user_activity_ticket.relate_gp_activity_id">打開活動</a>
					</p>
					<p>
						<span class="icon-calendar"></span>
						<span>{{user_activity_ticket.start_date}}</span>
					</p>
					<p>
						<span>{{ priceUnitPrint }}</span>
						<span>{{ getPrice(user_activity_ticket.amt,user_activity_ticket.currency_unit) }}</span>
					</p>
					<p v-if="user_activity_ticket.authorized_to">
						<span>授權給： {{ user_activity_ticket.assignee.name }}</span>
					</p>
					<hr>
					<div>
						<div class="col-xs-3 m0-p0">
							<button type="button" class="btn btn-default panel-button btn-sm">{{$t('More')}}...</button>
						</div>
						<div class="col-xs-9 text-right m0-p0">
							<button v-if="user_activity_ticket['is_available']['status']" type="button" class="btn btn-default panel-button btn-sm" :disabled="useTicketButtonStatus(user_activity_ticket)" @click="useTicketButtonAction(user_activity_ticket.name,user_activity_ticket.ticket_hash_id,user_activity_ticket.sub_title)">{{useTicketStatus(user_activity_ticket)}}</button>
							
							<button v-if="user_activity_ticket['is_available']['status']" type="button" class="btn btn-default panel-button btn-sm" :disabled="refundButtonStatus(user_activity_ticket)" @click="RefundActionButton(user_activity_ticket.name,user_activity_ticket.ticket_hash_id,user_activity_ticket.sub_title)">{{refundStatus(user_activity_ticket)}}</button>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script>
	import {mapGetters, mapActions} from 'vuex'
	export default {
		props:['user_activity_tickets'],
		created: function() {
			this.currencyUnit=this.getCurrency();
			this.currencyRate=this.getCurrencyRate();
			this.setCurrencyUnit();
			this.filterOutNotUseTicket();
		},
		data() {
			return {
				priceUnitPtr : {
					TWD: 'NT$',
					HKD: 'HK$',
					USD: 'US$',
					JPY: 'JP¥'
				},
				priceUnitPrint:'',
				currencyUnit:'',
				currencyRate:Object,
				temp_user_activity_tickets:Object
			}
		},
		methods: {
			setCurrencyUnit(){
				this.priceUnitPrint = this.priceUnitPtr[this.currencyUnit];
			},
			getPrice(amt,currency_unit){
				return (amt*this.currencyRate[this.currencyUnit]/this.currencyRate[currency_unit]).toFixed(0);
			},
			filterOutNotUseTicket(){
				this.temp_user_activity_tickets=this.user_activity_tickets.filter(function(v){return v.used_at==null})
			},
			useTicketStatus(user_activity_ticket){
				if(user_activity_ticket['is_available']['status'] == 'unavailable'){
					if(user_activity_ticket['is_available']['msg'].indexOf('not_achieved')>=0){
						return '尚未成團'
						//return Vue.t('insufficient joiners')
					}
					else if(user_activity_ticket['is_available']['msg'].indexOf('ticket_is_refunded')){
						return 'is refunded'
					}
					else{
						return 'unavailable'
					}
				}
				else if(this.$moment() < this.$moment(user_activity_ticket['use_duration']['from'], "YYYY-MM-DD h:i:s")){
					return Vue.t('not in use day')
				}
				else if(this.$moment()  > this.$moment(user_activity_ticket['use_duration']['to'], "YYYY-MM-DD h:i:s")){
					return Vue.t('Expired')
				}

				else{
					return Vue.t('use ticket')
				}
			},
			useTicketButtonStatus(user_activity_ticket){
				if(user_activity_ticket['is_available']['status'] == 'unavailable'){
					if(user_activity_ticket['is_available']['msg'].indexOf('not_achieved')>=0){
						return true;
					}
					else if(user_activity_ticket['is_available']['msg'].indexOf('ticket_is_refunded')){
						return true;
					}
					else{
						return true;
					}
				}
				else if(this.$moment() < this.$moment(user_activity_ticket['use_duration']['from'], "YYYY-MM-DD h:i:s")){
					return true;
				}
				else if(this.$moment()  > this.$moment(user_activity_ticket['use_duration']['to'], "YYYY-MM-DD h:i:s")){
					return true;
				}

				else{
					return false;
				}
			},
			refundStatus(user_activity_ticket){
				if(this.$moment()>=this.$moment(user_activity_ticket['use_duration']['from'], "YYYY-MM-DD h:i:s")){
					return 'refund forbidden'
				}
				else{
					return Vue.t('Refund')
				}
			},
			refundButtonStatus(user_activity_ticket){
				this.refundStatusButton=false;
				if(this.$moment()>=this.$moment(user_activity_ticket['use_duration']['from'], "YYYY-MM-DD h:i:s")){
					return true;
				}
				else{
					return false;
				}
			},
			useTicketButtonAction(ticketActiName,ticketId,ticketDetail){
				let vm=this;
				vm.$swal({
					title: ticketActiName+'票券',
					text: '內容：'+ticketDetail,
					type: "success",
					confirmButtonText: '使用 !',
					cancelButtonText: '取消',
					showCancelButton: true,
				})
				.then((response) => {
					vm.$loader.methods.start();
					axios.post('/api-web/v1/user_activity_ticket/use',{
						ticket_id: ticketId
					})
					.then(function (res) {
						vm.$loader.methods.stop();
						res = res.data;
						if (res.success) {
							vm.$swal({
								type: 'success',
								title: res.data.activity_name,
								text: res.data.detail+"\n" + res.data.use_date,
								confirmButtonText: '使用成功'
							})
							.then((response) => {
								window.location.hash = "#recent_used";
								window.location.reload();
							})
						} else if(!res.success){
							let title='';
							let swalType='';
							if(res.code == 1){
								title = '尚未到使用日期。';
								swalType = 'warning';
							}else if(res.code == 2){
								title = '此票券已過期。';
								swalType = 'error';
							}else if(res.code == 3){
								title = '查無此票券。';
								swalType = 'error';
							}else if(res.code == 4){
								title = '票券已失效。';
								swalType = 'error';
							}
							vm.$swal({
								type: swalType,
								title: title,
								confirmButtonText: '返回'
							});
						}else{
							vm.$swal("發生錯誤", ".", "error");
						}
					})

				});

			},
			RefundActionButton(ticketActiName,ticketId,ticketDetail){
				let vm=this;
				vm.$swal({
					type: 'warning',
					title: '辦理退票',
					text: ticketActiName +'：' + ticketDetail,
					type: "warning",
					confirmButtonText: '確認退票 !', 
					confirmButtonClass: 'btn-danger waves-effect waves-light',
					cancelButtonText: '取消',
					showCancelButton: true,
				})
				.then((response) => {
					vm.$loader.methods.start();
					axios.post('/api-web/v1/user_activity_ticket/refund',{
						ticket_id: ticketId
					})
					.then(function (res) {
						vm.$loader.methods.stop();
						res = res.data;
						if (res.success) {
							vm.$swal({
								type: 'success',
								title: '退票成功',
								confirmButtonText: '確認'
							},function () {
								window.location.reload();
							});
						} else if(!res.success){
							vm.$swal({
								type: 'error',
								title: '退票失敗',
								text: res.msg,
								confirmButtonText: '知道',
								cancelButtonText: '取消',
								showCancelButton: true,
								closeOnConfirm: false,
							},function () {
								window.location.reload();
							});
						}else{
							vm.$swal("發生錯誤，請聯絡客服", ".", "error");
						}
					});
				})
			},
			...mapGetters([
				'getCurrency',
				'getCurrencyRate'
				])
		}
	}
</script>

<style scoped>
.activity-title{
	font-size: 20px;
}
.name{
	font-size: 12px;
}
.panel-button{
	border-radius: 4px;
	color: #666;
	min-height: 35px;
	margin-top: 5px;
}
.float-right{
	float: right;
}
.m0-p0{
	margin: 0px;
	padding: 0px;
}
</style>