;(function($) {
    var tripActivityId;
    var tripActivityLanguage;
    $.extend({
        "tripActivityEditorSetBasicData" : function(data){
            tripActivityId = data.tripActivityId;
            tripActivityLanguage = data.tripActivityLanguage;
        },
        "setTripActivityEditorIntroImgData": function (data) {
            $.each(data,function (k, data) {
                createIntroImgEditor(data.imgPath, data.tripImgId, data.tripImgDescription);
            })

        }
    })
/*-------------------------------
 | Component UI
 -------------------------------*/
    $introImgInput = '<input type="file" name="trip_activity_intro_img" class="dropify" data-default-file="" data-show-errors="true" />';
    $introImgDescription = '<input type="text" name="trip_activity_intro_img_description" class="form-control m-t-15">';
    $introImgSubmitBtn = '<button class="submit-intro-img-btn btn btn-primary m-t-15">提交</button>';
    $introImgEditor =
        '<div class="card-box" style="border: 1px #fff solid">' +
            $introImgInput +
            $introImgDescription +
            $introImgSubmitBtn +
        '</div>';
    $introImgInputBootstrapShell = '<div class="col-md-6">'+$introImgEditor+'</div>';

/*-------------------------------
 | 功能
 -------------------------------*/
    function createIntroImgEditor(imgPath, tripImgId, tripImgDescription) {
        $content = $($introImgInputBootstrapShell);
        $content.find('[name="trip_activity_intro_img"]').attr('data-default-file', imgPath);
        $content.find('[name="trip_activity_intro_img_description"]').val(tripImgDescription);
        //控制input disable
        if(imgPath == null || imgPath == undefined){
            $content.find('[name="trip_activity_intro_img_description"]').attr('disabled', true);
            $content.find('.submit-intro-img-btn').attr('disabled', true);
        }
        $('.activity-img-groups-section').append($content);
        bindIntroImgEditorAction($content, tripImgId);
    }
/*-------------------------------
 | Bind Btn
 -------------------------------*/
    $('.create-trip-activity-img-editor-btn').click(function () {
        createIntroImgEditor(null);
    })
/*-------------------------------
 | Action
 -------------------------------*/
    function bindIntroImgEditorAction($component, tripImgId) {
        $dropifyEv = $component.find('[name="trip_activity_intro_img"]').dropify({
            messages: {
                'default': 'Drag and drop a image here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });

        $component.find('[name="trip_activity_intro_img"]').bind('change',function(){
            backendAPI.createIntroImg($(this));

        })

        $component.find('.submit-intro-img-btn').click(function () {
            var imgDescription = $component.find('[name="trip_activity_intro_img_description"]').val();
            $action = backendAPI.updateIntroImgDescription(tripImgId, imgDescription);
            if($action.success){
                window.location.reload();
            }
        })

    }
/*-------------------------------
 | Backend API
 -------------------------------*/
    backendAPI = (function() {
        return {
            createIntroImg: function ($media) {
                data = new FormData();
                data.append('trip_activity_id', tripActivityId);
                data.append('trip_activity_intro_img',$media[0].files[0]);

                return $.ajax({
                    url : '/employee/trip_activity/create/intro_image',
                    data : data,
                    contentType: false,
                    processData: false,
                    async : false
                }).responseJSON;
            },
            updateIntroImgDescription: function ($tripIntroImgId, tripImgDescription) {
                return $.ajax({
                    url : '/employee/trip_activity/update/intro_image_info',
                    data : {trip_activity_id: tripActivityId, trip_img_id: $tripIntroImgId, trip_img_description: tripImgDescription, trip_language: tripActivityLanguage},
                    async : false
                }).responseJSON;
            }
        }
    })();
})(jQuery);