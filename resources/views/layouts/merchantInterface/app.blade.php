<!DOCTYPE html>
<?php
use App\Http\Controllers\UserProfileController;
$is_dp = BrowserDetect::isDesktop() == true ? 'true' : 'false';
?>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    @yield('css')
    {!! Html::style('css/userInterface.css'.VERSION);  !!}
    {!! Html::style('css/userInterface/pulgin.css'.VERSION) !!}
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
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
</head>
<body class="fixed-left widescreen" id="app">
@if($is_dp == 'true')
    <!-- Cropper Modal -->
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
                    <input id="upload_userIcon_orgin" name="userIconOrgin2" type="file" style="display: none" accept=".jpeg,.png,.gif,.jpg"/>
                    <button type="button" class="btn btn-primary submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Uploading">儲存</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Cropper Modal-->
@endif
<!-- Begin page -->
<div id="wrapper">
    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- LOGO -->
        <div class="topbar-left">
            <a href="{{ url('/') }}" class="logo"><span>Pneko</span></a>
        </div>
        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">

                <!-- Page title -->
                <ul class="nav navbar-nav navbar-left">
                    <li>
                        <button class="button-menu-mobile open-left">
                            <i class="zmdi zmdi-menu"></i>
                        </button>
                    </li>
                    <li>
                        <h4 class="page-title">@yield('title')</h4>
                    </li>
                </ul>
            </div><!-- end container -->
        </div><!-- end navbar -->
    </div>
    <!-- Top Bar End -->

    <!-- ========== Left Sidebar start ========== -->

    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">
            <!-- User -->
            @include('layouts.managementInterface.navUserIcon')
            <!-- End User -->
            <!--- Divider -->
            <div id="sidebar-menu">
                <ul>
                    <li class="text-muted menu-title">@lang('userInterface.Navigation')</li>

                    <li>
                        <a href="{{url('/merchant/dashboard')}}" class="waves-effect"><i class="zmdi zmdi-view-dashboard"></i> <span> dashboard </span> </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========== Left Sidebar end ========== -->

    <!-- ========== main content start ========== -->
    <div class="content-page">

        <!-- content -->
        <div class="content">
            @yield('interfaceContent')
        </div>

        <!-- content -->

        <!-- footer -->
        @include('layouts.managementInterface.footer')
        <!-- End footer -->
    </div>

    <!-- ========== main content end ========== -->


    <!-- ========== Right sidebar start ========== -->
    <div class="side-bar right-bar">
    </div>

    <!-- ========== Right sidebar end ========== -->

</div>


</body>
<!-- scripts -->
<script>
    var resizefunc = [];
</script>
<!-- jQuery  -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>

{!! Html::script('js/userInterface/pulgin.js'.VERSION); !!}
@yield('js_plugin')
@yield('js_file')
@yield('script')
{!! Html::script('js/userInterface.js'.VERSION) !!}
{!! Html::script('js/employee.js'.VERSION) !!}
{!! Html::script('/js/vue/all.js'.VERSION) !!}
<script>
    @if($is_dp == 'true')
    /*
     |CROPPER
     */
    $('#cropperImage').cropper({
        aspectRatio: 3 / 4,
        preview: '.preview'
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
     ** Upload Image
     */
    var userIconFormData = new FormData();
    $('.user-img').click(function(){
        $('#cropperModal').modal('show');
    })
    $('#cropperModal').find('#upload-user-icon').click(function(){
        $('#upload_userIcon_orgin').click();
    })
    $("#upload_userIcon_orgin").change(function(){
        readURL(this);
        userIconFormData.append('userIconOrgin',$('#upload_userIcon_orgin')[0].files[0]);
    });
    /*
     **sumbit Post
     */
    var userIconFormData = new FormData();
    $('#cropperModal').find('.submit').click(function(){
        if($('#upload_userIcon_orgin').val() == ''){
            alert('Please choose your image to upload');
            return;
        }
        $(this).button('loading');
        saveCropAndOrginIcon($(this));

    })
    var saveCropAndOrginIcon = function($btn){
        $('#cropperImage').cropper('getCroppedCanvas').toBlob(function(blob){
            userIconFormData.append('userIcon',blob);
            postUserIcon().done(function(res){
                if(res.success == true){
                    alert('success');
                    location.reload();
                }else if(res.success == false){
                    alert(res.msg);
                    location.reload();
                }else{
                    alert('something wrong');
                }
            }).fail(function(){
                alert('something wrong');
            }).always(function(){
                $btn.button('reset');
            });
        });
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
    @else
    $('.user-img').click(function(){
        alert(' 抱歉，手機不支援頭像製作，請使用電腦版');
    })
    @endif
</script>
</html>
