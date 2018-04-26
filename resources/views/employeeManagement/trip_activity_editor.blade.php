@extends('employeeManagement.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@section('css')
@endsection
@section('title')
    行程編輯頁
@endsection
@section('interfaceContent')
<div class="container">
    <h4 class="header-title m-t-0 m-b-30">活動編號： <b>{{$trip_activity_id}}</b></h4>
    <div class="row m-b-25">
        <div class="form-group">
            <div class="col-xs-4">
                {{ Form::select('trip_language', ['zh_tw' => '繁體中文', 'en' => 'English', 'jp' => '日本語'], $trip_language, ['id' => 'trip_language','class' => 'form-control']) }}
            </div>
        </div>
        <input type="hidden" name="id" value="{{$trip_activity_id}}"/>
    </div>
    <h3 class=" m-t-0 m-b-30">活動封面顯示內容</h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <form class="form-horizontal" role="form" method="POST" action="/employee/trip_activity/update/gallery_image" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="trip_language" value="{{$trip_language}}">
                <input type="hidden" name="id" value="{{$trip_activity_id}}">
                <h4 class="header-title m-t-0 m-b-30">活動封面照片</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <input type="file" name="trip_main_gallery_img" class="dropify" data-media-id="{{$trip_activity->trip_gallery_pic_id}}" data-show-errors="true" data-default-file="{{$MediaPresenter->img_path($trip_activity->trip_gallery_pic)}}"/>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9 m-t-15">
                            <input type="submit" class="btn btn-default">
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div><!-- End row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <form class="form-horizontal" role="form" method="POST" action="/employee/trip_activity/update/video_url">
                    <h4 class="header-title m-t-0 m-b-30">活動影片</h4>
                    {{ csrf_field() }}
                    <input type="hidden" name="trip_language" value="{{$trip_language}}">
                    <input type="hidden" name="id" value="{{$trip_activity_id}}">
                    <div class="form-group">
                        {{ Form::label('video_url','影片連結', ['class' => 'control-label col-md-2']) }}
                        <div class="col-md-8">
                            {!! Form::text('video_url',$trip_activity->video_url,array_merge(['class' => 'form-control'])) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9 m-t-15">
                            <input type="submit" class="btn btn-default">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- End row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <form class="form-horizontal" role="form" method="POST" action="/employee/trip_activity/update">
                    {{ csrf_field() }}
                    <input type="hidden" name="trip_language" value="{{$trip_language}}">
                    <input type="hidden" name="id" value="{{$trip_activity_id}}">
                <h4 class="header-title m-t-0 m-b-30">基本資料</h4>
                <div class="row">
                    <div class="col-lg-12">
                            <div class="form-group">
                                {{ Form::label('title','標題', ['class' => 'control-label col-md-2']) }}
                                <div class="col-md-8">
                                    {!! Form::text('title',$trip_activity->title,array_merge(['class' => 'form-control'])) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('sub_title', '簡介', ['class' => 'control-label col-md-2']) }}
                                <div class="col-md-8">
                                    {!! Form::text('sub_title',$trip_activity->sub_title,array_merge(['class' => 'form-control'])) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('description', '活動介紹', ['class' => 'control-label col-md-2']) }}
                                <div class="col-md-8">
                                    <textarea class="form-control" placeholder="Description..." name="description">{{$trip_activity->description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('map_address', '地址', ['class' => 'control-label col-md-2']) }}
                                <div class="col-md-8">
                                    {!! Form::text('map_address',$trip_activity->map_address,array_merge(['class' => 'form-control'])) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('map_url', '地圖-url', ['class' => 'control-label col-md-2']) }}
                                <div class="col-md-8">
                                    {!! Form::text('map_url',null,array_merge(['class' => 'form-control'])) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9 m-t-15">
                                    <input type="submit" class="btn btn-default">
                                </div>
                            </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div><!-- End row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="m-b-15">
                <h4 class="header-title m-t-0 m-b-30">活動介紹圖片</h4><button class="create-trip-activity-img-editor-btn btn-sm btn btn-success">新增</button>
                </div>
                <section class="activity-img-groups-section row">
                </section><!--End activity-img-groups-container -->
            </div>
        </div>
    </div><!-- End row -->
    <h3 class=" m-t-0 m-b-30">票券資料</h3>
    @include('employeeManagement.tripActivityEditor.activityTicketTable')
</div>
@endsection
@section('js_plugin')
@endsection
@section('script')
<script>
    $(document).ready(function () {
        $.tripActivityEditorSetBasicData({
            tripActivityId: '{{$trip_activity_id}}',
            tripActivityLanguage: '{{$trip_language}}'
        });
        $.setTripActivityEditorIntroImgData([
            @foreach($trip_activity['trip_img'] as $trip_activity_media)
            @if($trip_activity_media['is_gallery_image'] == 0)
            {
                imgPath: '{{$MediaPresenter->img_path($trip_activity_media['media']['media_location_standard'])}}',
                tripImgId: '{{$trip_activity_media['id']}}',
                tripImgDescription: '{{$trip_activity_media['description'.'_'.$trip_language]}}'

            },
            @endif
            @endforeach
        ]);
    })
    /*language change*/
    $('#trip_language').change(function(){
        alert($(this).val());
        window.location.replace("{{url('/employee/trip_activity/get/')}}"+'/'+$(this).val()+'/'+'{{$trip_activity_id}}');
    });
    /*  bind Dropify  */
    $dropifyEv = $('.dropify').dropify({
        messages: {
            'default': 'Drag and drop a image here or click',
            'replace': 'Drag and drop or click to replace',
            'remove': 'Remove',
            'error': 'Ooops, something wrong appended.'
        },
        error: {
            'fileSize': 'The file size is too big (1M max).'
        }
    });
    $dropifyEv.on('dropify.beforeClear', function(event, element){
        return $.ajax({
            url : '{{url('/employee/trip_activity/delete/gallery_image')}}',
            data : {
                trip_activity_id: {{$trip_activity_id}},
                media_id: $(this).data('media-id')
            },
            success : function(data){
                console.log(data);
                //location.reload();
            }
        });
    });
    /* *
    *create activity
    * */


</script>
@endsection