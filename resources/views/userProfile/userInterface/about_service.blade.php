<div class="card-box" id="service_inform_box">
    <h4 class="header-title m-t-0 m-b-30">@lang('userInterface.ServiceAndPrice')</h4>
    <form class="form-horizontal" id="userServiceForm">
        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.PricingForService')</label>
            <div class="col-md-1">
                <?php $sevice_pricing_unit = isset($user->guide->currency_unit) ? $user->guide->currency_unit : Cookie::get('currency_unit')?>
                {{Form::select('charge_per_day_cur_select',$UserPresenter->cur_units_list(),$sevice_pricing_unit,['class' => 'form-control', 'id' => 'charge_per_day_cur_select'])}}
            </div>
            <div class="col-sm-2 input-group">
                <span class="input-group-addon bg-primary b-0 text-white">$</span>
                <input class="form-control" type="number" name="charge_per_day" placeholder="0.00" value="{{$user->guide->charge_per_day}}" max="1000000"/>
                <span class="input-group-addon bg-primary b-0 text-white">每日</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.ServiceList')</label>
            <div class="col-sm-6">
                @foreach($all_guide_services as $guide_service)
                    <div class="checkbox checkbox-pink">
                        <input type="checkbox" class="form-control" value="{{$guide_service['name']}}" name="user_service[]" id="{{$guide_service['name']}}" {{$guide_service['checked']}} />
                        <label for="{{$guide_service['name']}}">@lang($guide_service['label'])</label>
                    </div><!--End div checkbox-->
                @endforeach
            </div>
        </div><!-- End form-group -->
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9 m-t-15">
                <button id="user_service_form_submit" type="button" class="submit-btn btn btn-primary waves-effect waves-light">@lang('userInterface.Submit')</button>
                <button class="btn btn-default waves-effect m-l-5">@lang('userInterface.Cancel')</button>
            </div>
        </div><!-- End form-group -->
    </form>
</div>