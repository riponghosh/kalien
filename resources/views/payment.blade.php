@inject('UserPresenter','App\Presenters\UserPresenter')
@inject('TripActivityPresenter','App\Presenters\TripActivityPresenter')
@inject('CountryPresenter','App\Presenters\CountryPresenter')@inject('CountryPresenter','App\Presenters\CountryPresenter')
<?php
    $sub_total_price = 0.0;
    $total_price = 0.0;
    $show_cur_unit = $UserPresenter->cur_units(Cookie::get('currency_unit'), 's');

?>
@extends('layouts.app')
@section('content')
<style>
    .payment-panel{
        width: 100%;
        border-radius: 5px;
        border: 1px #ddd solid;
        background-color: #fff;
    }
    .payment-panel > .panel-heading .title{
        color: #ffffff;
        font-size: 16px;
    }
    .payment-panel .panel-heading{
        background-color: #81cfb3;
    }
</style>
<div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12" style="float: right"><!-- Product info-->
        @if(!empty($receipt_trip_activity_tickets))
        <?php $receipt_trip_activity_tickets_group_by_id = object_group_by($receipt_trip_activity_tickets,'trip_activity_ticket.id'); ?>
            @foreach($receipt_trip_activity_tickets_group_by_id as $receipt_trip_activity_tickets)
                @foreach($receipt_trip_activity_tickets as $receipt_ta_ticket)
                    <?php $ta_ticket = $receipt_ta_ticket['trip_activity_ticket']; ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{$TripActivityPresenter->lan_convert($ta_ticket['trip_activity'], 'title')}} - {{$TripActivityPresenter->lan_convert($ta_ticket['trip_activity'], 'sub_title')}}
                        </div>
                        <div class="panel-body">
                            <div class="row p-b-10">
                                <div class="col-md-12">
                                    <span style="color: #9c9c9c">{{trans('payment.ItemDetail')}}</span>
                                    <div class="pull-right">
                                        {{$TripActivityPresenter->lan_convert($ta_ticket,'name')}}
                                    </div>
                                </div>
                            </div><!--row-->
                            <div class="row p-b-10">
                                <div class="col-md-12">
                                    <span style="color: #9c9c9c">{{trans('payment.useDate')}}</span>
                                    <div class="pull-right">
                                        {{$receipt_ta_ticket['start_date']}}
                                    </div>
                                </div>
                            </div><!--row-->
                            <div class="row p-b-10">
                                <div class="col-md-12">
                                    <span style="color: #9c9c9c">{{trans('payment.Qty')}}</span>
                                    <div class="pull-right">
                                        {{$receipt_ta_ticket['qty']}}
                                    </div>
                                </div>
                            </div><!--row-->
                        </div>
                        <div class="panel-footer">
                            <span style="color: #9c9c9c">{{trans('payment.Total')}}</span>
                            <?php $sub_total_price = cur_convert($ta_ticket['amount'],$ta_ticket['currency_unit']) * $receipt_ta_ticket['qty']?>
                            <span class="pull-right">{{$show_cur_unit}}&nbsp;{{number_format($sub_total_price,1,'.','')}}</span>
                            <?php $total_price += $sub_total_price; $sub_total_price = 0;?>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endif
        @if(!empty($user_service_tickets))
        @foreach($user_service_tickets as $seller => $user_service_ticket)
            <?php $us_ticket_ser_types = object_group_by($user_service_ticket,'guide_service_ticket.service_type'); ?>

            @foreach($us_ticket_ser_types as $us_ticket_ser_type)
                <?php $us_ticket_dates = object_group_by($us_ticket_ser_type,'guide_service_ticket.start_date'); ?>
                @foreach($us_ticket_dates as $us_tickets)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{trans('userInterface.'.$UserPresenter->get_user_service_names($us_tickets[0]['guide_service_ticket']['service_type']))}} {{$us_tickets[0]['guide_service_ticket']['seller']['name']}}
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    {{trans('payment.useDate')}}
                                    <div class="pull-right">
                                        {{$us_tickets[0]['guide_service_ticket']['start_date']}}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table reciept-table table-hover ">
                                        <thead>
                                        <tr style="color: #9c9c9c">
                                            <th style="padding-left: 0;font-weight: 300">Description</th>
                                            <th style="font-weight: 300">Time</th>
                                            <th class="text-right" style="padding-right: 0; font-weight: 300">Price</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($us_tickets as $us_ticket)
                                            <tr>
                                                <td style="padding-left: 0">{{$us_ticket['guide_service_ticket']['product_name']}}</td>
                                                <td>{{$us_ticket['guide_service_ticket']['start_time']}}-{{$us_ticket['guide_service_ticket']['end_time']}}</td>
                                                <td class="text-right " style="padding-right: 0">{{cur_convert(number_format($us_ticket['guide_service_ticket']['price_by_seller'],1,'.',''),$us_ticket['guide_service_ticket']['price_unit_by_seller'])}}</td>
                                                <?php $sub_total_price += cur_convert(number_format($us_ticket['guide_service_ticket']['price_by_seller'],1,'.',''),$us_ticket['guide_service_ticket']['price_unit_by_seller']); ?>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <span style="color: #9c9c9c">{{trans('payment.Total')}}</span>
                            <span class="pull-right">{{$show_cur_unit}} {{$sub_total_price}}</span>
                            <?php $total_price += $sub_total_price; $sub_total_price = 0;?>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
        @endif
        <div class="panel">
            <div class="panel-body">
                <p>
                    <span style="color: #9c9c9c">{{trans('payment.Total')}}</span><span class="pull-right">{{$UserPresenter->cur_units(Cookie::get('currency_unit'),'s')}}&nbsp;{{number_format($total_price,1,'.','')}}</span>
                </p>
                <p>
                    <span style="color: #9c9c9c">{{trans('payment.PaymentAmount')}}</span><span class="pull-right" style="font-size: 18px;color: #81cfb3">{{$UserPresenter->cur_units(Cookie::get('currency_unit'),'s')}}&nbsp;{{number_format($total_price,1,'.','')}}</span>
                </p>
            </div>
        </div>
    </div><!--Product info-->
    <div class="col-md-8 col-sm-12 col-xs-12" style="float: right">
    @if(count($receipt_trip_activity_tickets) == 0 && count($user_service_tickets) == 0)
        <div class="panel">
            <div class="panel-body text-center" style="height: 200px;display: table;width: 100%;">
                <p class="h4" style="display: table-cell;vertical-align: middle">沒有訂購任何產品</p>
            </div>
        </div>
    @else
        <form id="cardview-container" class="payment-panel m-b-15">
            <div class="panel-heading">
                <div class="title">{{trans('payment.PaymentType')}}</div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group form-group-sm">
                            <label>{{trans('payment.CardNumber')}}</label>
                            <div class="input-wrapped full" id="validateCard">
                                <input class="form-control" type="text" size="20" name="ccNumber" id="cc_number" class="full" placeholder="xxxx 5678 xxxx 3456" data-creditcard="true" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-4 form-group form-group-sm">
                        <label class="">{{trans('payment.ExpiryDate')}}</label>
                        <div class="row">
                            <div class="col-md-5 col-xs-6">
                                <input id="cc_exp_month" class="cc-expires month form-control" maxlength="2"  placeholder="MM" required>
                            </div>
                            <div class="col-md-5 col-xs-6">
                                <input id="cc_exp_year" class="cc-expires year form-control" maxlength="2"  placeholder="YY" required>
                            </div>
                        </div>
                   </div>
                    <div class="col-xs-4 col-sm-3 form-group form-group-sm">
                        <label>{{trans('payment.SecurityCode')}}</label>
                        <input id="ccv" class="form-control" placeholder="CVC" maxlength="3" required>
                    </div>
                </div>
                <hr>
                @include('payment.invoiceForm')
                <hr>
                <div class="row">
                    <div class="col-md-12">
                    <div class="text-center">
                    <button id="tappaySubmitBtn" class="btn" style="width: 50%; background: #da2f67;color: white">{{trans('payment.Pay')}}</button>
                    </div>
                    </div>
                </div>
            </div>
        </form>
        @include('payment.userInfoFormForTripActivity')
    @endif
    </div>
