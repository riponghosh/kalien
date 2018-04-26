var userNotificationsManager = new function () {
    var hash = {};
    var $notificationBellStatus = false;

    function create(notiData) {
        if(hash[notiData.id] == undefined){
            hash[notiData.id] = new UserNotification(notiData);
            if(hash[notiData.id].read_status == false){
                notiBellAlert(true);
            }
        }
    }

    function notiBellAlert($status) {
        if($notificationBellStatus == $status) return;
        if($status == true){
            $('#notification-btn').find('.noti-dot').show();
            $notificationBellStatus = true;
        }else{
            $('#notification-btn').find('.noti-dot').hide();
            $notificationBellStatus = false;
        }
    }
    function bindEvent() {
        $('#notification-btn').click(function () {
            notiBellAlert(false);
        });
    }
//---------------------------------
//  點擊事件
//---------------------------------



    return {
        init: function () {
            $('#notification-btn').find('.noti-dot').hide();
              $.get('/notifications/get_all',function (res) {
                  if(res.success == true){
                      res.data.forEach(function(notiData, idx){
                          create(notiData);
                      })
                  }
              });
            bindEvent();
        },
        create: create
    }
}

function UserNotification(data) {
    var id = data.id;
    var is_read = data.is_read;
    var $notificationUI =
        '<li class="list-group-item">' +
        '<a href="#" class="user-list-item">' +
        '<div class="avatar">' +
        '<img src="" alt="">' +
        '</div>' +
        '<div class="user-desc">' +
        '<span class="name">' + data.title +'</span>' +
        '<span class="desc">'+ data.body +'</span>' +
        '<span class="time"></span>'+
        '</div>' +
        '</a>' +
        '</li>';
    $notification = $($notificationUI);
    $('#notification-list').find('.list-group').append($notification);
    if(data.is_read == 0) $notification.addClass('active');
//---------------------------------
//  點擊事件
//---------------------------------
    $notification.click(function () {
        updateToIsRead();
    })
//---------------------------------
//  更新至已讀
//---------------------------------
    function updateToIsRead() {
        if($notification.hasClass('active') == true){
            $backAPI = $.ajax({
                url: '/notifications/is_read',
                data: {notification_id: id},
                async : false
            }).responseJSON;
            if($backAPI.success == true){
                $(this).removeClass('active');
            }
        }
    }

    return {
        read_status: is_read,
    }
}