<template>
  <div class="vue_modal">
    <modal name="login-modal" width="90%" :height="400" @before-open="beforeOpen">
      <div class="login-form">
        <form @submit.prevent='loginUser' method="post">
            <p style="color: red">{{err}}</p>      
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Email" required="required" v-model="email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" required="required" v-model="password">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">{{$t('Log in')}}</button>
            </div>      
        </form>
        <p class="text-center">或者</p>
        <div class="m-b-15">
            <a @click=fbLogin() class=" btn btn-default" style="width:100%;background: #3b5998;color: #ffffff"><i class="fa fa-facebook m-r-15 fa-lg" aria-hidden="true"></i>{{$t('Continue Facebook')}}</a>
        </div>
        <div class="clearfix">
            <label class="pull-left checkbox-inline"><input type="checkbox"> {{$t('Remember me')}}</label>
            <p class="pull-right"><a @click="moveToRegister">{{$t('Sign up')}}</a><span> | </span><a href="/password/reset">{{$t('Forgot PW?')}}</a></p>
        </div>  
        <!--<p class="text-center"><a href="#">Create an Account</a></p>-->
      </div>
    </modal>
    <modal name="registration-modal" width="90%" :height="425" @before-open="beforeOpen">
        <div class="login-form">
            <form @submit.prevent='registrationUser' method="post">
                <p style="color: red">{{err}}</p>      
                <div class="form-group row">
                  <div class="col-xs-6">
                    <input type="text" class="form-control" placeholder="first name" required="required" v-model="first_name">
                  </div>
                  <div class="col-xs-6 ">
                    <input type="text" class="form-control" placeholder="last name" required="required" v-model="last_name">
                  </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Email" required="required" v-model="email">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" required="required" v-model="password">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirm Password" required="required" v-model="password_confirmation">
                </div>
                <div class="form-group">
                    <select id="inputState" class="form-control Rectangle-2" v-model="sex">
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>      
            </form>
            <div class="clearfix">
              <label class="pull-left checkbox-inline"><input type="checkbox"> {{$t('Remember me')}}</label>
              <p class="pull-right"><a @click="moveToLogin">{{$t('Login')}}</a><span> | </span><a href="/password/reset">{{$t('Forgot PW?')}}</a></p>
            </div> 
        </div>
    </modal>
  </div>
</template>


<script>
export default {
    ready() {
    },
    props: ['task', 'params'],
    data() {
        return {
            email:'',
            password:'',
            first_name:'',
            last_name:'',
            password_confirmation:'',
            sex:'M',
            err:'',
            taskValsAfterLoginExcute: {
                type: Object
            },
            taskAfterLoginExcute: null
        }
    },
    mounted() {
    },
    methods: {
      beforeOpen (event) {
        if(event == undefined || event.params.task == undefined)return
        this.taskAfterLoginExcute = event.params.task
        if(event.params.vals != null){
          this.taskValsAfterLoginExcute = event.params.vals
        }
        
      },
      afterLoginExecute(){
        if(this.taskAfterLoginExcute == null) return
        if(this.taskValsAfterLoginExcute == null){
          this.taskAfterLoginExcute.fire();
        }else if(typeof this.taskValsAfterLoginExcute == 'object'){
          this.taskAfterLoginExcute.fireWith(window, this.taskValsAfterLoginExcute)
        }else{
          this.taskAfterLoginExcute.fire(this.taskValsAfterLoginExcute)
        }
      },
      fbLogin(){
        //this.newPageLoginActionFB()
        this.directLoginActionFB()
      },
      directLoginActionFB(){
        window.location.href='/auth/facebook/isDirectPage'
        /*
        axios.get('/auth/facebook',{}).then(function (res) {
          if(res.status == 200){
            this.fbLoginResponse(res.data)
          }
        }).catch(function (err) {
          alert('登入失敗')
        })
        */
      },
      newPageLoginActionFB(){
        window.open('/auth/facebook','test','width=780,height=500,directories=no,location=no,menubar=no');
      },
      fbLoginResponse(data){
        if(data.success){
          this.afterLoginExecute();
          this.$modal.hide('login-modal');
        }else{
          alert('登入失敗。')
        }
      },
      loginUser(){
        let vm = this
        axios.post('/login', {
          email: vm.email,
          password: vm.password
        })
        .then(function (response) {
          if (response.status==200) {
            vm.afterLoginExecute();
            vm.$modal.hide('login-modal');
          }
        })
        .catch(function (error) {
          vm.err=error.response.data.errors.email[0];
        });
      },
      moveToRegister(){
        let vm =this;
        vm.$modal.hide('login-modal');
        vm.$modal.show('registration-modal');

      },
      moveToLogin(){
        let vm =this;
        vm.$modal.show('login-modal');
        vm.$modal.hide('registration-modal');

      },
      registrationUser(){
        let vm = this
        axios.post('/register', {
          first_name: vm.first_name,
          last_name: vm.last_name,
          email: vm.email,
          password: vm.password,
          password_confirmation: vm.password_confirmation,
          sex: vm.sex
        })
        .then(function (response) {
          if (response.status==200) {
            vm.afterLoginExecute();
            vm.$modal.hide('registration-modal');
          }
          
        })
        .catch(function (error) {
          vm.err=error.response.data.errors.email[0];
        });
      }
    }
}
</script>

<style scoped>
  .login-form {
    width: 100%;
    padding: 15px;
  }
    .login-form form {
      margin-bottom: 15px;
    }
    .form-control, .btn {
        min-height: 38px;
        border-radius: 2px;
    }
    .btn {        
        font-size: 15px;
        font-weight: bold;
    }
</style>