</div>
@endsection
@section('script')
    <script src="https://js.tappaysdk.com/tpdirect/v2_2_1"></script>
    <script>

        $('.select_pt_country').select2({
            placeholder: "護照上的國家／地區",
            allowClear: true
        });

        //發票操作
        (function ($) {
            var paymentCardForm = $('#cardview-container');
            var invoiceTypeInput = paymentCardForm.find('[name="invoice_type"]');
            var receiptDonationInput = paymentCardForm.find('[name="receipt_donation"]');
            var invoiceCarryInput = paymentCardForm.find('[name="receipt_carry_type"]');
            var B2CInfoPanel = paymentCardForm.find('.invoice_B2C_panel');
            //B2C OR B btn
            invoiceTypeInput.change(function () {
                if($(this).val() == 0){
                    B2CInfoPanel.show();
                }else{
                    B2CInfoPanel.hide();
                }
            });
            //donation btn
            receiptDonationInput.change(function () {
                $('.receipt_donation_code').toggle($(this).val());
            });
            //invoice carry
            invoiceCarryInput.change(function () {
                if($(this).val() == 'null'){
                    $('.receipt_donation_gp').show();
                }else{
                    $('.receipt_donation_gp').hide();
                }
                if($(this).val() == 0 || $(this).val() == 1 || $(this).val() == 2){
                    $('.receipt_carry_num').show();
                }else{
                    $('.receipt_carry_num').hide();
                }
            })

        })(jQuery);

        (function($){
            var $submitBtn = $('#tappaySubmitBtn');
            var $ccNumberInput = $('#cc_number').payment('formatCardNumber');
            var $ccExpMonthInput = $('#cc_exp_month');
            var $ccExpYearInput = $('#cc_exp_year');
            var $ccvInput = $('#ccv');
            var paymentCardForm = $('#cardview-container');
            var paymentUserInfoForm = $('#payment_user_info_form');

            TPDirect.setupSDK({{env('TAP_PAY_APP_ID')}},'{{ env('TAP_PAY_APP_KEY')}}', '{{env('TAP_PAY_F2E_SETUP_SDK')}}');
            $submitBtn.preventDoubleClick();
            $submitBtn.click(function(e){
                e.preventDefault();
                var cc_number = $ccNumberInput.val();
                var cc_exp_month = $ccExpMonthInput.val();
                var cc_exp_year = $ccExpYearInput.val();
                var ccv = $ccvInput.val();
                var primeToken = '';
                var params = paymentUserInfoForm.serializeArray();
                var creditCardParams = paymentCardForm.serializeArray();
                params = params.concat(creditCardParams);
                params = paymentFieldProcess(params);
                if(!paymentFieldValidator()){
                    $submitBtn.unlockPreventDoubleClick();
                    return false;
                }
                TPDirect.card.createToken(cc_number.split(' ').join(''), cc_exp_month, cc_exp_year, ccv, function (res) {
                    if (res.status != 0) {
                        alert('信用卡資料錯誤');
                        console.log(res);
                        $submitBtn.unlockPreventDoubleClick();
                        return false;
                    }
                    primeToken = res.card.prime;
                    params.prime_token = primeToken;
                    payment_process(params);
                });
            })

            function paymentFieldProcess(params) {
                var output = {};
                $.each(params,function () {
                    output[this.name] = this.value;
                });
                if(output.receipt_donation == '0'){
                    output.receipt_donation_code = '';
                }
                if(output.receipt_carry_type == 'null'){
                    output.receipt_carry_num = '';
                }else{
                    output.receipt_donation_code = '';
                }

                console.log(output);
                return output;
            }
            function paymentFieldValidator(){
                var $hasRequired = false;
                paymentUserInfoForm.find(':input').each(function () {
                    if($(this).prop('required') && ($(this).val() == null || $(this).val() == undefined || ($(this).val() == '')) ){
                        $(this).closest('.form-group').addClass('has-error');
                        $(this).focus();
                        $hasRequired = true;
                        return false;

                    }
                })
                // 信用卡資訊檢查
                paymentCardForm.find(':input').each(function () {
                    if($(this).prop('required')){
                        if($(this).attr('type') == 'checkbox'){
                            if(!$(this).is(":checked")){
                                $(this).closest('label').addClass('text-danger');
                                $(this).focus();
                                $hasRequired = true;
                                return false;
                            }
                        } else if( $(this).val() == null || $(this).val() == undefined || ($(this).val() == '') ) {
                            $(this).closest('.form-group').addClass('has-error');
                            $(this).focus();
                            $hasRequired = true;
                            return false;
                        }
                    }

                })
                if($hasRequired == true){
                    return false;
                }
                //發票選項檢查
                if(paymentCardForm.find('[name="invoice_type"]:checked').val() == 0){
                    if(paymentCardForm.find('[name="receipt_carry_type"]:checked').val() != 'null'){
                        var receiptCarryCode = paymentCardForm.find('[name="receipt_carry_num"]');
                        var receiptCarryType = paymentCardForm.find('[name="receipt_carry_type"]:checked');
                        if(receiptCarryCode.val() == undefined || receiptCarryCode.val() == ''){
                            receiptCarryCode.focus();
                            return;
                        }else if(receiptCarryType.val() == 0){
                            if(receiptCarryCode.val().length != 8) {
                                alert('手機載具號碼由\"斜線\/\"與7個英數混合字串組成。');
                                receiptCarryCode.focus();
                                return;
                            }else if(receiptCarryCode.val().charAt(0) != '/'){
                                alert('第一個字元是\"斜線\/\"。');
                                receiptCarryCode.focus();
                                return;
                            }
                        }else if(receiptCarryType.val() == 1){
                            if(receiptCarryCode.val().length != 16) {
                                alert('載具號碼字串長度是16位，前2位英字加後14位數字');
                                receiptCarryCode.focus();
                                return;
                            }else{
                                function isAlphaOrParen(str) {
                                    return /^[a-zA-Z]+$/.test(str);
                                }

                                $str = receiptCarryCode.val();
                                if( !$.isNumeric($str.substring(2,17)) ){
                                    alert('載具號碼字後14位是數字');
                                    receiptCarryCode.focus();
                                    return;
                                }else if(!isAlphaOrParen($str.substring(0,2))){
                                    alert('載具號碼字首兩2位是英文字');
                                    receiptCarryCode.focus();
                                    return;
                                }
                            }
                        }
                    }else{
                        //捐贈欄位
                        if(paymentCardForm.find('[name="receipt_donation"]:checked').val() == 1){
                            var receiptDonationCode = paymentCardForm.find('[name="receipt_donation_code"]');
                            if(receiptDonationCode.val() == undefined || receiptDonationCode.val() == ''){
                                receiptDonationCode.focus();
                                return false;
                            }else if(receiptDonationCode.val().length < 3){
                                alert('捐贈碼長度最小3位。');
                                receiptDonationCode.focus();
                                return false;
                            }else if(!$.isNumeric(receiptDonationCode.val())){
                                alert('捐贈碼需全為數字。');
                                receiptDonationCode.focus();
                                return false;
                            }
                        }
                    }
                }else if(paymentCardForm.find('[name="invoice_type"]:checked').val() == 2){
                    if(paymentCardForm.find('[name="B2B_id"]').val() == undefined || paymentCardForm.find('[name="B2B_id"]').val() == ''){
                        paymentCardForm.find('[name="B2B_id"]').focus();
                        return false;
                    }
                }else{
                    return false;
                }

                return true;
            }
            function payment_process(params) {
                $.post('/api-web/v1/transaction/pay',params,function(res){
                    if(res.success){
                        swal({
                            title: '購買成功',
                            confirmButtonClass: 'btn-success waves-effect waves-light',
                            confirmButtonText: '打開我的票券!',
                            closeOnConfirm: false
                        },function () {
                            window.location.href = '/my_ticket'
                        });
                    }else if(!res.success){
                        swal({
                            title: '購買失敗',
                            text: res.msg,
                            confirmButtonClass: 'btn-success waves-effect waves-light',
                            confirmButtonText: '重試',
                            closeOnConfirm: false
                        },function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: '購買失敗',
                            confirmButtonClass: 'btn-success waves-effect waves-light',
                            confirmButtonText: '重試',
                            closeOnConfirm: false
                        },function () {
                            window.location.reload();
                        });
                    }
                });
            }
        })(jQuery);

    </script>
@endsection