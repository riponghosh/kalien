<div class="row" id="tw_receipt_title">
    <div class="col-md-12">
        <p class="m-b-25 title h4"><span>@lang('payment.ReceiptInfo')</span><span class="text-warning h6">點選檢視或編輯</span></p>
    </div>
</div>
<div id="invoice-info-panel" style="display:none;">
<div class="row">
    <div class="col-md-12">
        <p><span class="text-warning h6">@lang('payment.RuleOfGOVReceipt')</span></p>
    </div>
</div>
<div class="row form-group">
    <div class="col-xs-4 col-sm-3">
        <div class="radio-inline" style="vertical-align: -webkit-baseline-middle;">
            <label><input type="radio" name="invoice_type" checked value=0 >@lang('payment.EReceipt')</label>
        </div>
    </div>
    <div class="col-xs-8">
        <div class="input-group input-group-sm input-group-inline">
                <span class="input-group-addon">
                    <input type="radio" name="invoice_type" value=2 aria-label="Radio button for following text input">
                </span>
            <input type="text" class="form-control" name="B2B_id" aria-label="Text input with radio button" placeholder="@lang('payment.taxID')">
        </div>
    </div>
</div><!-- End row receipt type-->
<div class="row invoice_B2C_panel">
    <div class="col-xs-12">
        <div class="row form-group">
            <div class="col-md-12">
                <label>@lang('payment.SelectReceiptDevice')</label>
            </div>
            <div class="col-md-8">
                <div class="radio-inline" style="vertical-align: -webkit-baseline-middle;">
                    <label><input type="radio" name="receipt_carry_type" checked value="null">@lang('payment.No')</label>
                </div>
                <div class="radio-inline" style="vertical-align: -webkit-baseline-middle;">
                    <label><input type="radio" name="receipt_carry_type" value="1">@lang('payment.NaturalPersonCertificate')</label>
                </div>
                <div class="radio-inline" style="vertical-align: -webkit-baseline-middle;">
                    <label><input type="radio" name="receipt_carry_type" value="0">@lang('payment.MobilePhoneBarCode')</label>
                </div>
            </div>
            <div class="receipt_carry_num col-md-5" style="display:none">
                <input class="form-control input-sm" placeholder="載具號碼" name="receipt_carry_num">
            </div>
        </div><!--end row invoice carry-->
        <div class="receipt_donation_gp row form-group">
            <div class="col-md-12">
                <label>@lang('payment.DonateYourReceipt')</label>
            </div>
            <div class="col-sm-4 col-md-3">
                <div class="radio-inline" style="vertical-align: -webkit-baseline-middle;">
                    <label><input type="radio" name="receipt_donation" checked value="0">@lang('payment.No')</label>
                </div>
                <div class="radio-inline" style="vertical-align: -webkit-baseline-middle;">
                    <label><input type="radio" name="receipt_donation" value="1">@lang('payment.Yes')</label>
                </div>
            </div>
            <div class="receipt_donation_code col-sm-6 col-md-5" style="display: none">
                <input class="form-control input-sm" placeholder="捐贈碼" name="receipt_donation_code">
            </div>
        </div><!--end row donate receipt-->
    </div>
</div><!-- invoice_B2C_panel -->
<div class="row">
    <div class="col-md-9">
        <div class="form-group">
            <label>@lang('payment.AddressForReceiptLottery')</label>
            <input class="form-control input-sm" placeholder="(選填)" name="address_for_lottery_mailing" value="{{Auth::user()->living_address}}">
        </div>
    </div>
</div><!-- End row -->
</div>
<div class="row">
    <div class="col-md-12 form-group">
        @if(app()->getLocale() == 'jp')
            <label><input name="agree_policy_checkbox" type="checkbox" style="margin-right: 5px;" required><a href="{{url('servicePolicy')}}" target="_blank">@lang('payment.ServicePolicy')</a> 、 <a href="{{url('privacyPolicy')}}" target="_blank">@lang('payment.PrivacyPolicy')</a>に同意する</label>
        @else
            <label><input name="agree_policy_checkbox" type="checkbox" style="margin-right: 5px;" required>@lang('payment.IAgreeAndAccept') <a href="{{url('servicePolicy')}}" target="_blank">@lang('payment.ServicePolicy')</a> @lang('payment.And') <a href="{{url('privacyPolicy')}}" target="_blank">@lang('payment.PrivacyPolicy')</a></label>
        @endif
    </div>
</div>

<script>
$('#tw_receipt_title').click(function () {
    $('#invoice-info-panel').toggle();
})
</script>