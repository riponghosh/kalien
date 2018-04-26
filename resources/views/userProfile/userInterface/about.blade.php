@extends('userProfile.userInterface.main')
@inject('CountryPresenter','App\Presenters\CountryPresenter')
@inject('UserPresenter','App\Presenters\UserPresenter')
<?php
$firstname = null;
$lastname = null;
if($user->name != null){
    $fullname = explode(" ",$user->name);
    $lastname = array_pop($fullname);
    $firstname = implode(" ",$fullname);
};
if($user->sex != null){
    $sex = $user->sex;
}else{
    $sex = 'null';
};
if($user->country != null || $user->country != ''){
    $country = $user->country;
}else{
    $country = 'null';
}
if(count($user->intro_video) > 0){
    $intro_video_url = $user->intro_video[0]->media->media_location_standard;
}else{
    $intro_video_url = '';
}
if($is_tourist && isset($user->guide)){
    /****
    * 進階
    **/
    if($user->country != null || $user->country != ''){
        $country = $user->country;
    }else{
        $country = 'null';
    }
    /*
    **language fluent
    */
    /*流利語言列表*/
    $lan_arr_fluent = array();
    /*user的流利語言*/
    $lan_user_arr_fluent = array();
    foreach($languages_list as $lan_list){
        $id = $lan_list->id;
        /*翻譯語言名稱 如：中文 －> chinese = 中文； 日文 －> chinese = 中囯語*/
        $name = $UserPresenter->language($lan_list->language_name);
        $lan_arr_fluent[$id] = $name;
    }
    foreach ($user->languages as $v) {
        if($v->level == 3){
            array_push($lan_user_arr_fluent,$v->language_id);
        }
    }
    /*
    **language familiar
    */
    /*一般語言列表*/
    $lan_arr_familiar = array();
    /*user的一般語言*/
    $lan_user_arr_familiar = array();
    foreach($languages_list as $lan_list){
        $id = $lan_list->id;
        $name = $UserPresenter->language($lan_list->language_name);
        $lan_arr_familiar[$id] = $name;
    }
    foreach ($user->languages as $v) {
        if($v->level == 2){
            array_push($lan_user_arr_familiar,$v->language_id);
        }
    }
    $service_place = array();
    foreach ($user->guide->guideServicePlace as $data){
        array_push($service_place,$data{'city_name'});
    }
    /*
    ** guide service
    */
	$all_guide_services = [
		1 => ['name' => 'us_assistant', 'label' => 'userInterface.us_assistant', 'checked' => ''],
		2 => ['name' => 'us_photographer', 'label' => 'userInterface.us_photographer', 'checked' => ''],
		3 => ['name' => 'us_translator', 'label' => 'userInterface.us_translator', 'checked' => '']
	];
    foreach ($user->guide->userServices as $user_service){
		$all_guide_services[$user_service->service_id]['checked'] = 'checked';
    }
}
?>
@section('css')
    {!! Html::style('css/userInterfaceAbouts.css'.VERSION)!!}
@endsection
@section('title')
    @lang('userInterface.Abouts')
@endsection
@section('interfaceContent')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            @include('userProfile.userInterface.about_summary')
        </div>
        @if($is_tourist == 1 && isset($user->guide))
        <div class="col-lg-12">
            @include('userProfile.userInterface.about_advance')
        </div>
        <div class="col-lg-12">
            @include('userProfile.userInterface.about_service')
        </div>
        @endif
    </div>
