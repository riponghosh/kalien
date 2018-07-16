@inject('UserPresenter','App\Presenters\UserPresenter')
<footer class="footer container">
    <div class="row">
        <div class="col-xs-6">
            2016 © Pneko.
            <div class="row">
                <div class="col-md-2">
                    {{Form::select('cur_select',$UserPresenter->cur_units_list(),CLIENT_CUR_UNIT,['class' => 'form-control', 'id' => 'cur_selection'])}}
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <ul class="list-unstyled">
                @lang('app.websiteLanguage')
                <li>
                    <a href="{{url('/changeLocaleLanguage/en')}}" >English</a>
                </li>
                <li>
                    <a href="{{url('/changeLocaleLanguage/zh_tw')}}" >繁體中文</a>
                </li>
            </ul>
        </div>
    </div>
</footer>