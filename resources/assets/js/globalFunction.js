/*
 youtube
 */
function onPlayerReady(event) {
    event.target.mute();
}
function onPlayerPause(event){
    event.target.pauseVideo();
}
function getYoutubeId($url){
    var matches;
    var $id;
    var $regex_pattern = /^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    if(matches = $url.match($regex_pattern)){
        $id = matches[1];
        return $id;
    }
}
/*
 |
 | globalFunction
 |
*/
function openUserProfileModal($user_name){
    var url = 'GET/userProfile/modal/' + $user_name;
    $.ajax({
        url : $(this).attr('href'),
        type: 'GET',
        statusCode: {
            404: function() {
                alert( "這用戶暫時不是Pneker" );
            },
            500: function(){
                alert('這用戶暫時不是Pneker');
            }
        },
        success: function(data){
            $('#userModal').attr('data-user-name',$user_name);
            $('#userModal').modal('show').find('.modal-content').load(url);
            $('#userModal').modalSteps(url);
        }
    })
}
/*
|
|Travel Appointment API
|
|
*/
/*Accept Appointment*/
acceptTripAppointment = function($appointment_id){
    $url = '/accept_appointment_by_guide';
    return $.ajax({
        url: $url,
        data : {appointment_id : $appointment_id},
        statusCode: {
            500: function(){
                alert("something wrong");
            }
        },
    })
}
/*Reject Appointment*/
rejectTripAppointment = function($appointment_id){
    $url = '/reject_appointment_by_guide/' + $appointment_id;

    return $.ajax({
        url: $url,
        statusCode: {
            500: function(){
                alert("something wrong");
            }
        },
    })
}
/*Cancel Appointment*/
cancelTripAppointment = function($appointment_id){
    $url = '/cancel_appointment_for_guide/'+$appointment_id;
    return $.ajax({
        url : $url,
        statusCode: {
            500: function() {
                alert( "something wrong." );
            }
        }
    });//ajax
}
discardTripAppointment = function($appointment_id){
    $url = '/trip_appointment/discard';
    return $.post($url,{appointment_id: $appointment_id});
};
/*
** ChatRoom
*/
chatRoomSendMsg = function($room_id, $content){
    $url = '/sendMsg';
    return $.post($url,{room_id: $room_id, content: $content});
}
/*
** Helpers
*/
function addURLParameter(url, param, paramVal){
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    if(additionalURL){
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] == param){
                return baseURL + "?" + additionalURL;
            }
        }
        additionalURL += additionalURL + param + '=' + paramVal;
    }else{
        additionalURL = param + '=' + paramVal;
    }

    return baseURL + "?" + additionalURL;
}

function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

function removeURLParameter(url, param) {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    return baseURL + newAdditionalURL;
}

function initURLParameterAction(url) {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var params = tempArray[1];
    params = params.split("&");
    for(var i=0; i<params.length; i++){
        key = params[i].split('=')[0];
        val = params[i].split('=')[1];

        switch(key) {
            case 'hopscotchAction':
                hopscotch.startTour(hopscotchs[val]);
                break;
            default:

        }
    }



}
//-------------------------------------------
//  preventDoubleClick
//-------------------------------------------
(function ($) {
    var $originBtnText = '';
    $.fn.preventDoubleClick = function(){
        $originBtnText = $(this).text();
        return $(this).preventDoubleEvent('click', 'clicked');
    };
    $.fn.unlockPreventDoubleClick = function(){
        return $(this).unlockPreventDoubleEvent();
    };
    $.fn.preventDoubleSubmission = function(){
        return $(this).preventDoubleEvent('submit', 'submitted');
    };
    $.fn.preventDoubleEvent = function(event, dataType){
        $(this).on(event,function(e){
            var $form = $(this);
            if ($form.data(dataType) === true) {
                e.preventDefault(); // Previously submitted - don't submit again
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }else{
                $form.text('Loading...');
                $form.data(dataType, true); // Mark it so that the next submit can be ignored
            }
        });
        return this; // Keep chainability
    };
    $.fn.unlockPreventDoubleEvent = function () {
        var $form = $(this);
        $form.data('clicked', false);
        $form.text($originBtnText);

        return this;

    }
})(jQuery);
