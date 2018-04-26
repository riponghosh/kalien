@extends('employeeManagement.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('MediaPresenter','App\Presenters\MediaPresenter')
@section('css')
@endsection
@section('title')
    商家資訊
@endsection
@section('interfaceContent')
<div class="container">
    @if($merchant == false)
        此店家不存在
    @else
    <h4 class="header-title m-t-0 m-b-30">店家 Uni-name： <b>{{$merchant['uni_name']}}</b></h4>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box task-detail">
                <h4 class="font-600 m-b-30"><b>基本資料</b></h4>
                <ul class="list-inline task-dates m-b-0 m-t-20">
                    <li>
                        <h5 class="font-600 m-b-5">店家名稱</h5>
                        <p>{{$merchant['name']}}</p>
                    </li>
                    <li>
                        <h5 class="font-600 m-b-5">聯絡電話</h5>
                        <p>{{$merchant['tel_area_code']}} - {{$merchant['tel']}}</p>
                    </li>
                    <li>
                        <h5 class="font-600 m-b-5">地址</h5>
                        <p>{{$merchant['address']}}</p>
                    </li>
                </ul>
                <div class="clearfix">
                </div>
            </div>
        </div>
    </div><!-- End basic info row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card-box task-detail">
                <h4 class="font-600 m-b-30"><b>成員</b></h4>
                @if(count($merchant['merchant_members']) == 0)
                    沒有任何成員
                @else
                    @foreach($merchant['merchant_members'] as $k => $merchant_member)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>姓名</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$k + 1}}</td>
                                    <td>{{$merchant_member['user']['name']}}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                @endif
            </div>
        </div>
    </div><!-- End members Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-box task-detail">
                <h4 class="font-600 m-b-30"><b>帳戶資訊</b></h4>
                @if($merchant['merchant_credit_account'] == false)
                    還沒有戶口
                @else
                    <ul class="list-inline task-dates m-b-0 m-t-20">
                        <li>
                            <h5 class="font-600 m-b-5">銀行戶口號碼</h5>
                            <p>{{$merchant['merchant_credit_account']['bank_account']}}</p>
                        </li>
                    </ul>
                    <ul class="list-inline task-dates m-b-0 m-t-20">
                        <li>
                            <h5 class="font-600 m-b-5">餘額</h5>
                            <p>{{$merchant['merchant_credit_account']['currency_unit']}} {{$merchant['merchant_credit_account']['credit']}}</p>
                        </li>
                    </ul>
                    <div class="clearfix">
                    </div>
                @endif
            </div>
        </div>
    </div><!-- End Account Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-box task-detail">
                <h4 class="font-600 m-b-30"><b>相關活動</b></h4>
                @if(count($merchant['trip_activities']) == 0 )
                    沒有任何活動
                @else
                    @foreach($merchant['trip_activities'] as $k => $trip_activity)
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>活動名稱</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><a href="{{url('/activity/'.$trip_activity['uni_name'])}}" target="_blank">{{$k + 1}}</a></td>
                                <td>{{$trip_activity['uni_name']}}</td>
                                <td>
                                    <a href="{{url('/employee/trip_activity_ticket/'.$trip_activity['uni_name'])}}" class="btn btn-primary btn-xs">票券檢視</a>
                                <a href="{{url('/employee/trip_activity/get/zh_tw/'.$trip_activity['id'])}}" class="btn btn-pink btn-xs">活動編輯頁</a></td>
                            </tr>
                            </tbody>
                        </table>
                    @endforeach
                @endif
            </div>
        </div>
    </div><!-- End Trip Activity Row -->
    @endif
</div>
@endsection