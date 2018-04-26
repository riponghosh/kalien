function EventSyncManager(){
  var CMD_ADD = 'add';
  var CMD_DEL = 'del';
  var CMD_UPDATE = 'update';
  var CMD_LOCK = 'lock_eventBlock';
  var CMD_ORDER = _.keyBy([CMD_ADD, CMD_UPDATE, CMD_LOCK, CMD_DEL]);
  var deleteDateData = [];
  var cmds1 = {}, cmds2 = {}, basicParams; //cmds1, cmds2 分離怕後臺ID剛好跟前臺自創的ID一樣
  var lockCmd = {}; //lock另外傳
  var checkDataFunc, isSyncing = false, isSending = false;
//---------------------------------------------------
//  發送資料與後臺溝通
//---------------------------------------------------
  var sendData = _.debounce(function(){
    if (isSending) return sendData(); //前一個請求還未回來的話，再等 debounce 一次的時間再送。
    isSending = true;
    var cmds = [];
    function pushCmd(cmd){
      cmds.push(cmd);
      if (cmd.callbacks && cmd.callbacks.beforeSend) cmd.callbacks.beforeSend();
    }
    _.each(cmds1, pushCmd);
    _.each(cmds2, pushCmd);
    _.each(lockCmd, pushCmd);
    cmds1 = {};
    cmds2 = {};
    lockCmd = {};
    if (cmds.length == 0) return finishSending();
    cmds = _.sortBy(cmds, function(s){ return -CMD_ORDER[s.info.type]; })
    var params = _.merge({ cmds: _.map(cmds, function(s){ return s.info; }) }, basicParams);
    console.log(params);
    $.post('/schedule/eventBlock', params, function(response){
      _.each(response, function(cmdResponse, idx){
        if (cmdResponse.status != 'ok'){
          alert('出了錯誤，為你重新導頁頁面');
          //window.location.reload();
        }
        var callbacks = cmds[idx].callbacks;
        if (callbacks && callbacks.onSuccess) callbacks.onSuccess(cmdResponse);
      });
      finishSending();
    }, 'json').fail(function(){
      alert('出了錯誤，為你重新導頁頁面');
      //window.location.reload();
    });
  }, 800);
  function finishSending(){
    isSending = false;
    sendDeleteDateRequest();
  }
  function sendDeleteDateRequest(){
    if (deleteDateData.length == 0) return;
    _.each(deleteDateData, function(data){
      var params = _.merge({}, data.params, basicParams);
      var callbacks = data.callbacks || {};
      console.log('delete_date', params);
      $.post('/schedule/delete_date', params, function(response){
        if (callbacks.onSuccess) callbacks.onSuccess(response);
      });
    });
    deleteDateData = [];
  }
//---------------------------------------------------
//  ACCESS
//---------------------------------------------------
  return {
    sync: function(eventInfos, callbacks){
      isSyncing = true;
      _.each(eventInfos, function(info){
        info.date = new Date(info.date); //後臺傳來的是字串
        if (info.type == undefined) info.type = CMD_ADD;
        switch(info.type){
        case CMD_ADD:{
          callbacks.add(info);
          break;}
        case CMD_DEL:{
          callbacks.del(info);
          break;}
        case CMD_UPDATE:{
          callbacks.update(info);
          break;}
        case CMD_LOCK:{
          callbacks.lock(info);
          break;}
        }
      });
      isSyncing = false;
    },
    setBasicParams: function(params){
      basicParams = params;
    },
    setCheckDataMethod: function(func){
      checkDataFunc = _.debounce(func, 30);
    },
    checkData: function(){
      if (isSyncing) return;
      if (checkDataFunc) checkDataFunc();
    },
    updateEvent: function(eventID, unikey, info, callbacks){
      if (eventID == undefined){
        info.type = CMD_ADD;
        cmds1[unikey] = {info: info, callbacks: callbacks};
      }else{
        info.type = CMD_UPDATE;
        info.id = eventID;
        delete cmds1[unikey];
        cmds2[eventID] = {info: info, callbacks: callbacks};
      }
      sendData();
    },
    delEvent: function(eventID, unikey, fromSync){
      if (eventID == undefined){
        delete cmds1[unikey];
      }else{
        if (fromSync) delete cmds2[eventID];
        else cmds2[eventID] = {info: {type: CMD_DEL, id: eventID}};
      }
      if (!fromSync) sendData();
    },
    lockEvent: function(eventID, unikey, sLocked, callbacks){
      if (eventID == undefined) return;
      lockCmd[eventID] = {info: {type: CMD_LOCK, id: eventID, status: sLocked}, callbacks: callbacks}
      sendData();
    },
    cleanCmd: function(eventID, unikey){
      if (unikey != undefined){
        delete cmds1[unikey];
      }
      if (eventID != undefined){
        delete cmds2[eventID];
        delete lockCmd[eventID];
      }
    },
    deleteDate: function(date, force){
      force = force == true ? 1 : 0;
      deleteDateData.push({
        params: {date: date, force: force}
      });
      sendData();
    }
  }
};
