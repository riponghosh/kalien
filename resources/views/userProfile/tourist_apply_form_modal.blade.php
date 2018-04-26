@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
<?php
if($user->name != null){
  $fullname = explode(" ",$user->name);
  $lastname = array_pop($fullname);
  $firstname = implode(" ",$fullname);
};
if($user->sex != null){
  $sex = $user->sex;
}else{
  $sex = 'null';
};
if($user->country != null || $user->country != ''){
   $country = $user->country;
}else{
  $country = 'null';
}
//languages
$lan_arr = array();
$lan_user_arr = array();
foreach($languages_list as $lan_list){
$id = $lan_list->id;
$name = $UserPresenter->language($lan_list->language_name);
$lan_arr[$id] = $name;
}
foreach ($user->languages as $v) {
if($v->level == 3){
  array_push($lan_user_arr,$v->language_id);
}
}
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
    </button>
    <p class="h4 modal-title">讓你的同遊伙伴更了解你</p>
</div>
<div class="modal-body">
  <form id="tourist-apply-form" class="form-horizontal" role="form">
  @if(!$user->name) 
  <div class="form-group">
        <label class="col-xs-12 col-sm-2 col-md-2 control-label">Name</label>
        <div class="col-xs-12 col-sm-5 col-md-5 col-xs-6">  
          <input class="kal-style form-control" type="text" name="first_name" placeholder="First Name" />
        </div>
        <div class="col-xs-12 col-md-5 col-sm-5 col-xs-6">
          <input class="kal-style form-control" type="text" name="last_name" placeholder="Last Name" />
        </div>
  </div>
  @endif
  @if(!$user->birth_date)
    <div class="form-group">
      <label class="col-xs-12 col-md-2 col-sm-2 col-xs-12 control-label">Birth Date</label>
      <div class="col-md-3 col-sm-3 col-xs-12">
        <input class="kal-style form-control" type="text" name="birth_date" placeholder="yyyy-mm-dd" />
      </div>
    </div>
  @endif
    @if(!$user->phone_number)
  <div class="form-group">
      <label class="col-sm-2 col-xs-12 control-label">Phone</label>
      <div class="col-xs-2 col-sm-3">
          <select class="form-control" name="phone_area_code">
              {{$UserPresenter->create_phone_area_code_option($user->phone_area_code)}}
          </select>
      </div>
      <div class="col-sm-6">
        <input class="kal-style form-control" type="text" name="phone_number" placeholder="Phone Number" />
      </div>
    </div>
    @endif
    @if($country == 'null')
    <div class="form-group row">
    <label class="col-md-2 col-sm-2 col-xs-12  control-label">Country</label>
    <div class="col-xs-12 col-sm-10 col-md-10">
      <div class="select-wrapper {{$country != 'null'  ? 'has-success' : ''}}">
        <select data-placeholder="Your country" name="country" class="chosen-select-deselect" style="width:100%">
            <option value=""></option>
            {{$CountryPresenter->create_country_option('null')}}
        </select> 
      </div>
    </div>
    </div>
    @endif
    @if($sex == 'null')
    <div class="form-group row">
      <label class="col-md-2 col-sm-2 col-xs-12  control-label">Gender</label>
      <div class="col-md-3 col-sm-3 col-xs-5">
      <select class="k-style" name="sex">
          <option value="" disabled selected>Gender</option>
          <option value="M">Male</option>
          <option value="F">Female</option>       
      </select>
      </div> 
    </div><!--gender grouup-->
    @endif
  </form>
</div>
<div class="modal-footer">
  <button id="send_tourist_apply_form_btn" type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> sending">submit</button>
  <button type="button" class="btn btn-default" data-dismiss="modal">cancel</button>
</div>
<script type="text/javascript">
$('.chosen-select').chosen({max_selected_options : 5,width : "100%"});
$('.chosen-select-deselect').chosen({allow_single_deselect:true,width : "100%"});
$('.chosen-select-no-results').chosen({no_results_text:'Oops, nothing found!'});
$('#send_tourist_apply_form_btn').click(function(e){
  e.preventDefault();
  var $loadingBtn = $(this);
    $loadingBtn.button('loading');
    setTimeout(function() {
       $loadingBtn.button('reset');
    }, 2000);
    form = new FormData(document.getElementById('tourist-apply-form'));
    $.when(sendTouristApplyForm.insertData(form)).done(sendTouristApplyForm.action());
})
$('.close').click(function(){
  $('#becomeGuideModal').modal('hide');
})
</script>
