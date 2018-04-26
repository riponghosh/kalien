function EventManager(startHour, endHour, draggingLine){
  var eventSyncManager = new EventSyncManager();
  var allEvents = {};
//-------------------------------
//  創建行程
//-------------------------------
  var eventIDCounter = 0, eventInfo = new EventInfo($('.list-container'));
  function createEvent(rawData, getCellHeight, callbacks){
    var thisObj, columnObj, layer;
    var fromMinute, toMinute, topic, subTitle, description, eventID, sLocked = false, oLocked = false;
    var minuteHeight = getCellHeight() / 60;
    var unikey = (eventIDCounter -= 1);
    var $element = $(
      '<div class="eventBlock disabled">' +
        '<div class="left_bar"></div>' +
        '<div class="main">' +
          '<div class="omit_text topic"></div>' +
          '<div class="omit_text location"></div>' +
        '</div>' +
        '<div class="resize_footer"></div>' +
      '</div>'
    ).mousedown(function(){ thisObj.onMouseDown(eventInfo); });
    function setEventID(newID){
      if (eventID == newID) return;
      if (eventID != undefined) delete allEvents[eventID];
      eventID = newID;
      $element.removeClass('disabled'); //行程載入成功時，opacity : 0.5 -> 1
      if (eventID != undefined) allEvents[eventID] = thisObj;
    }
    function updateData(rawData, fromSync){
      var needUpdateTime = false;
      var needUpdateText = false;
      var needUpdateLock = false;
      if (rawData.id != undefined){
        setEventID(rawData.id);
      }
      if (rawData.from != undefined && rawData.from != fromMinute){
        fromMinute = rawData.from;
        needUpdateTime = true;
      }
      if (rawData.to != undefined && rawData.to != toMinute){
        toMinute = rawData.to;
        needUpdateTime = true;
      }
      if (rawData.topic != undefined && rawData.topic != topic){
        topic = rawData.topic;
        needUpdateText = true;
      }
      if (rawData.sub_title != undefined && rawData.sub_title != subTitle){
        subTitle = rawData.sub_title;
        needUpdateText = true;
      }
      if (rawData.description != undefined && rawData.description != description){
        description = rawData.description;
        needUpdateText = true;
      }
      if (rawData.s_locked != undefined && rawData.s_locked != sLocked){
        sLocked = rawData.s_locked;
        needUpdateLock = true;
      }
      if (rawData.o_locked != undefined && rawData.o_locked != oLocked){
        oLocked = rawData.o_locked; 
        needUpdateLock = true;
      }
      if (needUpdateTime) updateTime();
      if (needUpdateText) updateText();
      if (needUpdateLock) updateLock();
      if (fromSync) confirmSyncData(rawData);
    }
    function getRawData(){
      return {
        id          : eventID,
        from        : fromMinute,
        to          : toMinute,
        topic       : topic,
        sub_title   : subTitle,
        description : description,
        s_locked    : sLocked,
        o_locked    : oLocked,
      };
    }
    ;(function(){
    //-------------------------------
    //  拖曳行為
    //-------------------------------
      var spanMinute = 10;
      function bindOndrag($target, allowXchgFlag, callback){
        var x = 0, y = 0;
        function changeX(offX){
          if (!allowXchgFlag) return;
          var colWidth = columnObj.$column.width();
          x += offX;
          if (x > colWidth * 0.8){
            x -= colWidth;
            callbacks.toNextColumn();
          }else if (x < -colWidth * 0.2){
            x += colWidth;
            callbacks.toPrevColumn();
          }
        }
        function changeY(offY){
          y += offY / minuteHeight;
          var span = Math.round(y / spanMinute);
          if (span != 0){
            var val = span * spanMinute;
            y -= val * minuteHeight;
            callback(val);
          }
        }
        $target.ondrag({
          checkDraggable: function(){ return !locked(); },
          onDragStart: function(){
            draggingLine.bindWith(thisObj);
          },
          onDrag: function(offX, offY){
            changeX(offX);
            changeY(offY);
          },
          onDragEnd: function(){
            draggingLine.bindWith(undefined);
          }
        });
      }
    //-------------------------------
    //  平移行程時間
    //-------------------------------
      bindOndrag($element.find('.left_bar'), true, function(minuteChange){
        minuteChange = Math.max(Math.min(fromMinute + minuteChange, endHour * 60 - 10), startHour * 60) - fromMinute;
        minuteChange = Math.max(Math.min(toMinute + minuteChange, endHour * 60 - 10), startHour * 60) - toMinute;
        if (minuteChange == 0) return;
        fromMinute += minuteChange;
        toMinute += minuteChange;
        updateTime();
      });
    //-------------------------------
    //  增加行程時間
    //-------------------------------
      bindOndrag($element.find('.resize_footer'), false, function(minuteChange){
        minuteChange = Math.max(Math.min(toMinute + minuteChange, endHour * 60 - 10), fromMinute + spanMinute) - toMinute;
        if (minuteChange == 0) return;
        toMinute += minuteChange;
        updateTime();
      });
    })();
    function getOverlayEvents(){
      var events = [[], []], flag = 0;
      _.each(columnObj.events, function(event){
        if (event == thisObj){ flag = 1; return; }
        var fromTo = event.getFromTo();
        if (toMinute < fromTo[0] || fromMinute > fromTo[1]) return;
        events[flag].push(event);
      });
      return events;
    }
    function deleteSelfInEvents(){
      if (columnObj == undefined) return;
      columnObj.events.splice(columnObj.events.indexOf(thisObj), 1);
    }
    function updateLayer(execHash, force){
      if (execHash[thisObj.unikey] && !force) return layer;
      execHash[thisObj.unikey] = true;
      var maxLayer = -1, events = getOverlayEvents();
      events[0].forEach(function(e){ maxLayer = Math.max(maxLayer, e.updateLayer(execHash, true)); });
      events[1].forEach(function(e){ e.updateLayer(execHash); });
      layer = maxLayer + 1;
      if ($element){
        var left = layer * 10;
        var width = 100 - left;
        draggingLine.updateLeft(thisObj, left, width);
        $element.css({
          width: String(width) + '%',
          left: String(left) + '%',
          'z-index': layer,
        });
      } 
      return layer;
    }
  //-------------------------------
  //  將行程移到最前面
  //-------------------------------
    function moveToFirst(){
      if (!columnObj) return;
      deleteSelfInEvents();
      columnObj.events.push(thisObj);
      updateLayer({});
    }
  //-------------------------------
  //  更新行程時間
  //-------------------------------
    function updateTime(){
      function minutesToTime(minute){
        function padZero(input, needSize){
          return _.padStart(String(input), needSize || 2, '0');
        }
        var hour = Math.floor(minute / 60);
        minute -= hour * 60;
        minute = Math.floor(minute / 10) * 10; //要是十的倍數
        return padZero(hour, 2) + ':' + padZero(minute, 2);
      }
      moveToFirst();
      var top = Math.round((fromMinute - startHour * 60) * minuteHeight);
      var height = Math.round((toMinute - fromMinute) * minuteHeight)
      draggingLine.updateTop(thisObj, top, height, minutesToTime(fromMinute), minutesToTime(toMinute));
      $element.css({
        top: top,
        height: height,
      });
      eventSyncManager.checkData();
    }
    function updateText(){
      $element.find('.topic').text(topic);
      $element.find('.location').text(subTitle);
      eventInfo.eventChanged(thisObj);
      eventSyncManager.checkData();
    }
    function updateLock(){
      eventInfo.eventChanged(thisObj);
      eventSyncManager.checkData();
      $element.toggleClass('locked', locked());
    }
  //-------------------------------
  //  獲取變更
  //-------------------------------
    var originAttributes = {};
    function checkSyncData(dateStr){ //eventSyncManager.checkData(); 後會呼叫各event的此函式
      var hasChangedInfo = false;
      var info = getChangedInfo();
      var sLocked = info.s_locked;
      if (sLocked != undefined){
        hasChangedInfo = true;
        delete info.s_locked;
        eventSyncManager.lockEvent(eventID, unikey, sLocked, {
          beforeSend: function(){
            confirmCurrentData();
          }
        });
      }
      if (Object.keys(info).length != 0){
        hasChangedInfo = true;
        info.date = dateStr;
        eventSyncManager.updateEvent(eventID, unikey, info, {
          beforeSend: function(){
            confirmCurrentData();
          },
          onSuccess: function(response){
            if (response.id) setEventID(response.id); //更新後臺給的ID
          } 
        });
      };
      if (!hasChangedInfo) eventSyncManager.cleanCmd(eventID); //避免變動沒了，卻還送錯的資料到後臺
    }
    function confirmSyncData(info){
      _.each([
        ['topic', info.topic],
        ['sub_title', info.sub_title],
        ['description', info.description],
        ['from', info.from],
        ['to', info.to]
      ], function(data){
        var key = data[0];
        var value = data[1];
        if (value != undefined) originAttributes[key] = value;
      });
    }
    function confirmCurrentData(){
      originAttributes = _.merge(originAttributes, getChangedInfo());
    }
    function getChangedInfo(){
      var changedInfo = {};
      _.each([
        ['topic', topic],
        ['sub_title', subTitle],
        ['description', description],
        ['from', fromMinute],
        ['to', toMinute],
        ['s_locked', sLocked],
      ], function(data){
        var key = data[0];
        var value = data[1];
        if (originAttributes[key] != value) changedInfo[key] = value;
      });
      if (changedInfo.to   != undefined) changedInfo.from = fromMinute; //成對傳
      if (changedInfo.from != undefined) changedInfo.to   = toMinute;   //成對傳
      return changedInfo;
    }
    function locked(){
      return (sLocked || oLocked);
    }
    thisObj = {
      unikey: unikey, //給遞迴處理時判斷用
      getFromTo: function(){ return [fromMinute, toMinute]; },
      getColumn: function(){ return columnObj; },
      setColumn: function(_columnObj){
        if (_columnObj == undefined) return;
        deleteSelfInEvents();
        columnObj = _columnObj;
        columnObj.events.push(thisObj);
        columnObj.$column.find('.events_container').append($element);
        updateTime();
      },
      updateLayer: updateLayer,
      updateData: updateData,
      getRawData: getRawData,
      bound: function(){
        if ($element) $element.addClass('bound');
      },
      unbound: function(){
        if ($element) $element.removeClass('bound');
      },
      locked: locked,
      remove: function(fromSync){
        if (locked()) return alert('error! cannot delete locked events'); //防呆
        deleteSelfInEvents();
        $element.remove();
        $element = undefined;
        updateLayer({});
        eventSyncManager.delEvent(eventID, unikey, fromSync);
        setEventID(undefined);
      },
      onMouseDown: function(eventInfo){
        eventInfo.bindWith(thisObj);
        if (!eventInfo.isDisabled()) moveToFirst();
      },
      checkSyncData: checkSyncData,
      confirmCurrentData: confirmCurrentData
    };
    updateData(rawData); //要在 thisObj 被設定之後，否則 setEventID 時會記到 undefined。
    eventInfo.bindWith(thisObj); //創新行程時，自動綁定右邊行程info面版
    return thisObj;
  }
  return {
    create: createEvent,
    getSyncManager: function(){ return eventSyncManager; },
    getEventInfo: function(){ return eventInfo; },
    getEvent: function(id){ return allEvents[id]; }
  };
};
