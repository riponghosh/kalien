<div class="card-box" id="advance_inform_box">
    <h4 class="header-title m-t-0 m-b-30">@lang('userInterface.AdvanceInformation')</h4>
    <form class="form-horizontal" id="userAdvancedForm">
        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.ServiceCountry')</label>
            <div class="col-sm-8">
                <div class="btn-group" data-toggle="buttons">
                    <?php $serviceCountrys = ['jp','tw','kp','hk','mo']  ?>
                    @foreach($serviceCountrys as $serviceCountry)
                        <label href="#guideProfile-{{$serviceCountry}}" class="btn btn-default" data-toggle="tab">
                            <input type="radio" name="service_country" autocomplete="off" value="{{$serviceCountry}}">
                            {{$CountryPresenter->service_country_name($serviceCountry)}}
                        </label>
                    @endforeach
                </div><!--btn group country-->
                <div class="tab-content serviceRegion tab-content-bs m-t-10">
                    <div class="tab-pane fade" id="guideProfile-jp">
                        <select name="serviceRegion[]" class="multi-select multi-select3" multiple="multiple" data-region="jp">
                            @foreach($CountryPresenter->get_city('jp') as $data)
                                <option value="{{$data}}"
                                        @if(in_array($data,$service_place))
                                        selected
                                        @endif
                                >{{trans('countries.'.$data)}}
                                </option>
                            @endforeach
                        </select>
                    </div><!--c-jp-->
                    <div class="tab-pane fade" id="guideProfile-tw">
                        <select name="serviceRegion[]" class="multi-select multi-select3" multiple="multiple" data-region="tw">
                            @foreach($CountryPresenter->get_city('tw') as $data)
                                <option value="{{$data}}"
                                        @if(in_array($data,$service_place))
                                        selected
                                        @endif
                                >{{trans('countries.'.$data)}}
                                </option>
                            @endforeach
                        </select>
                    </div><!--c-tw-->
                    <div class="tab-pane fade" id="guideProfile-kp">
                    </div><!--c-kp-->
                    <div class="tab-pane fade" id="guideProfile-hk">
                    </div><!--c-hk-->
                    <div class="tab-pane fade" id="guideProfile-mo">
                    </div><!--c-mo-->
                </div><!--row servicreRegion-->
            </div>
        </div><!--End serviceCountry FormGroup-->
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.LanguageFluent')</label>
            <div class="col-sm-6">
                {{Form::select('languages_fluent[]',$lan_arr_fluent,$lan_user_arr_fluent,['multiple','class'=>'select2 select2-multiple','data-placeholder' => 'languages (fluent)'])}}
            </div>
        </div><!--End Language Fluent FormGroup-->
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.LanguageGood')</label>
            <div class="col-sm-6">
                {{Form::select('languages_familiar[]',$lan_arr_familiar,$lan_user_arr_familiar,['multiple','class'=>'select2 select2-multiple','data-placeholder' => 'languages (familiar)'])}}
            </div>
        </div><!--End Language good FormGroup-->
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9 m-t-15">
                <button id="advance_form_submit" type="button" class="submit-btn btn btn-primary waves-effect waves-light">@lang('userInterface.Submit')</button>
                <button class="btn btn-default waves-effect m-l-5">@lang('userInterface.Cancel')</button>
            </div>
        </div>
    </form>
</div><!-- End card-box-->