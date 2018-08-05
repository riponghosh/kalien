<template>
	<div class="event-bg" @click="closeWindow($event)">
		<form>
			<div class="panel panel-default full-panel">
				<div class="panel-body form-body" id="body-detect">
					<h5 class="text-center event-title">輸入活動名稱...</h5>
					<div class="form-group create-event-form-group">
						<input type="text" class="form-control create-event-name-form" placeholder="輸入你的團名..." name="event_name" ref="activityTitle" v-model="eventName">
						<div @click="setEventName" class="send-btn"><i class="fa fa-send-o"></i></div>
					</div>

					<div class="form-group create-event-form-group2">
						<p style="font-size: 12px;">使用預設活動名稱</p>
						<a @click="getEventName($event)" class="text1">加入我的飯局吧</a><br>
						<a @click="getEventName($event)" class="text1">八卦姊妹淘</a><br>
						<a @click="getEventName($event)">老朋友聚會</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</template>


<script>
	export default {
		ready() {
		},
		created: function() {

		},
		data() {
			return {
				eventName:''
			}
		},
		watch: {
			
		},
		methods: {
			closeWindow(event){
				if (document.getElementById('body-detect').contains(event.target)){
				    // Clicked in box
				    // console.log('You clicked inside');
				} else{
				    // Clicked outside the box
				    // console.log('You clicked outside');
				    this.$emit('create-event-name', false)
				}
			},
			getEventName(name){
				this.$emit('update:eventName', name.target.innerHTML);
				this.$emit('create-event-name', false)
			},
			setEventName:function(){
				this.$emit('update:eventName', this.eventName);
				this.$emit('create-event-name', false)
			}
		}
	}
</script>

<style scoped>

input{
	padding: 22px 42px !important;
}
input:focus{
	border: 1px solid #42f30b !important;
}
.event-title{
	margin: 15px 0px;
}
.event-bg{
	position: fixed;
	width: 100%;
	height: 100% !important;
	top: 0px;
	z-index: 99999;
	background: #15141487;
}
.full-panel{
	width: 100%;
	position: absolute;
	height: 100%;
	background:transparent;
}
.back-icon{
	position: absolute;
	left: 20px;
	padding-top: 2px;
}

.create-event-name-form{
	box-shadow: none;
	/*border: none;*/
	border-radius: 0px;
	background-color: #fff !important;
}
.form-body{
	padding: 0px;
	background: #fff;
}
.create-event-form-group{
	margin-bottom: 1px;
	position: relative;
}
.create-event-form-group2{
	padding: 15px 15px;
}
.create-event-form-group2 a{
	line-height: 25px;
}
.send-btn{
	position: absolute;
	top: 0px;
	right: 0px;
	padding: 15px;
}
.send-btn >i{
	font-size: 15px;
}
</style>
