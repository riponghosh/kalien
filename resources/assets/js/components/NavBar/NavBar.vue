<template>
    <div class="container nav-fix">
        <nav class="navbar navbar-default">
      <div class="container-fluid" style="padding-right: 0px;">
        <!-- Brand and toggle get grouped for better mobile display -->

        <div class="navbar-header" style="float: left;">
          <a class="navbar-brand" href="/"><img src="/img/components/company_logo/nav_brand_logo_v1.png" style="height: 20px;"></a>
        </div>
        <ul class="nav navbar-nav float-right" style="float:right list-style-type: none;" v-if="!auth">
          <li>
            <a href="/login" type="button" class="navbar-toggle navbar-toggle-icon collapsed float-right medium-icons"style="display: block;">
              <i class="icon-user middle icons" style="font-size: 24px;color: #777;"></i>
            </a>
          </li>
        </ul>
        <ul class="nav navbar-nav float-right" style="float:right list-style-type: none;" v-else>
          <li style="float: right !important;">
            <a type="button" class="navbar-toggle navbar-toggle-auth collapsed float-right"style="display: block;"  @click="dropdown=(dropdown==1?0:1)">
              <img class="img-circle img-responsive" :src="url" @error="url='/img/icon/user_icon_bg.png'" style="height: 34px;width: 34px; object-fit: cover">
            </a>
            <ul v-if="dropdown" class="dropdown-menu" style="display: block;right: 0;left: inherit;">
              <li><a href="/my_ticket"><i class="icon-mustache m-r-5"></i>Tickets</a></li>
              <li><a href="/user/abouts"><i class="icon-settings m-r-5"> </i> Account Setting</a></li>
              <li><a @click="logout"> <i class="icon-logout m-r-5"> </i> Logout</a></li>
            </ul>
          </li>
        </ul>
        
      </div><!-- /.container-fluid -->
    </nav>
    </div>
</template>


<script>
import {mapGetters, mapActions} from 'vuex'
export default {
    computed: mapGetters([
        'getAuthStatus',
        'getUserIcon'
    ]),
    created: function() {
        this.auth = this.getAuthStatus;
        this.url = this.getUserIcon;

    },
    data() {
        return {
            auth: false,
            url:'',
            dropdown:0
        }
    },
    methods: {
      logout(){
        axios.post('/logout', {
        })
        .then(function (response) {
          if(response.status==200){
            window.location.reload ()
          }
        })
        .catch(function (error) {
        });
      },
      ...mapActions([
        'authChecked'
      ])
    }
    
}
</script>

<style scoped>
  .navbar-nav>li {
      float: none !important;
  }
  .navbar {
      margin-bottom: 5px;
  }
  .collapse {
      display: block !important; 
      width: 50%;
      float: right;
  }
  .navbar-default {
      background-color: #ffffff;
      border-color: #e7e7e7;
  }
  .navbar-nav {
      float: none;
      margin: 0 auto;
  }
  .navbar-header{
    display: table;
  }
  .navbar-toggle-icon{
    padding: 0px !important;
    margin-bottom: 0px !important;
    margin-top: 0px !important;
    border: none;
    display: table !important;
    height: 50px;
  }
  .middle{
    vertical-align: middle;
    display: table-cell;
  }
  .navbar-toggle-auth{
    padding: 0px !important;
    margin-bottom: 0px !important;
    margin-top: 7px !important;
    border: none;
    display: table-cell;
    vertical-align: middle;
  }
  .nav-fix{
    position: fixed;
    width: 100%;
  }
  .container{
    padding: 0px !important;
  }
</style>