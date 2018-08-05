<template>
	<div class="event-bg">
		<form @submit.prevent='dataValidation' method="post">
			<div class="panel panel-default full-panel">
				<div class="panel-heading text-center"><span @click="hideCreateEventPage" class="icon-arrow-left back-icon"></span>{{packageName}}</div>
				<div class="panel-body form-body">
					<div class="form-group create-event-form-group">
						<Datepicker 
						:disabled="state.disabledDates" 
						input-class="form-control create-event-form" 
						placeholder="活動開始時間"
						v-model="createEventForm.startDate"
						@opened="setHeader">
					</Datepicker>
					<div class="input-icon"><i class="fa fa-calendar"></i></div>
				</div>
				<div class="form-group create-event-form-group styled-select">
					<select :class="['form-control create-event-form',{placeholder:createEventForm.startTime==null}]" name="time" v-model="createEventForm.startTime" ref="startTime">
						<option value="null" disabled selected hidden>時間</option>
						<!-- <option v-for="timeRange in timeRanges">{{$moment(timeRange,'HH:mm:ss').format('HH:mm')}}</option> -->
						<option v-for="timeRange in closingTime">{{$moment(timeRange,'HH:mm:ss').format('HH:mm')}}</option>
						<option v-if="closingTime.length>0&&openingTime.length>0" disabled>Close</option>
						<option v-for="timeRange in openingTime">{{$moment(timeRange,'HH:mm:ss').format('HH:mm')}}</option>
					</select>
					<div class="input-icon"><i class="fa fa-clock-o"></i></div>
				</div>
			</div>
			<div class="panel-footer">資訊</div>
			<div class="panel-body form-body">
				<div class="form-group create-event-form-group">
					<select :class="['form-control create-event-form',{placeholder:createEventForm.limitJoiner==null&&!maxJoinersNoLimit}]" name="joiners" v-model="createEventForm.limitJoiner" ref="limitJoiner">
						<option v-if="maxJoinersNoLimit" value="null" selected>no limit</option>
						<option value="null" selected>人數 (可選)</option>
						<option v-for="value in range(minParticipantGpActivity, maxParticipantGpActivity)" :value="value">
							<span v-if="value==minParticipantGpActivity">{{value}} (minimum)</span>
							<span v-else-if="value==maxParticipantGpActivity">{{value}} (maximum)</span>
							<span v-else>{{value}}</span>
						</option>
					</select>
					<div class="input-icon"><i class="fa fa-user-o"></i></div>
				</div>
				<div class="form-group create-event-form-group">
					<input  type="text" class="form-control create-event-form" placeholder="活動名稱" name="event_name" v-model="createEventForm.activityTitle" ref="activityTitle" @click="eventPageAction" readonly="readonly" unselectable="on" onfocus="this.blur()">
					<div class="input-icon"><i class="fa fa-sticky-note-o"></i></div>
				</div>
			</div>
		</div>
		<div class="container-fulid bottom-bg group_activity_bottom_bar">
			<button type="submit" class="btn btn-danger bottom-button">{{ $t('Publish Event') }}</button>
		</div>
		<event-name 
		v-show="eventNamePageShow"
		v-on:create-event-name="eventPageActionHide"
		:event-name.sync="eventName" 
		ref="eventNameRef"
		></event-name>
	</form>
</div>
</template>


