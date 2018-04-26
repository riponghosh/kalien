<footer style="background: white;">
    <div class="container">
        <div class="row p-b-75 footer-link">
            <div class="col-xs-12 col-sm-4">
                <h4>Pneko.</h4>
                <label class="lg_select_label">
                {{Form::select('cur_select',$UserPresenter->cur_units_list(),CLIENT_CUR_UNIT,['class' => 'lg_select', 'id' => 'cur_selection'])}}
                </label>
            </div>
            <div class="col-xs-12 col-sm-8">
                <div class="col-xs-4">
                    <h5 class="title">@lang('app.ContactUs')</h5>
                    <ul class="social-btn-group list-inline">
                        <li><a href="https://www.facebook.com/Pneko-1998097573809329" target="_blank"><i class="fa fa-facebook-square fa-3x"></i></a></li>
                        <li><a><div class="line-icon"></div></a></li>
                    </ul>
                </div>
                <div class="col-xs-4">
                    <h5 class="title">@lang('app.websiteLanguage')</h5>
                    <ul>
                        <li><h5><a href="{{url('changeLocaleLangauge/en')}}" >English</a></h5></li>
                        <li><h5><a href="{{url('changeLocaleLangauge/zh_tw')}}" >繁體中文</a></h5></li>
                        <li><h5><a href="{{url('changeLocaleLangauge/jp')}}" >日本語</a></h5></li>
                    </ul>
                </div>
                <div class="col-xs-4">
                    <h5 class="title">{{trans('app.other')}}</h5>
                    <ul>
                        <li><h5><a>{{trans('app.joinUs')}}</a></h5></li>
                        <li><h5><a href="{{url('privacyPolicy')}}">{{trans('app.privacyPolicy')}}</a></h5></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row copyright">
            <div class="col-md-12 text-center">
            <p>
                <small>© 2017 喵星人科技股份有限公司. All Rights Reserved.</small>
            </p>
            </div>
        </div>
    </div>
</footer>
