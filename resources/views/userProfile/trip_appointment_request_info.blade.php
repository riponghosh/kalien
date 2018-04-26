@inject('DatePresenter','App\Presenters\DatePresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@extends('layouts.app')
@section('content')
<?php $app_dates = [];?>
@foreach($trip_appointment->appointmentDates as $app_date)
    <?php
    array_push($app_dates,$app_date['date']);
    ?>
@endforeach
<?php $date = $DatePresenter->get_min_and_max_date($app_dates);?>
    <div class="container" style="max-width:450px">
            <div class="row" >
                <div class="card-box-white col-sm-12" style="width: 100%">
                    <div class="modal-header row">
                    <p class="modal-title">
                        遊客請求資訊
                    </p>
                    </div>
                    <div class="row m-t-15">
                        <div class="col-xs-4">
                            <div class="tourist-img-container">
                                <?php $img_path = count($tourist->user_icons) > 0 ? $tourist->user_icons[0]->media->media_location_low : null; ?>
                                <img class="user-icon img-fit-cover img-thumbnail" src="{{$MediaPresenter->img_path($img_path)}}" style="width: 100%;height:  100%">
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <ul>
                                <li><span class="h4">{{$tourist->name}}</span></li>
                                <li><span class="list-key m-r-5">Age :</span>{{$UserPresenter->birth_date_convert_to_age($tourist->birth_date)}}</li>
                                <li><span class="list-key  m-r-5">Gender :</span>{{$tourist->sex}}</li>
                                <li><span class="list-key m-r-5">Residence :</span>{{$CountryPresenter->iso_convertTo_name($tourist->country)}}</li>
                            </ul>
                        </div>
                    </div><!-- user-info row -->
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-5">
                                    出發日期
                                </div>
                                <div class="col-sm-6">
                                    {{$date['min_date']}}
                                </div>
                            </div>
                        </div><!-- col-6  start_date -->
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-5">
                                    結束日期
                                </div>
                                <div class="col-sm-6">
                                    {{$date['max_date']}}
                                </div>
                            </div>
                        </div><!-- col-6  end_date -->
                    </div><!-- End date row-->
                    <hr>
                    @if($chat_room != null)
                    <div class="row m-b-10">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>留言</p>
                                    @foreach($chat_room['content'] as $content)
                                    <?php
                                        $room_member = [];
                                        $room_member[(string)Auth::user()->id]['name'] = Auth::user()->name;
                                        $img_path = count(Auth::user()->user_icons) > 0 ? Auth::user()->user_icons[0]->media->media_location_low : null;
                                        $room_member[(string)Auth::user()->id]['img_path'] = $MediaPresenter->img_path($img_path);
                                        $room_member[(string)$tourist->id]['name'] = $tourist->name;
                                        $img_path = count($tourist->user_icons) > 0 ? $tourist->user_icons[0]->media->media_location_low : null;
                                        $room_member[(string)$tourist->id]['img_path'] = $MediaPresenter->img_path($img_path);
                                    ?>
                                    <div class="commit">
                                        <img class="img-fit-cover user-icon commit-user-icon" src="{{$room_member[(string)$content->sent_by]['img_path']}}" style="width: 32px;height: 32px;float: left">
                                        <div class="commit-body">
                                            <div class="commit-text">
                                                <div class="commit-header">
                                                    <span>{{$room_member[(string)$content->sent_by]['name']}}</span>
                                                </div>
                                                <span class="commit-msg">{{$content->content}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <form class="commit_form">
                                        <input type="hidden" name="room_id" value="{{$chat_room['room_id']}}">
                                        <div class="form-group m-t-15">
                                            <textarea class="form-control" name="content" placeholder="post a new message"></textarea>
                                        </div>
                                        <div class="pull-right">
                                            <a class="send_comment btn btn-sm btn-primary">send</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- end msg row -->
                    @endif
                    <div class="row modal-footer">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{url('/')}}" type="button" class="btn btn-default" style="float:left">Go to Homepage</a>
                                    <a type="button" class="accept-request btn btn-success" data-appointment_id="{{$trip_appointment->id}}" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing">Accept</a>
                                    <a type="button" class="reject-request btn btn-danger" data-appointment_id="{{$trip_appointment->id}}" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing">Reject</a>
                                </div>
                            </div>
                        </div>
                    </div><!-- end submit btn row -->
                </div><!--card-box-->
            </div>
    </div>
@endsection
@section('script')
<script>
    $('.accept-request.btn').click(function(e){
        e.preventDefault();
        var $btn = $(this);
        acceptTripAppointment($btn.data('appointment_id')).done(function(res){
            if(res.success == true) {
                alert('accept the request succeed!');
                window.location = '/';
            }else if(res.success == false){
                alert('失敗，正在重新為你導入頁面');
                window.location.reload();
            }else{
                alert('失敗，正在重新為你導入頁面');
                window.location.reload();
            }
        }).always(function(){
            $btn.button('reset');
        })
    })
    $('.reject-request.btn').click(function(e){
        e.preventDefault();
        var $btn = $(this);
        $btn.button('loading');
        rejectTripAppointment($btn.data('appointment_id')).done(function(res){
            if(res.success == true) {
                alert('reject the request succeed!');
                window.location = '/';
            }else if(res.success == false){
                alert('失敗，正在重新為你導入頁面');
                window.location.reload();
            }else{
                alert('失敗，正在重新為你導入頁面');
                window.location.reload();
            }
        }).always(function(){
            $btn.button('reset');
        })

    })

    /*
    ** send commit
    */
    $('.send_comment').on('click',function(){
        $form = $(this).closest('.commit_form');
        $room_id = $form.find('[name="room_id"]').val();
        $content = $form.find('[name="content"]').val();
        chatRoomSendMsg($room_id, $content).done(function(res){
            if(res.success == true) {
                alert('留言成功!');
            }else if(res.success == false){
                alert('失敗，正在重新為你導入頁面');
            }else{
                alert('失敗，正在重新為你導入頁面');
            }
        }).always(function(){
            window.location.reload();
        });
    })


</script>
@endsection