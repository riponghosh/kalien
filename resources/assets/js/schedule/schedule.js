var schedularsManager = new function(){
  var hash = {};
  return {
    create: function(scheduleID, $container, startHour, endHour, isOwner){
      if (hash[scheduleID] == undefined){
        hash[scheduleID] = new Schedular(scheduleID, $container, startHour, endHour, isOwner);
      } 
      return hash[scheduleID];
    },
    syncEvent: function(schuduleID, cmds){
      var schedule = hash[schuduleID];
      if (schedule == undefined) return;
      schedule.syncEvent(cmds);
    },
    syncSchedule: function(schuduleID, cmds){
        var schedule = hash[schuduleID];
        if (schedule == undefined) return;
        schedule.syncSchedule(cmds);
    }
  }
}
function Schedular(scheduleID, $container, startHour, endHour, isOwner){
  var draggingLine = new DraggingLine($container);
  var eventManager = new EventManager(startHour, endHour, draggingLine);
  var eventSyncManager = eventManager.getSyncManager();
  var pageManager = new PageManager({
    onPageChange: function(){
      updateDateColumnsDisplayStatus();
      updatePageArrow();
    },
  });
  eventSyncManager.setBasicParams({schedule_id: scheduleID});
  function eachHour(callback){
    for(var hour = startHour; hour < endHour; ++hour) callback(hour % 24, startHour, endHour);
  }
//-------------------------------
//  元件
//-------------------------------
    $btnGroup = $container.find('.page_control_btns_group');
    $leftMoveBtn = $btnGroup.find('.left_btn');
    $rightMoveBtn = $btnGroup.find('.right_btn');
    $addDateBtn = $btnGroup.find('.add_date_btn');
//-------------------------------
//  創建時間軸
//-------------------------------
  function createTimeline(){
    var $timelineContent = $('.timeline > .content');
    eachHour(function(hour){
      var $cell = $('<div class="cell">');
      if (hour % 2 == 0) $cell.text(String(hour) + ':00');
      $timelineContent.append($cell);
    });  
  }
//-------------------------------
//  刷新所以欄位的寬度、日期
//-------------------------------
  function updateAllDateColumns(){
    dayColumns.forEach(function(columnObj, idx){
      var DayColumnLength = Math.min(dayColumns.length, 4);
      columnObj.$column.css('width', 'calc(' + String(100 / DayColumnLength) + '% - ' + String(44 / DayColumnLength) + 'px)');
      columnObj.updateDate(idx);
      columnObj.position.cleanCache();
    });
    pageManager.updateMaxPage(Math.max(dayColumns.length - 4, 0));  //eg: 最大顯示天數 ： 4天, 總天數： 6天, 最左邊的最大值是第2（3）天。
    updateDateColumnsDisplayStatus();
    updatePageArrow();

  }
//-------------------------------
//
//-------------------------------
  function updatePageArrow(){
    $leftMoveBtn.toggle(dayColumns.length > 4 && !pageManager.isFirstPage());
    $rightMoveBtn.toggle(dayColumns.length > 4 && !pageManager.isLastPage());

  }
//-------------------------------
//
//-------------------------------
//-------------------------------
//  初始化
//-------------------------------
  ;(function(){
    createTimeline();
  })();
//-------------------------------
//  加日期
//-------------------------------
  var dayColumns = [];
  function findDayColumnFromDate(date){
    return _.find(dayColumns, function(s){ return s.currentDate().getTime() == date.getTime(); });
  }
  function addDate(date){
    var idx = _.filter(dayColumns, function(s){ return s.currentDate().getTime() < date.getTime(); }).length;
    dayColumns.splice(idx, 0, new DateColumn($container, date, eachHour, idx, {
      eachHour: eachHour,
      createEvent: function(self, getCellHeight, info){
        var event = eventManager.create(info, getCellHeight, {
          toNextColumn: function(){
            event.setColumn(dayColumns[dayColumns.indexOf(event.getColumn()) + 1]);
          },
          toPrevColumn: function(){
            event.setColumn(dayColumns[dayColumns.indexOf(event.getColumn()) - 1]);
          }
        });
        event.setColumn(self);
        eventSyncManager.checkData();
        return event;
      },
      couldBeDelete: function(){
        if (dayColumns.length <= 1){
          alert('至少要有一天的行程');
          return false;
        } 
        return true;
      },
      onDelete: function(self, force){
        eventSyncManager.deleteDate(moment(self.currentDate()).format('YYYY-MM-DD'), force);
        dayColumns.splice(dayColumns.indexOf(self), 1);
        updateAllDateColumns();
        var eventInfo = eventManager.getEventInfo();
        var event = eventInfo.getBindedEvent();
        if (event && event.getColumn() == self) eventInfo.bindWith(undefined); //行程info面版綁定的事件被移除了
      },
      CallOrderWholeDayEvents: function(self){
        if(!isOwner) return;
        params = {date: moment(self.currentDate()).format('YYYY-MM-DD'), schedule_id: scheduleID};
        $.post('/get_info_to_create_guide_ticket_order_in_a_date_form', params, function(response){
          if(response.success != 'undefined' && response.success == false){
              if(response.ref_code == '001'){
                  swal({
                      title: "當天沒有任何行程。",
                      type: "warning",
                      confirmButtonClass: 'btn-success',
                      confirmButtonText: '返回'
                  });
              }else if(response.ref_code == '002'){
                  swal('此人員暫時不提供任何服務','你可以告知正在跟你對話的人員，在「設定」開通收費服務。');
              }
          }else{
              $('#ModalForm').modal('show').find('.modal-content').html(response);
          }
        }).fail(function(){
            alert('出了錯誤，為你重新導頁頁面');
            //window.location.reload();
        });
      },
      selfDelete: function(self){
        dayColumns.splice(dayColumns.indexOf(self), 1);
        updateAllDateColumns();
      },
    }));
    updateAllDateColumns();
    return {
      idx: idx,
    }
  }
//-------------------------------
//  取行程表上最後的一天
//-------------------------------
  function getMaxDate(){
    dateStimeFormat = _.map(dayColumns,function(x){
      return x.currentDate().getTime();
    });
    maxTime = _.max(dateStimeFormat);
    return new Date(maxTime);
  };
//-------------------------------
//  更新所有dayColumn顯示或hide狀態
//-------------------------------
  function updateDateColumnsDisplayStatus() {
    var currentPage = pageManager.getCurrentPage();
    dayColumns.forEach(function(columnObj, idx){
      columnObj.$column.toggle(idx >= currentPage && idx < currentPage + 4);
    })
  };
//-------------------------------
//
//-------------------------------
//-------------------------------
//  事件綁定
//-------------------------------
  $rightMoveBtn.click(function(){
    pageManager.nextPage();
  })
  $leftMoveBtn.click(function(){
    pageManager.prevPage();
  })
//-------------------------------
//  同步資料
//-------------------------------
  function syncEvent(eventInfos){
    console.log('---sync event---');
    console.log(eventInfos);
      eventSyncManager.sync(eventInfos, {
          add: function(info){ //info = {id:, topic:, sub_title:, date:, from:, to:, description:}
              var event = findDayColumnFromDate(info.date).createEvent(info);
              event.confirmCurrentData();
          },
          del: function(info){
              var event = eventManager.getEvent(info.id);
              if (event) event.remove(true);
          },
          update: function(info){
              var event = eventManager.getEvent(info.id);
              if (!event) return;
              event.updateData(info, true);
              if (!info.date) return;
              event.setColumn(findDayColumnFromDate(info.date));
          },
          lock: function(info){ //websocket 才會進這裡。一開始初始化 lock 狀態是在 add 內。
              var event = eventManager.getEvent(info.id);
              if (!event) return;
              event.updateData({o_locked: info.status}, true);
          }
      });
  }
  function syncSchedule(data) {
      console.log('---sync schedule---');

      switch (data.type){
          case 'addDate':
              addDate(new Date(data.date));
              break;
          case 'deleteDate':
              findDayColumnFromDate(new Date(data.date)).selfDelete();
            break;
          default:
            return;
      }

  }
//-------------------------------
//  同步資料
//-------------------------------
  eventSyncManager.setCheckDataMethod(function(){
    _.each(dayColumns, function(columnObj){
      var dateStr = moment(columnObj.currentDate()).format('YYYY-MM-DD');
      _.each(columnObj.events, function(event){
        event.checkSyncData(dateStr);
      });
    });
  });
//-------------------------------
//  ACCESS
//-------------------------------
  return {
    syncEvent: syncEvent,
    syncSchedule: syncSchedule,
    setBasicData: function(rawDates, eventInfos){
      _.each(rawDates, function(date){ addDate(new Date(date * 1000)); });
      syncEvent(eventInfos);
    },
    addDate1: function(){
      var maxDate = getMaxDate();
      var newDate = new Date(maxDate.getTime() + 86400000);
      $.ajax({
        type: 'post',
        url: '/schedule/add_date',
        data: {new_date: moment(newDate).format('YYYY-MM-DD'), schedule_id: scheduleID},
        success: function(res){
          if(res.status == 'ok'){
            newDateIdx = addDate(newDate).idx;
            if(newDateIdx >= pageManager.getCurrentPage() + 4){
              pageManager.goToPage(newDateIdx)
            }else if(newDateIdx <= pageManager.getCurrentPage()){
              pageManager.goToPage(Math.max(newDateIdx - 4, 0));
            };

          }
        }
      });
    },
  }
}

function PageManager(callbacks){
  if (callbacks == null) callbacks = {};
  var page = 0;
  var maxPage = 0;
  return {
    getCurrentPage: function(){
      return page;
    },
    isFirstPage: function(){
      return page == 0;
    },
    isLastPage: function(){
      return page == maxPage;
    },
    goToPage: function(pageNum){
      page = pageNum > maxPage ? maxPage : pageNum;
      if (callbacks.onPageChange) callbacks.onPageChange();
    },
    nextPage: function(){
      if(page >= maxPage) return;
      page = page + 1;
      if (callbacks.onPageChange) callbacks.onPageChange();
    },
    prevPage: function(){
      if(page <= 0) return;
      page = page - 1;
      if (callbacks.onPageChange) callbacks.onPageChange();
    },
    updateMaxPage: function(newMaxPage){
      maxPage = newMaxPage;
      if(page > maxPage) page = maxPage;
    }
  }
}