<script>
	import EventName from './EventName.vue'
	import Datepicker from 'vuejs-datepicker';
	export default {
		components:{
			Datepicker,
			EventName
		},
		ready() {
		},
		created: function() {

		},
		data() {
			return {
				state :{
					disabledDates: {
						days: [],
						to: null,
						from: null,
						dates: [],
					}

				},
				createEventForm:{
					startDate:'',
					startTime:null,
					activityTitle:'',
					limitJoiner:null
				},
				createEventMethod:false,
				tripActivityTicketId:null,
				packageName:'',
				timeRanges:[],
				openingTime:[],
				closingTime:[],
				minParticipantGpActivity: null,
				maxParticipantGpActivity: null,
				maxJoinersNoLimit:false,
				eventNamePageShow:false,
				eventName:null
			}
		},
		watch: {
			tripActivityTicketId: function(){
				this.removeFromSetData();
				this.datePickerOpen();
				this.getDateTimeQuery();
			},
			eventName:function(value){
				this.createEventForm.activityTitle=value;
			},
			eventNamePageShow:function(value){
				let bodyTag=document.getElementsByTagName("body")[0];
				if (value) {
					bodyTag.style.overflow='hidden';
				}
				else{
					// bodyTag.style.overflow='initial';
				}
			}
		},
		methods: {
			setMinEventJoiners(value){
				if(value === null){
					this.minParticipantGpActivity = 1
				}else{
					this.minParticipantGpActivity = value
				}
				//this.minParticipantGpActivity = value === null ? 1 : value;
			},
			setMaxEventJoiners(value){
				if (value === null) {
					this.maxJoinersNoLimit=true;
					this.maxParticipantGpActivity=80;
				}else{
					this.maxParticipantGpActivity = value;
				}
			},
			hideCreateEventPage(){
				this.$emit('create-event-method', false)
			},
			getDateTimeQuery(){
				let vm = this
				vm.$loader.methods.start();
				axios.post('/api-web/v1/activity_ticket/get_ticket_available_purchase_dates_and_time_ranges', {
					trip_activity_ticket_id: vm.tripActivityTicketId
				})
				.then(function (response) {
					vm.$loader.methods.stop();
					vm.setDisableDate(response);
					vm.setTimeRange(response);
				})
				.catch(function (error) {
				});
			},
			setDisableDate(response){
				let vm=this;
				vm.state.disabledDates.to=new Date(response.data.data.sold_dates.start_date);
				vm.state.disabledDates.from=new Date(response.data.data.sold_dates.end_date);
				vm.state.disabledDates.days=response.data.data.sold_dates.disable_weeks;
				for(let i=0;i<response.data.data.sold_dates.disable_dates.length;i++){
					vm.state.disabledDates.dates.push(new Date(response.data.data.sold_dates.disable_dates[i]));
				}
			},
			setTimeRange(response){
				this.timeRanges=response.data.data.time_ranges;
				this.filtterTimeRanges();
			},
			createEventFormSubmit(){
				let vm = this;
				this.$loader.methods.start();
				let data = new FormData();
				data.append('activity_ticket_id', vm.tripActivityTicketId);
				data.append('start_date', vm.$moment(vm.createEventForm.startDate).format('YYYY-MM-DD'));
				data.append('start_time', vm.$moment(vm.createEventForm.startTime,'HH:mm').format('HH:mm:ss'));
				data.append('activity_title', vm.createEventForm.activityTitle);
				if(vm.createEventForm.limitJoiner){
					data.append('limit_joiner', vm.createEventForm.limitJoiner);
				}
				axios.post('/group_activity_api/create', data)
				.then(function (response) {
					vm.$loader.methods.stop();
					console.log(response);
					if (response.data.success) {
						vm.hideCreateEventPage();
						vm.$swal({
							title: "活動建立成功",
							text: "您的活動已建立，打開看看",
							type: "success",
							showCancelButton: true,
							confirmButtonClass: 'btn-success waves-effect waves-light',
							confirmButtonText: '打開活動', 
						})
						.then((confirm) => {
							window.location.href=response.data.gp_activity_url;
						}, function(dismiss) {

						});
					}
					else{
						vm.$swal('建立失敗', "", "error");
						return
						vm.$swal(response.data.msg, "", "error");
					}

				})
				.catch(function (error) {
					vm.$loader.methods.stop();
					console.log(error.response.status)
					if (error.response.status==401) {
						vm.$modal.show('login-modal',{ task: $.Callbacks().add(vm.createEventFormSubmit),vals: {data: data} });
					}
				});
			},
			removeFromSetData(){
				this.createEventForm.startDate='';
				this.createEventForm.startTime=null;
				this.createEventForm.activityTitle='';
				this.createEventForm.limitJoiner=null;

				this.state.disabledDates.days= [];
				this.state.disabledDates.to= null;
				this.state.disabledDates.from= null;
				this.state.disabledDates.dates= [];

				this.timeRanges=[];
				this.maxJoinersNoLimit=false;
			},
			dataValidation(){
				var datePickerInputField = document.getElementsByClassName("vdp-datepicker")[0].getElementsByTagName("input")[0];
				if(!datePickerInputField.value){
					datePickerInputField.click();
					return;
				}
				if (!this.createEventForm.startDate) {
					this.$refs.startDate.focus();
					return;
				}
				if (!this.createEventForm.startTime) {
					this.$refs.startTime.focus();
					return;
				}
				/*
				if (!this.createEventForm.activityTitle) {
					this.$refs.activityTitle.focus();
					return;
				}
				*/
				/*
				if (!this.createEventForm.limitJoiner && !this.maxJoinersNoLimit) {
					this.$refs.limitJoiner.focus();
					return;
				}
				*/
				this.createEventFormSubmit();
			},
			range(start, end) {
				var ans = [];
				for (let i = start; i <= end; i++) {
					ans.push(i);
				}
				return ans;
			},
			datePickerOpen(){
				var datePickerInputField = document.getElementsByClassName("vdp-datepicker")[0].getElementsByTagName("input")[0];
				datePickerInputField.click();
			},
			eventPageAction(){
				this.eventNamePageShow=true
			},
			eventPageActionHide(value){
				this.eventNamePageShow=value
			},
			setHeader(){
				let div = document.createElement("div");
				let parent=document.getElementsByClassName("vdp-datepicker__calendar")[0];
				let element='<h5 class="text-center event-title">活動日期</h5>';
				if (parent.firstChild.nodeName!='DIV') {
					parent.insertBefore(div,parent.getElementsByTagName("header")[0]).innerHTML=element;
				}
			},
			filtterTimeRanges(){
				// this.timeRanges.sort();
				let close=this.timeRanges[(this.timeRanges.length)-1];
				this.closingTime=this.timeRanges.filter(function(number) {return close >= number;});
				this.openingTime=this.timeRanges.filter(function(number) {return close < number;});
			}
		}
	}
