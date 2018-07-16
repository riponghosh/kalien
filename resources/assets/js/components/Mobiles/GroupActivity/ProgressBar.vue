<template>
	<div class="container" style="    border-bottom: 2px solid rgb(241, 241, 241);">
		<div class="title-container">
			<span v-bind:class="['title-bg',labelTextColor]">
				<span class="title text-center">{{labelText}}</span>
			</span>
		</div>
	  <div class="row ps-bg" style="border-bottom:0;margin-top: 40px">
        <div v-for="StatusPoint in StatusPoints" v-bind:class="['ps-bg-step',StatusPoint.status]" :style="{width:100/(StatusPoints.length)+'%',float:'left'}">
          <div class="progress" ><div v-bind:class="['progress-bar',StatusPoint.bgcolor]" :style="{width:StatusPoint.width+'%'}"></div></div>
          <a href="#" v-bind:class="['ps-bg-dot', StatusPoint.width==100 ? StatusPoint.bgcolor : '',{finished:StatusPoint.finish}]"></a>
          <div v-bind:class="['text-center ps-bg-stepnum',StatusPoint.width==100 ? StatusPoint.color : '']">{{StatusPoint.displayName}}</div>
        </div>
    </div>
	</div>
</template>

<script>
export default {
  props: ['gp-is-achieved','gp-not-achieved-reason'],
  data(){
    return{
      StatusPoints: [
        {name: 'GOALS', displayName: Vue.t('Progress Status GOALS'), status: 'disabled', bgcolor:'', color:'',finish:0,width:100},
        {name: 'REVIEW',  displayName: Vue.t('Progress Status REVIEW'),status: 'disabled', bgcolor:'', color:'',finish:0,width:100},
        {name: "ACHIEVED", displayName: Vue.t('Progress Status ACHIEVED'),status: 'disabled', bgcolor:'', color:'',finish:0,width:100}
      ],
      labelTextColor:'',
      labelText:'',
      finish:0
    }
  },
	created: function (){
      this.setProgress();
	},
  methods:{
    setProgress(){
      if (this.gpIsAchieved) {
        this.finish=1;
        this.setProgressBar('ACHIEVED','success'); 
        this.setLabel(Vue.t('Is Achieved'),'success');
      }
      else if(this.gpNotAchievedReason && [2,4,5].includes(this.gpNotAchievedReason)){
        if (this.gpNotAchievedReason==2) {
          this.setProgressBar('GOALS','warning',50);
          this.setLabel(Vue.t('Num of joiners is not enough joiners'),'default');
        }
        else if(this.gpNotAchievedReason==4){
          this.finish=1;
          this.setProgressBar('REVIEW','danger',100);
          this.setLabel(Vue.t('Group Failed'),'danger');
          this.setProgressBarStatusName('REVIEW','FAILED');
        }
        else if(this.gpNotAchievedReason==5){
          this.finish=1;
          this.setProgressBar('REVIEW','warning',100);
          this.setLabel(Vue.t('Reviewing'),'warning'); 
        }
      }
      else{
        this.finish=1;
        this.setProgressBar('GOALS','danger',100);
        this.setProgressBarStatusName('GOALS','FAILED');
        this.setLabel(Vue.t('Group Failed'),'danger');
      }
    },
    setProgressBar(GoalStatus,color,progressAtCurrentStep=100){
      this.activedPassedPoint(GoalStatus,color,progressAtCurrentStep);
    },
    activedPassedPoint(GoalStatus,color,progressAtCurrentStep){
      let position = this.StatusPoints.findIndex(item => item.name == GoalStatus);
      this.StatusPoints[position].finish=this.finish;
      this.StatusPoints[position].width=progressAtCurrentStep;
      
      for(let i =0; i<= position ; i++){
        this.StatusPoints[i].status='complete';
        this.StatusPoints[i].color=color;
        this.StatusPoints[i].bgcolor='bg-'+color;
      }
    },
    setProgressBarStatusName(oldTag,newtag){
      let position = this.StatusPoints.findIndex(item => item.name == oldTag);
      this.StatusPoints[position].displayName=newtag;
    },
    setLabel(text,color){
      this.labelText=text;
      this.labelTextColor='bg-'+color;
    }
  }
  }
</script>
<style type="text/css" scoped>
    .ps-bg {border-bottom: solid 1px #e0e0e0; padding: 0 15px 10px 15px;}
    .ps-bg > .ps-bg-step {position: relative;}
    .ps-bg > .ps-bg-step + .ps-bg-step {}
    .ps-bg > .ps-bg-step .ps-bg-stepnum {font-size: 10px; margin-bottom: 5px;float: right;margin-right: -10px;}
    .ps-bg > .ps-bg-step > .ps-bg-dot {position: absolute; width: 13px; height: 13px; display: block; top: 29px; right: 0%; margin-top: -15px; margin-left: -15px; border-radius: 50%;background-color: #dbdbdb} 
    .ps-bg > .ps-bg-step > .ps-bg-dot:after {content: ' '; width: 7px; height: 7px; background: #fff; border-radius: 50px; position: absolute; top: 3px; left: 3px; } 
    .ps-bg > .ps-bg-step > .progress {position: relative; border-radius: 0px; height: 3px; box-shadow: none; margin: 20px 0;}
    .ps-bg > .ps-bg-step > .progress > .progress-bar {width:0px; box-shadow: none; -webkit-transition: width 1s ease-in-out;
    -moz-transition: width 1s ease-in-out;
    -o-transition: width 1s ease-in-out;
    transition: width 1s ease-in-out;}
    .ps-bg > .ps-bg-step.complete > .progress > .progress-bar {width:100%;}
    .ps-bg > .ps-bg-step.active > .progress > .progress-bar {width:50%;}
    .ps-bg > .ps-bg-step:first-child.active > .progress > .progress-bar {width:0%;}
    .ps-bg > .ps-bg-step:last-child.active > .progress > .progress-bar {width: 100%;}
    .ps-bg > .ps-bg-step.disabled > .ps-bg-dot {background-color: #d8d8d8;}
    .ps-bg > .ps-bg-step.disabled > .ps-bg-dot:after {opacity: 0;}
    .ps-bg > .ps-bg-step.disabled a.ps-bg-dot{ pointer-events: none; }
    .title-container{margin-top: 20px;margin-bottom: 20px;text-align: left;}
    .title-bg {width: 58px;height: 19px;border-radius: 3px;background-color: #dbdbdb;padding: 5px 15px 5px 15px}
    .title {width: 36px;height: 17px;font-size: 14px;font-weight: 300;font-style: normal;font-stretch: normal;line-height: normal;letter-spacing: normal;color: #ffffff}
    .progress,.progress-bar{background-color: #dbdbdb}
    .bg-danger{background:#d9534f !important}
    .bg-success{background:#7ed321 !important}
    .bg-inital{background: #dbdbdb !important}
    .bg-warning{background: #ffac11 !important}
    .complete .danger{color:#d9534f }
    .complete .success{color:#7ed321}
    .complete .inital{color: #dbdbdb}
    .complete .warning{color: #ffac11}
    .finished{height: 16px !important;width: 16px !important;}
    .finished:after{height: 0px !important;width: 0px !important;}
    .finished-warning{width: 50% !important;}
</style>
