@inject('CountryPresenter','App\Presenters\CountryPresenter')
<div class="modal fade " id="Searching_filter_Modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body filter-body">
                <div class="filter_item row text-center" id="gender_filter_list">
                    <p class="title h4">你想選擇男生還是女生？</p>
                    <div class="row" data-toggle="buttons">
                     <label class="btn btn-outline-kalien">
                            <input type="radio" name="gender_options" autocomplete="off" value="M">Male
                    </label>
                    <label class="btn btn-outline-kalien">
                         <input type="radio" name="gender_options" value="F" autocomplete="off">Female
                    </label>
                    <label class="btn btn-outline-kalien active">
                         <input type="radio" name="gender_options" value="both" autocomplete="off" checked>Both
                    </label>
                    </div>
                </div>
                <hr>
                <div class="filter_item row text-center" id="age_filter_list">
                    <p class="title h4">年齡範圍</p>
                        <div class="col-md-6 col-md-offset-2 col-xs-11 col-xs-offset-1" style="height: 60px;">
                            <div id="slider" style="width:95%;"></div>
                        </div>
                        <div class="col-md-3 col-xs-12">
                        <span id="min-age-slider-value"></span>
                        <span>-</span>
                        <span id="max-age-slider-value"></span>
                        <span>歲</span>
                        </div>
                </div>
                <hr>
                <div class="filter_item row text-center" id="country_filter_list">
                    <p class="title h4">進一步的地區範圍</p>
                    <div class="row text-center" role="group">
                        <div class="row btn-group" data-toggle="buttons">
                             <label href="#filter-jp" class="btn btn-default" data-toggle="tab">
                                    <input type="radio" name="country_options" autocomplete="off" value="jp">日本
                            </label>
                            <label href="#filter-tw" class="btn btn-default" data-toggle="tab">
                                    <input type="radio" name="country_options" autocomplete="off" value="tw">台灣
                            </label>
                            <label href="#filter-kp" class="btn btn-default" data-toggle="tab">
                                    <input type="radio" name="country_options" autocomplete="off" value="kp">韓國
                            </label>
                            <label href="#filter-hk" class="btn btn-default" data-toggle="tab">
                                    <input type="radio" name="country_options" autocomplete="off" value="hk">香港
                            </label>
                            <label href="#filter-mo" class="btn btn-default" data-toggle="tab">
                                    <input type="radio" name="country_options" autocomplete="off" value="mo">澳門
                            </label>
                        </div>
                    </div>
                    <div class="row tab-content serviceRegion">
                        <div class="tab-pane fade" id="filter-jp">
                            <div class="checkbox-inline">
                                <label><input class="checkAll" name="region" data-region="jp" type="checkbox" value="jp_all">全選</label>
                            </div>
                            @foreach($CountryPresenter->get_jp_city() as $data)
                                <div class="checkbox-inline">
                                    <label><input type="checkbox" name="region" data-region="jp" value="{{$data}}">{{trans('countries.'.$data)}}</label>
                                </div>
                            @endforeach
                        </div><!--c-jp-->
                        <div class="tab-pane fade" id="filter-tw">
                         <div class="checkbox-inline">
                            <label><input class="checkAll" name="region" data-region="tw" type="checkbox" value="tw_all">全選</label>
                        </div>
                        @foreach($CountryPresenter->get_tw_city() as $data)
                        <div class="checkbox-inline">
                            <label><input type="checkbox" name="region" data-region="tw" value="{{$data}}">{{trans('countries.'.$data)}}</label>
                        </div>
                        @endforeach
                        </div><!--c-tw-->
                        <div class="tab-pane fade" id="filter-kp">
                        </div><!--c-kp-->
                        <div class="tab-pane fade" id="filter-hk">
                        </div><!--c-hk-->
                        <div class="tab-pane fade" id="filter-mo">
                        </div><!--c-mo-->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="filter-submit" class="btn btn-primary">Go</button>
                <button class="cancel-btn btn btn-default">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
/*checkAll function*/
$('.checkAll').click(function(e){
    checkboxesName = $(this).attr('name');
    region = $(this).data("region");
    checkboxes = $("[name=" + checkboxesName+ "][data-region=" + region+ "]");
    for(var i in checkboxes){
        checkboxes[i].checked = e.target.checked;
    }
})
</script>
