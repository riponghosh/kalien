@extends('employeeManagement.main')
@section('title')
    商家交易頁面
@endsection
@section('interfaceContent')
<div class="container">
    <h4 class="header-title m-t-0 m-b-30">店家 : <b>{{$merchant['name']}}</b></h4>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4 class="header-title m-t-0 m-b-30">未結算</b></h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>交易編號</th>
                            <th>可交易日期</th>
                            <th>原收入金額</th>
                            <th>Pneko費用</th>
                            <th>第三方支付費用</th>
                            <th>單筆收入</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $total_ori_amount = 0.00; $total_pneko_fee = 0.00; $total_other_fee = 0.00;?>
                    @foreach($merchant_records->where('balanced_at',null) as $k => $merchant_record)
                        <?php
                            $ori_amount = cur_convert($merchant_record['ori_amount'],$merchant_record['currency_unit']);
                            $Pneko_fee= cur_convert(pneko_fee($merchant_record['ori_amount'],$merchant_record['pneko_fee_percentage'], $merchant_record['currency_unit']), $merchant_record['currency_unit']);
                            $other_fee = $merchant_record['ori_amount']*cur_convert($merchant_record['other_fee'],$merchant_record['other_fee_currency_unit'])*0.01;
                            $single_amount = $ori_amount - $Pneko_fee - $other_fee;

                            $total_ori_amount +=  $ori_amount;
                            $total_pneko_fee += $Pneko_fee;
                            $total_other_fee += $other_fee;
                        ?>
                        <tr>
                            <td>{{'PN_'.$merchant_record['id']}}</td>
                            <td>{{$merchant_record['settlement_time']}}</td>
                            <td>{{$merchant_record['ori_amount']}}</td>
                            <td>{{$Pneko_fee}}</td>
                            <td>{{$other_fee}}</td>
                            <td>{{$single_amount}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pull-right" style="width: 150px;">
                    <p class="h4">
                        <span class="">原收入：</span> <span class="pull-right">{{$total_ori_amount}}</span>
                    </p>
                    <p class="h6">
                        Pneko費用： <span class="pull-right">{{$total_pneko_fee}}</span>
                    </p>
                    <p class="h6">
                        其他： <span class="pull-right">{{$total_other_fee}}</span>
                    </p>
                    <hr>
                    <p class="h4">
                        <span class="">淨收入：</span> <span class="pull-right">{{$total_ori_amount - $total_pneko_fee - $total_other_fee}}</span>
                    </p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div><!-- End row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
                <h4 class="header-title m-t-0 m-b-30">已結算</b></h4>
                @if(count($merchant_records->where('balanced_at','!=',null)) > 0 )
                <table class="table">
                    <thead>
                    <tr>
                        <th>交易編號</th>
                        <th>可交易日期</th>
                        <th>原收入金額</th>
                        <th>Pneko費用</th>
                        <th>第三方支付費用</th>
                        <th>單筆收入</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $total_ori_amount = 0.00; $total_pneko_fee = 0.00; $total_other_fee = 0.00;?>
                    @foreach($merchant_records->where('balanced_at','!=',null) as $k => $merchant_record)
                        <?php
                        $ori_amount = cur_convert($merchant_record['ori_amount'],$merchant_record['currency_unit']);
                        $Pneko_fee= cur_convert(pneko_fee($merchant_record['ori_amount'],$merchant_record['pneko_fee_percentage'], $merchant_record['currency_unit']), $merchant_record['currency_unit']);
                        $other_fee = $merchant_record['ori_amount']*cur_convert($merchant_record['other_fee'],$merchant_record['other_fee_currency_unit'])*0.01;
                        $single_amount = $ori_amount - $Pneko_fee - $other_fee;

                        $total_ori_amount +=  $ori_amount;
                        $total_pneko_fee += $Pneko_fee;
                        $total_other_fee += $other_fee;
                        ?>
                        <tr>
                            <td>{{'PN_'.$merchant_record['id']}}</td>
                            <td>{{$merchant_record['settlement_time']}}</td>
                            <td>{{$merchant_record['ori_amount']}}</td>
                            <td>{{$Pneko_fee}}</td>
                            <td>{{$other_fee}}</td>
                            <td>{{$single_amount}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pull-right" style="width: 150px;">
                    <p class="h4">
                        <span class="">原收入：</span> <span class="pull-right">{{$total_ori_amount}}</span>
                    </p>
                    <p class="h6">
                        Pneko費用： <span class="pull-right">{{$total_pneko_fee}}</span>
                    </p>
                    <p class="h6">
                        其他： <span class="pull-right">{{$total_other_fee}}</span>
                    </p>
                    <hr>
                    <p class="h4">
                        <span class="">淨收入：</span> <span class="pull-right">{{$total_ori_amount - $total_pneko_fee - $total_other_fee}}</span>
                    </p>
                </div>
                <div class="clearfix"></div>
                @endif
            </div>
        </div>
    </div><!--End row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card-box">
            </div>
        </div>
    </div>
</div>
@endsection