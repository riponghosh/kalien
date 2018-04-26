<div class="payment-panel m-b-15">
    <form id="payment_user_info_form">
        <div class="panel-heading">
            <div class="title">{{trans('payment.YourInformation')}}</div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>{{trans('payment.Title')}}</label>
                        <select class="form-control" name="salutation">
                            <option>{{trans('payment.TitleMale')}}</option>
                            <option>{{trans('payment.TitleFemale')}}</option>
                        </select>
                    </div><!-- End form-group-->
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('payment.PassportFirstName')}}</label>
                        <input type="text" class="form-control" placeholder="填寫護照上英文名字" name="first_name"/>
                    </div><!-- End form-group-->
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('payment.PassportLastName')}}</label>
                        <input type="text" class="form-control" placeholder="填寫護照上英文姓氐" name="last_name"/>
                    </div><!-- End form-group-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>國籍</label>
                        <select class="form-control select_pt_country" name="country">
                            {{$CountryPresenter->create_country_option()}}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="">@lang('payment.PhoneNumber')</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-control" name="phone_area_code">
                                            {{$UserPresenter->create_phone_area_code_option()}}
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="phone_number"  value="" placeholder="聯絡電話號碼"/>
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