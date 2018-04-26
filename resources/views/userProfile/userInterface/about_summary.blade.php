<div class="card-box">
    <div class="pull-right">
        <label for="guide_status" class="control-label m-r-10">@lang('userInterface.I_want_to_be_a_Guide')</label>
        @if($guide_status == 1)
            <input type="checkbox" id="guide_status" checked name="guide_status" data-plugin="switchery" data-color="#00b19d" data-size="small"/>
        @elseif($guide_status == 0)
            <input type="checkbox" id="guide_status" name="guide_status" data-plugin="switchery" data-color="#00b19d" data-size="small"/>
        @endif
    </div>
    <h4 class="header-title m-t-0 m-b-30">@lang('userInterface.BasicInformation')</h4>
    <form class="form-horizontal" id="userBasicForm">
        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.Email')</label>
            <div class="col-sm-3">
                <p class="h5">{{$user['email']}}</p>
            </div>
            <div class="col-sm-3">
                @if($user['is_activated'] == 0)
                    <button type="button" class="resendEmailActivateCode-btn btn btn-danger btn-xs" style="vertical-align: sub">@lang('userInterface.resendEmailActivateCode')</button>
                @else
                    <span class="label label-success" style="vertical-align: sub">@lang('userInterface.mailVerified')</span>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.Name')</label>
            <div class="col-sm-3">
                <input class="form-control" type="text" name="first_name" placeholder="@lang('userInterface.FirstName')" value="{{$firstname}}"/>
            </div>
            <div class="col-sm-3">
                <input class="form-control" type="text" name="last_name" placeholder="@lang('userInterface.LastName')" value="{{$lastname}}" />
            </div>
        </div><!--End Name formGroup-->
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 control-label">@lang('userInterface.Phone')</label>
            <div class="col-xs-2 col-sm-2">
                <select class="form-control" name="phone_area_code">
                    {{$UserPresenter->create_phone_area_code_option($user->phone_area_code)}}
                </select>
            </div>
            <div class="col-xs-10 col-sm-4">
                <input class="form-control" type="text" name="phone_number"  value="{{$user->phone_number}}"/>
                <span class="font-13 text-muted">example : 091994529</span>
            </div>
        </div><!--End Phone formGroup-->
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.BirthDate')</label>
            <div class="col-sm-6">
                <div class="input-group">
                    <input class="form-control" type="text" name="birth_date" value="{{$user->birth_date}}" placeholder="yyyy-mm-dd" id="birth_date"/>
                    <span class="input-group-addon bg-primary b-0 text-white"><i class="zmdi zmdi-calendar"></i></span>
                </div>
            </div>
        </div><!--End of Birth Date-->
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.Birthplace')</label>
            <div class="col-sm-6">
                <select class="form-control select_living_country" name="country">
                    <option></option>
                    {{$CountryPresenter->create_country_option($country)}}
                </select>
            </div>
        </div><!--End brithplace formGroup-->
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.Gender')</label>
            <div class="col-sm-6">
                <select class="form-control" name="sex">
                    <option value="" disabled selected>@lang('userInterface.Gender')</option>
                    <option value="M" {{$sex == 'M' ? 'selected' : ''}} >@lang('userInterface.Male')</option>
                    <option value="F" {{$sex == 'F' ? 'selected' : ''}} >@lang('userInterface.Female')</option>
                </select>
            </div>
        </div><!--End Gender-->
        <div class="form-group">
            <label class="col-sm-3 control-label">@lang('userInterface.IntroVideoUrl')</label>
            <div class="col-sm-6">
                <input class="form-control" type="text" name="intro_video" placeholder="url:youtube..." value="{{$intro_video_url}}"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9 m-t-15">
                <button id="summary_form_submit" type="button" class="submit-btn btn btn-primary waves-effect waves-light">@lang('userInterface.Submit')</button>
                <button class="btn btn-default waves-effect m-l-5">@lang('userInterface.Cancel')</button>
            </div>
        </div>
    </form>
</div><!-- End card-box-->