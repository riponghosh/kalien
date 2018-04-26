@extends('employeeManagement.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
@section('css')
@endsection
@section('title')
    dashboard
@endsection
@section('interfaceContent')
<div class="container">
    <div class="card-box">
        <p class="m-t-0 m-b-30 h4">店家</p>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>店家名稱</th>
                    <th>uni name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($merchants as $k => $merchant)
                    <tr>
                        <td><a href="{{url('/employee/merchant_info/'.$merchant['uni_name'])}}">{{$k+1}}</a></td>
                        <td>{{$merchant['name']}}</td>
                        <td>{{$merchant['uni_name']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('js_plugin')
@endsection
@section('script')
@endsection
