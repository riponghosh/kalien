@extends('layouts.app')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')

@section('content')
<?php
use App\Http\Controllers\UserProfileController;
$is_tourGuide =  UserProfileController::auth_user_is_tourGuide();
$is_tourist =  UserProfileController::auth_user_is_tourist();
?>
<style>
    .mainCropper{
        background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAAA3NCSVQICAjb4U/gAAAABlBMVEXMzMz////TjRV2AAAACXBIWXMAAArrAAAK6wGCiw1aAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABFJREFUCJlj+M/AgBVhF/0PAH6/D/HkDxOGAAAAAElFTkSuQmCC");
        height: 300px;
    }
    .cropper-container{
        margin:auto;

    }
    .preview{
        height: 150px;
        border: 1px #8c8c8c solid;
    }
</style>
<div class="modal fade" id="cropperModal" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                </button>
                <p class="h4 modal-title">編輯圖片</p>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-9 text-center">
                        <div class="mainCropper text-center" style="width: 100%;">
                        <img id="cropperImage">
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="preview" style="overflow: hidden;">
                        </div>
                        <p>預覽</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="upload-user-icon" class="btn btn-warning pull-left">新增或更換圖片</button>
                <button type="button" class="btn btn-primary submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Uploading">儲存</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-12">
<div id="wrapper" class="edit-UserProfile row">
  <aside id="sidebar-wrapper">
  <form id="userProfile_img_form">
  {{ csrf_field() }}
    <div class="icon-container">
      <label class="upload-userIcon" data-toggle="tooltip" title="upload your icon" data-placement="bottom">
        <input id="upload_userIcon_orgin" name="userIconOrgin2" type="file" />
      </label>
      <div class="img-container">
      @if (file_exists(public_path('/img/userIcon/'.$user->id.'.png')))
        <img id="user_icon" src="/img/userIcon/{{$user->id}}.png"/>
      @else
      <img id="user_icon" src=""/>
      @endif
      </div>
      <div class="name-container">
        {{$user->name}}
      </div>
    </div>
    <div class="sidebar">
    <ul class="nav menus">  
      <li>
        <a href="#summary">
        Summary
        </a>
      </li>
      @if($is_tourist)
      <li>
        <a href="#guideProfile">
          Guide Profile
        </a>
      </li>
      @endif
    </ul>
    </div>
  </form>
  </aside>
  <div id="page-content-wrapper" class="main-col col-xs-12 col-sm-10 col-md-11 col-lg-11">
    <a href="#menu-toggle" class="btn" id="menu-toggle">
      <i class="fa fa-navicon"></i>
    </a>
      @include('userProfile.edit.summary')
      @if ($is_tourist != null)
      @include('userProfile.edit.guideProfile')
      @endif  
  </div>
</div>
</div>
<script>
$(document).ready(function(){
@if(isset($user->guide))
  @if($user->guide->status == 1)
    @if($is_tourist == false)
      applicationHint(1);    
    @elseif($is_tourGuide == false)
      applicationHint(2);
    @elseif($is_tourGuide == true && !file_exists(public_path('/img/userIcon/'.$user->id.'.png')))
      applicationHint(3);
    @endif
  @endif
@endif
});
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#cropperImage').attr('src', e.target.result);
            $('#cropperImage').cropper('replace', e.target.result);

        }

        reader.readAsDataURL(input.files[0]);
    }
}
/*
|CROPPER
*/
$('.upload-userIcon').click(function(){
    $('#cropperModal').modal('show');
})
$('#cropperImage').cropper({
    aspectRatio: 3 / 4,
    preview: '.preview'
});
/*Upload*/
$('#cropperModal').find('#upload-user-icon').click(function(){
    $('#upload_userIcon_orgin').click();
})
$("#upload_userIcon_orgin").change(function(){
    readURL(this);
    userIconFormData.append('userIconOrgin',$('#upload_userIcon_orgin')[0].files[0]);
});
/*sumbit*/
var userIconFormData = new FormData();
$('#cropperModal').find('.submit').click(function(){
    $(this).button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 5000);
    saveCropAndOrginIcon()
})
var saveCropAndOrginIcon = function(){
    $('#cropperImage').cropper('getCroppedCanvas').toBlob(function(blob){
        userIconFormData.append('userIcon',blob);   
        postUserIcon().done(function(){
          location.reload();
        });     
    });  
    return;
}
var postUserIcon = function(){
  return $.ajax({
    url : '/PUT/userProfile/img',
    type : 'POST',
    data: userIconFormData,
    contentType: false,
    processData:false
  })
}

/*
|Hint
 */
function applicationHint($v){
  if($v == 1){
    alert('請先填寫基本資料');
  }else if($v == 2){
    alert('進一步告訴你的朋友，例如你在甚麼地區服務、你會甚麼語言等');
  }else if($v == 3){
    alert('加入你的大頭貼');
    $('#cropperModal').modal('show');
  }
}

</script>
@endsection
