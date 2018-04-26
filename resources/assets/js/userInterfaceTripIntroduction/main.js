;(function($) {
    $.extend({
        "setBasicData" : function(cards){
            $.each(cards,function(k,card){
                /*輸入參數*/
                tripId = card.tripId;
                if(card.media != undefined) {
                    var mediaVideo = [],mediaImg = [];
                    $.each(card.media, function (k, media) {
                        media = {
                            mediaOrder : media.order,
                            mediaUrl : media.media,
                            mediaId : media.mediaId,
                            mediaType : media.mediaType
                        }
                        if(media.mediaType == 'video_url') mediaVideo.push(media);
                        if(media.mediaType == 'img') mediaImg.push(media);
                    })
                }

                topicVal = _.unescape(card.topic);
                descriptionVal = _.unescape(card.description);
                map_addressVal = card.map_address;
                map_urlVal = card.map_url;
                external_linkVal = card.external_link;
                publishVal = card.publish;

                loadTripCard(tripId, topicVal, descriptionVal, map_addressVal, map_urlVal, external_linkVal, publishVal, mediaVideo, mediaImg);
                bindPulgin({switchery : false});
            })
        },
    })
    var $publishTripLabel = ' <label class="control-label m-r-10" for="publish">Publish</label>' ,
        $publishTripBtn = '<input type="checkbox" id="publish" name="publish" checked data-plugin="switchery" data-color="#00b19d" data-size="small"/>',
        $deleteTripBtn = '<button class="delete-Trip btn btn-icon btn-danger m-l-10"><i class="fa fa-remove"></i></button>',
        $mediaUploadArea = [];
        $mediaUploadArea[1] = '<input type="file" name="main_trip_media" class="dropify" data-media-order="1" data-media-id="" data-show-errors="true" data-default-file=""/>',
        $youtubeLinkInput = [];
        $youtubePlayer = [];
        $youtubeLinkInput[1] = '<input type="text" name="main_trip_video_url" data-media-id="" data-media-order="1" class="form-control" placeholder="youtube.com/..."/>',
        $youtubePlayer[1] = '<iframe class="videoFrame" width="100%" data-media-order="1" src=""></iframe>'
        $topic = '<input type="text" class="form-control" placeholder="Trips Topic" name="topic"/>',
        $description = '<textarea class="form-control" placeholder="Description..." name="description"></textarea>',
        $map_address = '<input type="text" name="map_address" class="form-control" placeholder="地址">',
        $map_url = '<input type="text" name="map_url" class="form-control" placeholder="google map 連結">',
        $external_link = '<input type="text" name="external_link" id="trip_external_link" class="form-control" placeholder="詳細資訊連結，如活動網址，部落客等">',
        $bootstrap_shell =
            '<div class="col-lg-12 trip-container">' +
                '<div class="card-box">' +
                    '<div class="pull-right">' +
                        $publishTripLabel + $publishTripBtn +
                        $deleteTripBtn +
                    '</div>' +
                    '<h4 class="header-title m-b-30">Create your trip</h4>' +
                    '<div class="row">' +
                        '<div class="col-sm-5">' +
                            '<p class="h3 m-t-0">活動封面照片</p>' +
                            $mediaUploadArea[1] +
                        '</div>' +
                        '<div class="col-sm-6">' +
                            '<div class="form-group">' +
                                $topic +
                            '</div>' +
                            '<div class="form-group">' +
                                $description +
                            '</div>' +
                            '<div>' +
                                '<label>活動影片</label>' +
                                $youtubeLinkInput[1]  +
                                $youtubePlayer[1] +
                            '</div>' +
                            '<p>地址(可選填)</p>' +
                            '<div class="form-group">' +
                                $map_address +
                            '</div>' +
                            '<div class="form-group">' +
                                $map_url +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label for="trip_external_link" class="control-label">相關連結</label>' +
                                $external_link +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
    /*-------------------------------
     |  card initialize
     -------------------------------*/
    function loadTripCard(tripId, topicVal, descriptionVal, map_addressVal, map_urlVal, external_linkVal, publishVal, mediaVideo, mediaImg){
        $content = $($bootstrap_shell);
        /*
         * 寫入變數
         * */
        $content.find('[name="topic"]').val(topicVal);
        $content.find('[name="description"]').val(descriptionVal);
        $content.find('[name="map_address"]').val(map_addressVal);
        $content.find('[name="map_url"]').val(map_urlVal);
        $content.find('[name="external_link"]').val(external_linkVal);
        $content.find('[name="publish"]').prop("checked",publishVal);
        /*panel 初始化*/
        $content.find('.media-group').attr('id','media_panel_group_' + tripId);
        $content.find('.media-group').find('.panel-title > a').attr('data-parent','#'+'media_panel_group_' + tripId);
        $content.find('.media-group').find('.panel-title > .imageCollapseTitle').attr('href','#imageCollapse' + tripId).attr('aria-controls','imageCollapse' + tripId);
        $content.find('.media-group').find('.panel-title > .videoCollapseTitle').attr('href','#videoCollapse' + tripId).attr('aria-controls','videoCollapse' + tripId);
        /*載入media video*/
        $.each(mediaVideo,function(k, video){

            $content.find('[name="main_trip_video_url"][data-media-order="'+video.mediaOrder+'"]').attr('data-media-id',video.mediaId).attr('data-media-order',video.mediaOrder).val(video.mediaUrl);
            if(video.mediaUrl != null) $content.find('.videoFrame[data-media-order="'+video.mediaOrder+'"]').attr('src',youtubeLinkCheckable(video.mediaUrl));
        })
        /*載入media image*/
        $.each(mediaImg,function(k, img){
            $content.find('[name="main_trip_media"][data-media-order="'+img.mediaOrder+'"]').attr('data-default-file',img.mediaUrl).attr('data-media-id',img.mediaId);
        })
        $('#Trip-card-container > .card-box-area').append($content);
        $content.find('.card-box').attr("data-tripid",tripId);
        bindAction($content);
        function youtubeLinkCheckable($url){
            var $regex_pattern = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            if(matches = $url.match($regex_pattern)){
                $newUrl = 'https://youtube.com/embed/' + matches[1] +'?autoplay=0&controls=0';
                return $newUrl;
            }else{
                alert('Incorrect URL')
                return false;
            }
        }
    }
    /*-------------------------------
     | Create new card
     -------------------------------*/
    $('#addTripsBtn').click(function(){
        if($('.card-box-area > .trip-container').length >= 5){
            alert('The maximum trip is 5');
            return;
        }
        createTrip().done(function(response){
            if(response.success){
                $content = $($bootstrap_shell);
                trip_id = response.trip_id;
                $('#Trip-card-container > .card-box-area').append($content);
                $content.find('.card-box').attr("data-tripid",trip_id);
                $content.find('[name="publish"]').prop("checked",response.trip_status);
                /*panel 變數*/
                $content.find('.media-group').attr('id','media_panel_group_' + trip_id);
                $content.find('.media-group').find('.panel-title > a').attr('data-parent','#'+'media_panel_group_' + trip_id);
                $content.find('.media-group').find('.panel-title > .imageCollapseTitle').attr('href','#imageCollapse' + trip_id).attr('aria-controls','imageCollapse' + trip_id);
                $content.find('.media-group').find('.panel-title > .videoCollapseTitle').attr('href','#videoCollapse' + trip_id).attr('aria-controls','videoCollapse' + trip_id);
                $content.find('.media-group').find('.imageCollaspe').attr('id','imageCollapse' + trip_id);
                $content.find('.media-group').find('.videoCollaspe').attr('id','videoCollapse' + trip_id);

                bindPulgin({switchery : true});
                bindAction($content);
            }else{
                alert('something wrong');
            }
        }).fail(function(){
            alert('something wrong');
        });

    })
    createTrip = function(){
        return $.post('/trip/create',{publish: 'published' });
    }
    /*-------------------------------
     | Action/Method for element
     -------------------------------*/
    function bindAction($con){
        $tripId = $con.find('.card-box').data('tripid');
        /*-----------------------------------
         *  update
         -----------------------------------*/
        $con.find('[name="topic"],[name="description"],[name="map_address"],[name="map_url"],[name="external_link"]').bind('change',function(){
            $tripId = $con.find('.card-box').data('tripid');
            $updateAPI = backendAPI.update($tripId,$(this).attr('name'),$(this).val());
            if($updateAPI.success == true){
                toastr['success']('更新成功');
            }
        })
        /* Media - image*/
        $con.find('[name="main_trip_media"]').bind('change',function(){
            $tripId = $con.find('.card-box').data('tripid');
            $order = $(this).attr('data-media-order');
            $API = backendAPI.uploadMainMedia($tripId, $(this), $order);

        })
        /*  Media - youtube*/
        $con.find('[name="main_trip_video_url"]').bind('change',function(){
            $tripId = $con.find('.card-box').data('tripid');
            if($(this).val() == '') {
                $removeLink = backendAPI.removeMainMediaUrl($tripId, $(this).attr('data-media-id'), $(this).attr('data-media-order'));
                if($removeLink.success == true) $con.find('.videoFrame[data-media-order="'+$(this).attr('data-media-order')+'"]').attr('src', '');
                return
            }
            getYoutubeLink = youtubeLinkCheckable($(this).val());
            if(getYoutubeLink == false) return;
            $con.find('.videoFrame').attr('src',getYoutubeLink);
            $uploadLinkApi = backendAPI.uploadMainMediaLink($tripId, $(this).val(), $(this).attr('data-media-order'));
            if($uploadLinkApi.success == true){
                toastr['success']('更新成功');
            }else if($uploadLinkApi.success == false){
                toastr['error']('不正確的連結');
            }else{
                alert('something wrong');
            }
        })
        /*  bind Dropify  */
        $dropifyEv = $con.find('.dropify').dropify({
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
        $dropifyEv.on('dropify.beforeClear', function(event, element){
            $tripId = $con.find('.card-box').data('tripid');
            backendAPI.removeMainMedia($tripId, $(this).attr('data-media-id'), $(this).attr('data-media-order'));
        });
        /*-----------------------------------
         *  delete
         -----------------------------------*/
        $con.find('.delete-Trip').bind('click',function(){
            $tripId = $con.find('.card-box').data('tripid');
            $delAPI = backendAPI.del($tripId);
            if($delAPI.success == true){
                $(this).parents('.trip-container').remove();
            }
        })
        /*-----------------------------------
         *  Publish
         -----------------------------------*/
        $con.find('[name="publish"]').bind('change',function(){
            $tripId = $con.find('.card-box').data('tripid');
            $publish = $con.find('[name="publish"]').prop('checked');
            backendAPI.publish($tripId, $publish);
        })
        function youtubeLinkCheckable($url){
            var $regex_pattern = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            if(matches = $url.match($regex_pattern)){
                $newUrl = 'https://youtube.com/embed/' + matches[1] +'?autoplay=1&controls=0';
                return $newUrl;
            }else{
                toastr['error']('不正確的youtube連結');
                return false;
            }
        }
    }
    /*-------------------------------
     | Backend API
     -------------------------------*/
    backendAPI = (function(){
        return {
            update : function($tripId,$name,$value){
                hash ={trip_id : $tripId};
                hash[$name] = $value;
                return $.ajax({
                    url : '/trip/update',
                    data: hash,
                    async: false
                }).responseJSON;
            },
            del : function($tripId){
                return $.ajax({
                    url : '/trip/del',
                    data: {trip_id : $tripId},
                    async: false
                }).responseJSON;
            },
            publish : function($tripId, $status){
                return $.ajax({
                    url : '/trip/publish',
                    data: {trip_id : $tripId, status : $status},
                    async: false
                }).responseJSON;
            },
            uploadMainMedia : function($tripId, $media, $order){
                mediaData = new FormData();
                mediaData.append('media',$media[0].files[0]);
                mediaData.append('trip_id',$tripId);
                mediaData.append('order',$order);
                return $.ajax({
                    processData: false,
                    url : '/trip/upload_main_trip_media',
                    data : mediaData,
                    contentType: false,
                    async : false
                }).responseJSON;
            },
            uploadMainMediaLink : function($tripId, $mediaLink, $order){
                return $.ajax({
                    url : '/trip/upload_main_trip_media_url',
                    data : {trip_id: $tripId, media_link: $mediaLink, order: $order},
                    async : false
                }).responseJSON;
            },
            removeMainMedia : function($tripId, $media_id, $order){
                return $.ajax({
                    url : '/trip/remove_main_trip_media',
                    data : {trip_id: $tripId,media_id: $media_id, order: $order},
                    async : false
                }).responseJSON;

            },
            removeMainMediaUrl : function($tripId, $mediaId, $order){
                return $.ajax({
                    url : '/trip/remove_main_trip_media_url',
                    data : {trip_id : $tripId, media_id : $mediaId, order : $order},
                    async : false
                }).responseJSON;
            }
        }
    })();
    /*-------------------------------
     | init pulgin
     -------------------------------*/
    function bindPulgin(options){
        //Switchery
        if(options.switchery !== false) {
            $('[data-plugin="switchery"]').each(function (idx, obj) {
                if ($(this).data('switchery') == true) {
                } else {
                    new Switchery($(this)[0], $(this).data());
                }
            });
        }

    }

})(jQuery);