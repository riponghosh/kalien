function sdReqJoinGpActivity(params, err, callback) {
    $res = $.ajax({
        type: 'POST',
        url: '/api-web/v1/group_activity/apply_for_join_in',
        data: {gp_activity_id: params.gpActivityId, known_is_participant: params.justPurchaseGroupTicket},
        statusCode: {
            401: function () {
                err();
                loginModal.show();
                login.afterSuccessToDo($.Callbacks().add(sdReqJoinGpActivity), [params, err, callback]);
            }
        },
        success: function (res) {
            callback(res,params,err,callback);
        }
    });
}
function sdReqJoinGpActivityCallback(res,params,err,callback) {
    $msg = res.msg != undefined ? res.msg: '請重試';
    if(res.success){
        window.location.href = '/payment';
    }else if(!res.success){
        if(res.code == 2){
            swal({
                title: '已是其中一位參加者',
                type: 'warning',
                text: $msg,
                confirmButtonText: '我要購票',
                showCancelButton: true,
                closeOnConfirm: false
            },function () {
                params.justPurchaseGroupTicket = true;
                sdReqJoinGpActivity(params,err,callback);
            })
        }else{
            function errswal($msg) {
                swal({
                    title: '參加失敗',
                    type: 'error',
                    text: $msg,
                    confirmButtonText: '知道',
                })
            }
            errswal($msg)
        }

    }else{
        swal({
            title: '參加失敗',
            type: 'error',
            text: '請聯絡客服。',
            confirmButtonText: '知道',
        })
    }
}
//----------------------------------------------
// Group Activity Component
//----------------------------------------------
//舉辦日期
holdActivityStartDayInput = function ($el) {
    var $element = $el;
    function bindDatePicker(DisableWeeks, DisableDates, EndDate) {
        var datepickerIntParam = {
            format: "yyyy-mm-dd",
            todayHighlight: true,
            startDate: 'today'
        };
        if(EndDate != null) datepickerIntParam.endDate = EndDate;
        if(DisableWeeks != null) datepickerIntParam.daysOfWeekDisabled = DisableWeeks;
        if(DisableDates != null) datepickerIntParam.datesDisabled = DisableDates;
        $element.datepicker(datepickerIntParam);
    }
    return {
        bindDatePicker: function(DisableWeeks, DisableDates, EndDate){
            bindDatePicker(DisableWeeks, DisableDates, EndDate);
        },
        value: function () {
            return $element.val();
        },
        el: function () {
            return $element;
        }
    }
};
holdActivityStartTimeInput = function ($el) {
    var $element = $el;
    return {
        bindTimeSelect: function (timeRanges) {
            lastTimeRange = null;
            activeCrossTime = false;
            $i = 0;
            _.forEach(timeRanges, function(value) {
                var timeString = value.split(":")[0]+":"+value.split(":")[1];
                if(activeCrossTime == false){
                    if(lastTimeRange != null && moment(lastTimeRange, 'H:i:s').isAfter(moment(value, 'H:i:s'))){
                        if(activeCrossTime == false){
                            $element.prepend( new Option('關門','cross'));
                            $element.find('option[value="cross"]').attr('disabled',true);
                            activeCrossTime = true;
                            $element[0].options.add( new Option(timeString, value) ,$element[0][$i]); //加插夜班前
                            $i++;
                        }else{
                            $element[0].options.add( new Option(timeString,value) ); // 正常
                        }
                    }else{
                        $element[0].options.add( new Option(timeString,value) );
                    }
                }else{
                    $element[0].options.add( new Option(timeString,value) ,$element[0][$i]);//加插夜班前
                    $i++;
                }
                lastTimeRange = value;
            });
        },
        value: function () {
            return $element.val();
        }
    }
}
//-----------------------------
createGpActivity = function ($container) {
    var TicketId = $container.data('ticketId');
    var $startDateInput = holdActivityStartDayInput($container.find('[name="start_date"]'));
    var $limitApplicantsInput = $container.find('[name="limitApplicants"]');
    var $startTimeInput = holdActivityStartTimeInput($container.find('[name="start_time"]'));
    var $activityTitleInput = $container.find('[name="gp_activity_title"]');
    var isRawStartDateTime = false;  //判斷是否已抓日期時間
    function rawDisableDates() {
        if(isRawStartDateTime == false){
            $.ajax({
                type: 'POST',
                url: '/api-web/v1/activity_ticket/get_ticket_available_purchase_dates_and_time_ranges',
                data: {trip_activity_ticket_id: TicketId},
                success: function (res) {
                    if(res.success){
                        resData = res.data;
                        if(resData.sold_dates != null){
                            $startDateInput.bindDatePicker(resData.sold_dates.disable_weeks, resData.sold_dates.disable_dates, resData.sold_dates.end_date);
                        }
                        if(resData.time_ranges != null){
                            $startTimeInput.bindTimeSelect(resData.time_ranges);
                        }
                        isRawStartDateTime = true;
                    }

                }
            })
        }
    }
    $container.find('.create-gp-activity-btn').click(function () {
        function AjaxCreateGpActivity(params) {
            $.ajax({
                url: '/group_activity_api/create',
                data: params,
                statusCode: {
                    401: function () {
                        loginModal.show();
                        login.afterSuccessToDo($.Callbacks().add(AjaxCreateGpActivity), [params]);
                    }

                },
                success: function (res) {
                    if(res.success){
                        swal({
                                title:'建立成功!',
                                showCancelButton: true,
                                confirmButtonClass: 'btn-success waves-effect waves-light',
                                confirmButtonText: '打開活動頁',
                                closeOnConfirm: false,
                            },function () {
                                window.location.href = res.gp_activity_url+'?'+'hopscotchAction=1';
                            }
                        );

                    }else if(!res.success){
                        if(res.status == 'not_tourist'){
                            swal({
                                    type: 'warning',
                                    title:'個人資料不足!',
                                    text: '發起活動人需要基本的足夠資料',
                                    showCancelButton: true,
                                    confirmButtonClass: 'btn-success waves-effect waves-light',
                                    confirmButtonText: '我要填寫',
                                    closeOnConfirm: false,
                                },function () {
                                    window.open('/user/abouts', '_blank');
                                }
                            );
                        }else{
                            swal({
                                    type: 'error',
                                    title:'建立失敗!',
                                    text: res.msg,
                                    showCancelButton: true,
                                    confirmButtonClass: 'btn-success waves-effect waves-light',
                                    confirmButtonText: '重新載入網頁',
                                    closeOnConfirm: false,
                                },function () {
                                    window.location.reload();
                                }
                            );
                        }
                    }else{
                        swal({
                                type: 'error',
                                title:'建立失敗!',
                                confirmButtonClass: 'btn-success waves-effect waves-light',
                                showCancelButton: true,
                                confirmButtonText: '重新載入網頁',
                                closeOnConfirm: false
                            },function () {
                                window.location.reload();
                            }
                        );
                    }
                }
            })
        }
        //start-date validation
        if(!moment($startDateInput.value(), 'YYYY-MM-DD').isValid()){
            console.log($startDateInput.value());
            $startDateInput.el().closest('.form-group').addClass('has-error');
            return;
        }
        //start-time validation
        if($startTimeInput.value() == null){
            return;
        }
        params = {
            activity_ticket_id: TicketId,
            start_date: $startDateInput.value(),
            start_time: $startTimeInput.value(),
            activity_title: $activityTitleInput.val()
        };
        if($limitApplicantsInput.val() != 'unlimited'){
            params.limit_joiner =  $limitApplicantsInput.val();
        }
        AjaxCreateGpActivity(params);

    })
    return{
        rawDisableDatesData: function () {
            rawDisableDates();
        }
    }

}