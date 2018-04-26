@extends('layouts.merchantInterface.app')

@section('interfaceContent')
<div class="container">
@if(count($merchants) > 0)
<div class="card-box">
    <p class="h4">你的商店</p>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>商店名稱</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($merchants as $k => $merchant)
            <?php $merchant = $merchant->Merchant?>
            <tr>
                <td>{{$k+1}}</td>
                <td>{{$merchant['name']}}</td>
                <td><a href="{{url('/merchant/merchant/'.$merchant->uni_name)}}" class="btn btn-primary btn-xs">打開</a></td>
            <tr>
                @endforeach
        </tbody>
    </table>
</div>
@else
    <div>No any merchant</div>
@endif
</div>
@endsection