</script>

<style>
select {
	-webkit-appearance: none;
	-moz-appearance: none;
	text-indent: 1px;
	text-overflow: '';
	padding: 0px 42px !important;
	height: 44px !important;
}
select > option{
	padding: 6px 12px !important;
	color: #555;
}
input{
	padding: 22px 42px !important;
}
select:focus,input:focus{
	border: 1px solid #42f30b !important;
}
.event-bg{
	position: fixed;
	width: 100%;
	height: 100% !important;
	top: 0px;
	z-index: 99999;
	background: rgb(237, 237, 237);
}
.full-panel{
	width: 100%;
	position: absolute;
	height: 100%;
	background: rgb(237, 237, 237);
}
.back-icon{
	position: absolute;
	left: 20px;
	padding-top: 2px;
}
.bottom-bg{
	background-color: #fff;
	position: fixed;
	height: 80px;
	bottom: 0;
	width: 100%;
	padding: 14px;
	box-shadow: 0px 0px 15px 0px #666;
}
.bottom-button{
	width: 100%;
	height: 50px;
	background: #DA2F66;
}
.swal2-shown{
	z-index: 99999;
}
.vdp-datepicker__calendar{
	width: 100% !important;
	top:-42px !important;
}
.create-event-form{
	box-shadow: none;
	border: none;
	border-radius: 0px;
	background-color: #fff !important;
}
.form-body{
	padding: 0px;
}
.create-event-form-group{
	margin-bottom: 1px;
	position: relative;
}


.styled-select {
	overflow: hidden;
}

.styled-select select {
	background: transparent;
	border: none;
	font-size: 14px;
	padding: 5px; 
}
.event-title{
	margin: 25px 0px 10px;
}
.placeholder{
	color: #8e8e8c;
}
.input-icon{
	position: absolute;
	top: 0px;
	left: 0px;
	padding: 15px;
}
.input-icon >i{
	font-size: 15px;
}
</style>
