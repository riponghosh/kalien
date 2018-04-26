@extends('employeeManagement.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@section('css')
@endsection
@section('title')
    Trip activity
@endsection
@section('interfaceContent')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav navbar-nav navbar-left">
                    <li>
                        <button type="button" class="create_trip_activity_btn btn btn-success btn-bordred waves-effect w-md waves-light m-b-5">
                            + 新增旅遊活動
                        </button>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right m-r-15">
                    <li class="hidden-xs">
                        <form role="search" class="app-search">
                            <input type="text" placeholder="Search..." class="form-control" style="margin-top: 0; ">
                            <a href=""><i class="fa fa-search"></i></a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
        </div>
    </div>
    @if(count($trip_activity_cards) > 0)
    <div class="container">
        @foreach($trip_activity_cards as $trip_activity_card)
            <?php $card_url = url('employee/trip_activity/get/zh_tw/'.$trip_activity_card->id); ?>
            @include('tripActivity.tripActivityCard')
        @endforeach
    </div>
    @endif
@endsection
@section('js_plugin')
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $('.create_trip_activity_btn').click(function(){
            window.open('{{url('/employee/trip_activity/create')}}', "_blank");
        })
    })
    </script>
@endsection