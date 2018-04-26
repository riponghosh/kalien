@extends('layouts.app')
@section('content')
    <style>
        .card-box-grey{
            box-shadow: 0 0px 24px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);
            background-color: #2f3e47;
            border-radius: 5px;
        }
    </style>
    <div class="container card-box-grey" style="max-width:450px; height: 300px">
        <div class="row text-center">
            <p class="h4 m-t-30" style="color:#ffffff;">開始安排你的行程吧</p>
        </div>
        <div class="row text-center m-t-30">
            <div class="col-md-12">
                <img src="{{'/img/components/calendar_0001.png'}}" style="width: 100px;height: 100px">
            </div>
        </div>
        <div class="row text-center m-t-30">
            <a class="btn btn-success" href="{{url('GET/scheduleDesk/'.$trip_appointment->schedule_id)}}">Open Schedule</a>
        </div>
    </div>
@endsection
