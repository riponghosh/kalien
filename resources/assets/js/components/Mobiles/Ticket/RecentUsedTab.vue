<template>
	<div>
		<h3>{{ $t('Recent Used Tickets') }}</h3>
		<div class="panel panel-default">
			<div class="panel-heading">{{ $t('Recent Used Tickets') }}</div>
			<table class="table">
				<thead>
					<tr>
						<th>名稱</th>
						<th>價格</th>
						<th>使用於</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="user_activity_ticket in temp_user_activity_tickets">
						<td>{{user_activity_ticket.name}}</td>
						<td>{{user_activity_ticket.priceUnitPrint}} {{getPrice(user_activity_ticket.amt,user_activity_ticket.currency_unit)}}</td>
						<td>{{user_activity_ticket.used_at}}</td>
					</tr>
				</tbody>
			</table>
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
			filterOutNotUseTicket(){
				this.temp_user_activity_tickets=this.user_activity_tickets.filter(function(v){return v.used_at!=null})
			},
			setCurrencyUnit(){
				this.priceUnitPrint = this.priceUnitPtr[this.currencyUnit];
			},
			getPrice(amt,currency_unit){
				return (amt*this.currencyRate[this.currencyUnit]/this.currencyRate[currency_unit]).toFixed(0);
			},
			...mapGetters([
				'getCurrency',
				'getCurrencyRate'
				])
		}
	}
</script>

<style scoped>
</style>