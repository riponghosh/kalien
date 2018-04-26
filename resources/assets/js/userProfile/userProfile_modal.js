/*
 |Friend
 */
$.fn.friendBtn = function(status,userId) {
    userModalFriend = new UserModalFriend($(this));//callback
    userModalFriend.init(status, userId);
}
var UserModalFriend = function(Btn,status){
    var friendBtn = Btn,
    currentBtn,
    targetId,
    notFriend = {
      content: 'Add friend',
      dataStatus: 'notFriend',
      method: [
          {name: 'addFriend',success: 'pending'}
      ]
    },
    isFriend = {
      content: 'Friend',
      dataStatus: 'isFriend',
      method: [
            {name: 'unfriend',success: 'notFriend'}
      ]
    },
    pending = {
      content: 'Request sent',
      dataStatus: 'pending',
      method: [
          {name: 'cancel',success: 'notFriend'}
      ]
    },
    requestGet = {
      content: 'Have Request',
      dataStatus: 'requestGet',
      method: [
          {name: 'accept',success: 'isFriend'},
          {name: 'reject',success: 'requestRejected'}
      ]
    },
    requestRejected = {
      content: 'Rejected',
      datatStatus: 'requestRejected',
      method: [
          {name: 'accept',success: 'isFriend'}
      ]
    };
    /*
    friendBtn.on('click',function(){
        sendRequest(currentBtn.method[0].name).done(function(response){
            if(response.status == 'success') initializeStatus(currentBtn.method[0].success);
        });
    });
    */
    initializeStatus = function(status){
        if(status == 'notFriend'){
            currentBtn = notFriend;
        }else if(status == 'isFriend'){
            currentBtn = isFriend;
        }else if(status == 'pending'){
            currentBtn = pending;
        }else if(status == 'requestGet'){
            currentBtn = requestGet;
        }else if(status == 'requestRejected'){
            currentBtn = requestRejected;
        }else{
            return;
        }
        UserModalFriendUI(friendBtn,status,{
            onSend: function(dataMethod){
                sendRequest(currentBtn.method[dataMethod].name).done(function(response){
                    if(response.status == 'success'){
                      if(response.friend_status != null){
                        initializeStatus(response.friend_status);
                      }else{
                        initializeStatus(currentBtn.method[dataMethod].success)
                      }
                    };
                    if(response.status == 'error'){
                       alert('抱歉，資料有變動的地方');
                       userProfileModal().reload(targetId);
                    }
                });
            }
        });
        //friendBtn.text(currentBtn.content);
        friendBtn.prop('data-status',currentBtn.dataStatus);
        friendBtn.prop('data-method');
    };
    sendRequest = function(method){
      return $.ajax({
          statusCode: {
              401: function () {
                  loginModal.show();
                  login.afterSuccessToDo($.Callbacks().add(userProfileModal().reload), targetId);
              },
          },
          url: 'relationship/friendRequest/' + method,
          data: {user_id: targetId}
      })
    };
  return{
    init: function(status,userId){
        targetId = userId;
        initializeStatus(status);
    }
  }
}
var UserModalFriendUI = function(Btn,status,callbacks){
    if (callbacks == undefined) callbacks = {};
    $container = Btn.parent('li');
    $container.find('.dropdown-menu').remove();
    Btn.html('');
    $dropdownMenu = $('<ul class="dropdown-menu friendBtn_menu" aria-labelledby="dropdownMenuFriend"></ul>');
    switch(Btn,status){
        case'notFriend':
            Btn.append("<i class='fa fa-user-plus' aria-hidden='true'></i> Add friend");
            $li = $('<li data-method="0"><a>Add friend</a></li>');
            break;
        case'isFriend':
            Btn.append("is Friend <span class='caret'></span>");
            $li = $('<li data-method="0"><a>unFriend</a></li>');
            break;
        case'pending':
            Btn.append("Request sent <span class='caret'></span>");
            $li = $('<li data-method="0"><a>Cancel request</a></li>');
            break;
        case'requestGet':
            Btn.append("Have Request<span class='caret'></span>");
            $li = $('<li data-method="0"><a>Accept</a></li>' +
                '<li data-method="1"><a>Reject</a></li>');
            break;
        case'requestRejected':
            Btn.append("Rejected <span class='caret'></span>");
            $li = $('<li data-method="0"><a>Accept</a></li>');
            break;
    }
    $container.append($dropdownMenu.append($li));
    $('.friendBtn_menu').find('li').click(function(){
        callbacks.onSend($(this).data('method'));
    })
}
/*
|Follow
*/
$.fn.followBtn = function(status,userId){
  userModalFollow = new UserModalFollow($(this));//callback
  userModalFollow.init(status,userId);
}
var UserModalFollow = function(Btn,status){
  var followBtn = Btn;
  var userId;
  var follow = {
    content : 'Following',
    dataStatus: 'follow',
  },
  unfollow = {
    content : 'Follow',
    dataStatus: 'unfollow',
  };
  followBtn.on('click',function(){
    $(this).prop('disabled',true);
    if($(this).prop('data-status') == 'unfollow'){
      $.when(followAction(userId)).done($(this).prop('disabled',false));
    }else if($(this).prop('data-status') == 'follow'){
      $.when(unfollowAction(userId)).done($(this).prop('disabled',false));
    }else{
      $(this).prop('disabled',false);
      return;
    }
  })
  function initializeStatus(status){
    var output;
    if(status == 'follow'){
      output = follow;
    }else if(status == 'unfollow'){
      output = unfollow;
    }else{
      return;
    }
    followBtn.text(output.content);
    followBtn.prop('data-status',output.dataStatus);
    return;
  }
  function changeStatus(status){
    initializeStatus(status);
  }
  function followAction(userId){
    $.ajax({
      url: '/FollowUser',
      data: {user_id: userId},
      statusCode: {
        401: function(){
          loginModal.show();
          login.afterSuccessToDo($.Callbacks().add(followAction),userId );
        },
      }
    }).done(function(response){
      if(response.status == 'ok'){
        changeStatus('follow');
      }else{
        return;
      }
    });
  }
  function unfollowAction(){
    $.post('/DELETE/FollowUser',{user_id: userId}).done(function(response){
      if(response.status == 'ok'){
        changeStatus('unfollow');
      }else{
        return;
      }
    })
  }
  return{
    init: function(status,userid){
      userId = userid;
      initializeStatus(status);
    }
  }
};
userProfileModal = function(){
    return{
        reload: function(uni_name){
            url = 'GET/userProfile/modal/' + uni_name;
            $('#userModal').find('.modal-content').load(url);
        }
    }
}

