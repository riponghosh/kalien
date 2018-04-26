function create_chatroom(roomID,cUserId, cProfileImg, oUserId, oProfileImg,oUserName){
    //新增
    roomsManager.create(roomID, {
        id: cUserId,
        profilePic: cProfileImg,
    }, {
        id: oUserId,
        name: oUserName,
        profilePic: oProfileImg
    });
}
function open_chat_room(roomID){
    $info = get_chat_room_info_by_id(roomID);
    if($info.success == true){
        create_chatroom(roomID, $info.c_user_info.id, $info.c_user_info.icon_path, $info.o_user_info.id, $info.o_user_info.icon_path, $info.o_user_info.name);
    }
}
function get_chat_room_info_by_id(roomID){
    $success = false;
    $c_user_info = {};
    $o_user_info = {};
    data = $.ajax({
        type: 'GET',
        url: '/chatRoom/get_info/'+roomID,
        async: false
    }).responseJSON;

    if(data.success == true){
        $c_user_info = data.current_user;
        $o_user_info = data.other_users[0]; //兩人聊天室限制
        $success = true;
    }else if(data.success == false){
        $success = false;
    }else{
        $success = false
    }
    return {success: $success,c_user_info: $c_user_info, o_user_info: $o_user_info};
}