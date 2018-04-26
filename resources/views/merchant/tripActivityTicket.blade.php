@extends('layouts.merchantInterface.app')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@section('css')
@endsection
@section('title')
    票券
@endsection
<?php
$user_activity_tickets_gp_by_activity_ticket_id = object_group_by($user_activity_tickets, 'trip_activity_ticket_id');
?>
@section('interfaceContent')
    <div class="container">
        @foreach($trip_activity_tickets as $activity_ticket)
            <div class="card-box">
                <p class="h4">{{$activity_ticket->name_zh_tw}}</p>
                <data-viewer
                        source="/api-web/v1/merchant/group_activity/get"
                        title="group activity"
                        :params="{activity_ticket_id: {{$activity_ticket['id']}}}"
                ></data-viewer>
            </div>
        @endforeach
    </div>
@endsection