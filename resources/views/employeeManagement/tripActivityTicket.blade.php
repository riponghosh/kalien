@extends('employeeManagement.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@section('css')
@endsection
@section('title')
    商家資訊 - 票券
@endsection
@section('interfaceContent')
<div id="app" class="container">
    @foreach($trip_activity['trip_activity_ticket'] as $activity_ticket)
    <div class="card-box">
        <p class="h4">{{$activity_ticket->name_zh_tw}}</p>
        <data-viewer
                source="/api-web/v1/employee/group_activity/get"
                title="group activity"
                :params="{activity_ticket_id: {{$activity_ticket['id']}}}"
        ></data-viewer>
    </div>
    @endforeach
</div>
@endsection

@section('script')
    <script src="/js/vue/all.js"></script>
@endsection