</div>
@endsection
@section('js_plugin')
    {!! Html::script('js/userInterfaceAbouts/pulgin.js'.VERSION); !!}
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            /*
            *
            */
            $('.resendEmailActivateCode-btn').click(function () {
                $action = resendActivateCodeAPI();
                if($action.success){
                    swal({
                        title: '發送成功，請檢查電子郵箱',
                        confirmButtonClass: 'btn-success waves-effect waves-light',
                        confirmButtonText: '知道',
                    })
                }else{
                    swal({
                        title: '發送失敗',
                        confirmButtonClass: 'btn-success waves-effect waves-light',
                        confirmButtonText: '確認',
                    })
                }

            })
            /*Date Picker*/
            $('#birth_date').datepicker({
                format: 'yyyy-mm-dd',
                defaultViewDate: { year: 1993, month: 12 }

            });
            $(".select_living_country").select2({
                placeholder: "Select your birth place",
                allowClear: true
            });
            $('.select2').select2({
            });

            $('.multi-select3').multiSelect({
                selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                afterInit: function (ms) {
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function (e) {
                            if (e.which === 40) {
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function (e) {
                            if (e.which == 40) {
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                },
                afterSelect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function () {
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });
        });
    @if($is_tourGuide == 1 )
    /*
    * ServiceCountry
    * */
    $("label[href='#guideProfile-{{$user->guide->service_country}}']").addClass('active').find('input').prop('checked',true);
    $('#guideProfile-{{$user->guide->service_country}}').addClass('active in');
    /*checkAll function*/
    $('.checkAll').click(function(e){
        checkboxesName = $(this).attr('name');
        region = $(this).data("region");
        checkboxes = $('[name="' + checkboxesName+ '[]"][data-region="' + region+ '"]');
        for(var i in checkboxes){
            checkboxes[i].checked = e.target.checked;
        }
    })
    @endif
    /*
    ** 基本資料
     */
    $('#guide_status').change(function(){
        $.get('/guide_status/' + $(this).is(':checked'));
    })
    var postUserSummary = function(){
        var data = $('#userBasicForm').serialize();
        return $.post('/PUT/userProfile/summary',data);
    }
    $('#userBasicForm').find('.submit-btn').click(function(){
        var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
            $this.button('reset');
        }, 5000);
        postUserSummary().done(function(response){
            switch(response.success){
                case true:
                    if(response.ref_code == 1){
                        alert('部分資料錯誤');
                        alert(response.msg);
                    }else{
                        alert('資料更新成功');
                    }
                    break;
                case false:
                    alert('失敗');
                    alert(response.msg);
                    break;
            }
            $this.button('reset');
        }).always(function(){
            $this.button('reset');
            window.location.reload();
        });
    });
    /*
    **進階資料
    */
    var serviceRegionProcess = function(){
        $country = $('input[name="service_country"]:checked').val();
        $('select[name="serviceRegion[]"]').not('select[data-region="' +$country+'"]').val([]);
    }
    var postUserGuide = function(){
        serviceRegionProcess();
        var data = $('#userAdvancedForm').serialize();
        return $.post('/PUT/userProfile/guideProfile',data);
    }
    var postUserService = function () {
        var data = $('#userServiceForm').serialize();
        return $.post('/PUT/userProfile/userService',data);
    }
    $('#userAdvancedForm').find('.submit-btn').click(function(){
        var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
            $this.button('reset');
        }, 5000);
        postUserGuide().done(function(response){
            switch(response.success){
                case true:
                    alert('資料更新成功');
                    break;
                case false:
                    if(response.msg != null){
                        alert(response.msg)
                    }else {
                        alert('失敗');
                    }
                    break;
            }
        }).always(function(){
            window.location.reload();
        });
    });
    /*
     **服務表單
     */
    $('#userServiceForm').find('.submit-btn').click(function(){
        var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
            $this.button('reset');
        }, 5000);
        postUserService().done(function(response){
            switch(response.success){
                case true:
                    alert('資料更新成功');
                    break;
                case false:
                    if(response.msg != null){
                        alert(response.msg)
                    }else {
                        alert('失敗');
                    }
                    break;
            }
        }).always(function(){
            window.location.reload();
        });
    });
    /*
    **流程檢驗
     */
    $(document).ready(function(){
        @if(isset($user->guide))
              @if($user->guide->status == 1)
                @if($is_tourist == false)
                  applicationHint(1);
                @elseif($is_tourGuide == false)
                  applicationHint(2);
                @elseif($is_tourGuide == true && $user_icon == null)
                  applicationHint(3);
                @elseif($is_tourGuide == true && $user_icon != null)
                  applicationHint(4);
                @endif
            @endif
        @endif
    });
    /*
     |Hint
     */
    function applicationHint($v){
        if($v == 1){
            var tour = {
                id: "my-intro",
                steps: [
                    {
                        target: "userBasicForm",
                        title: "填寫基本資料",
                        content: "如果希望與不同的人旅遊，請必需完全填寫基本資料。",
                        placement: "left",
                        yOffset: 10,
                        xOffset: 150
                    },
                    {
                        target: "summary_form_submit",
                        title: "填完後提交",
                        content: "確認資料填妥後，按提交。",
                        placement: "left",
                        yOffset: -10,
                    },
                ],
                showPrevButton: true
            };
        }else if($v == 2){
            var tour = {
                id: "my-intro",
                steps: [
                    {
                        target: "advance_inform_box",
                        title: " 進階資料",
                        content: "我們需要知道你可以成為那一個地區的旅遊伙伴，還有你會那幾種語言溝通。",
                        placement: "top",
                        yOffset: 10,
                        xOffset: 350
                    },
                    {
                        target: "advance_form_submit",
                        title: "填完後提交",
                        content: "確認資料填妥後，按提交。",
                        placement: "left",
                        yOffset: -10,
                    },
                ],
                showPrevButton: true
            };
        }else if($v == 3){
            var tour = {
                id: "my-intro",
                steps: [
                    {
                        target: "user-interface-icon",
                        title: " 加入你的大頭貼",
                        content: "有大頭貼的預約機會較高哦!",
                        placement: "right",
                        yOffset: 10,
                    },
                ]
            }
        }else if($v == 4){
            var tour = {
                id: "my-intro",
                steps: [
                    {
                        target: "side-menu-trip-introduction",
                        title: "行程介紹面頁",
                        content: "在你的行程介紹頁加入一些你熟識，且認為有趣或值得去的地方活動，讓外地人可以知道你能與他們去那些地方",
                        placement: "top",
                        xOffset: 40,
                        yOffset: 10,
                    },
                ]
            }
        }
        // Start the tour!
        hopscotch.startTour(tour);
    }
    </script>
@endsection
