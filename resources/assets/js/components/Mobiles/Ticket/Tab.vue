<template>
  <div style="min-height:400px;padding-top:65px;">
    <div class="container">
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#ticket">{{ $t('Tickets') }}</a></li>
        <li><a data-toggle="tab" href="#recent_used">{{ $t('Recent Used Tickets') }}<span v-if="recentUsedTickets.length>0">({{recentUsedTickets.length}})</span></a></li>
      </ul>

      <div class="tab-content">
        <div id="ticket" class="tab-pane fade in active">
          <ticket-tab :user_activity_tickets=user_activity_tickets></ticket-tab>
        </div>
        <div id="recent_used" class="tab-pane fade">
          <recent-used-tab :user_activity_tickets=user_activity_tickets></recent-used-tab>
        </div>
      </div>
    </div>
    
  </div>
</template>


<script>
  import TicketTab from './TicketTab.vue'
  import RecentUsedTab from './RecentUsedTab.vue'
  export default {
    props:['user_activity_tickets'],
    components:{
      'ticket-tab':TicketTab,
      'recent-used-tab':RecentUsedTab
    },
    created: function() {
      console.log('Ticket Page ready.');
      this.getRecentUsedTickets();
    },
    data() {
      return {
        recentUsedTickets:Object
      }
    },
    mounted:function(){
      this.setTab();
    },
    methods: {
      getRecentUsedTickets(){
        this.recentUsedTickets=this.user_activity_tickets.filter(function(v){return v.used_at!=null})
      },
      setTab(){
        let hash = window.location.hash
        if(hash=='#recent_used'){
          let tab =document.getElementsByClassName("nav-tabs")[0];
          tab.firstChild.classList.remove("active");
          tab.childNodes[2].childNodes[0].click();
        }
      }
    }
  }
</script>

<style scoped>
.header-slider{
  padding-top: 65px !important;
}
</style>