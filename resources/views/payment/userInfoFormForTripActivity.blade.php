@inject('UserPresenter','App\Presenters\UserPresenter')
<div class="payment-panel m-b-15">
    <form id="payment_user_info_form">
        <div class="panel-heading">
            <div class="title">{{trans('payment.YourInformation')}}</div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group form-group-sm">
                        <label>電子郵件</label>
                        <input class="form-control" value="{{Auth::user()->email}}" name="email" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="">@lang('payment.PhoneNumber')<span class="text-muted small">(特殊通知會發簡訊致此號碼)</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3 col-xs-4">
                                        <select class="form-control" name="phone_area_code" required>
                                            {{$UserPresenter->create_phone_area_code_option(Auth::user()->phone_area_code)}}
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xs-8">
                                        <input class="form-control" type="text" name="phone_number"  value="{{Auth::user()->phone_number}}" placeholder="聯絡電話號碼" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--End Phone formGroup-->
                </div>
            </div>
        </div>
    </form>
</div>