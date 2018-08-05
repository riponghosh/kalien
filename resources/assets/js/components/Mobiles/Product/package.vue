<template>
	<div class="container-fluid" id="content">
		<div class="row">
			<div class="col-xs-12 m-b-10">
				<span class="Line"></span>
				<span class="Product">Package</span>
			</div>
			<div v-for="trip_activity_ticket in trip_activity_tickets">
				<div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
					<div class="row">
						<div class="col-xs-7">
							<span class="Lorem-ipsum-dolor-si">
								{{trip_activity_ticket.name}}
							</span>
						</div>
						<div class="col-xs-5 text-right">
							<span class="package-price">
								{{priceUnitPrint}} {{getPrice(trip_activity_ticket.amount,trip_activity_ticket.currency_unit)}}
							</span>
						</div>

					</div>
				</div>
				<div class="col-xs-12" style="margin: 10px 0px 10px 0px;">
					<div class="row">
						<div class="col-xs-6">
							<a class="Lorem-ipsum-dolor-si">
								more...
							</a>
						</div>
						<div class="col-xs-6 text-right">
							<button class="create-event" @click="createEvent(trip_activity_ticket.id,trip_activity_ticket.name,trip_activity_ticket.min_participant_for_gp_activity,trip_activity_ticket.max_participant_for_gp_activity)">
								{{ $t('Create Group Event') }}
							</button>
						</div>

					</div>
				</div>
				<div class="clearfix"></div>
				<hr>
			</div>


		</div>
	</div>
</template>

<script>
	import {mapGetters, mapActions} from 'vuex'
	export default{
		props: {
			trip_activity_tickets: {
				type: Array
			}
		},
		created: function() {
			this.currencyUnit=this.getCurrency();
			this.currencyRate=this.getCurrencyRate();
			this.setCurrencyUnit();
		},
		data(){
			return{
				priceUnitPtr : {
					TWD: 'NT$',
					HKD: 'HK$',
					USD: 'US$',
					JPY: 'JP¥'
				},
				priceUnitPrint:'',
				currencyUnit:'',
				currencyRate:Object,
				createEventMethod:false,
				createEventId: null,
				eventPackageName:'',
				minParticipantGpActivity:null,
				maxParticipantGpActivity:null
			}
		},
		methods: {
			setCurrencyUnit(){
				this.priceUnitPrint = this.priceUnitPtr[this.currencyUnit];
			},
			getPrice(amt,currency_unit){
				return (amt*this.currencyRate[this.currencyUnit]/this.currencyRate[currency_unit]).toFixed(0);
			},
			createEvent(id,packageName,minParticipantGpActivity,maxParticipantGpActivity){
				this.$emit('update:createEventId', id);
				this.$emit('update:eventPackageName', packageName);
				this.$emit('update:minParticipantGpActivity', minParticipantGpActivity);
				this.$emit('update:maxParticipantGpActivity', maxParticipantGpActivity);
				this.$emit('create-event-method', true);
			},
			...mapGetters([
				'getCurrency',
				'getCurrencyRate'
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
	font-size: 14px;
	font-weight: normal;
	font-style: normal;
	font-stretch: normal;
	line-height: 1.42;
	letter-spacing: normal;
	text-align: left;
}
.package-price{
	color: red;
	font-size: 16px;
}
.create-event{
	color: rgb(129, 207, 179);
	background: #fff;
	border: 1px solid rgb(129, 207, 179);
	padding: 3px 10px 1px;
	border-radius: 3px;
}
.package-hr{
	border: 1px solid #dce0e0;
}
</style>