function DateColumn($container, currentDate, eachHour, idx, callbacks){
  var thisObj = {}, events = [];
  var $column = $(
    '<div class="day-column">' +
      '<div class="th date">' +
        '<span class="week"></span>' + 
        '<span class="day"></span>' + 
        '<span class="month"></span>' +
          '<div class="dropdown" style="display: inline-block; right: -10px">' +
            '<i class="fa fa-ellipsis-v dropMenuBtn" aria-hidden="true" data-toggle="dropdown"></i>' +
            '<ul class="dropdown-menu p-l-10">'+
              '<li class="deleteDateBtn" style="color: red"><i class="fa fa-times m-r-10" aria-hidden="true"></i>Delete this day</li>' +
              ' <li class="order_whole_day_event_btn"><a>Order all event in this day</a></li>' +
            '</ul>'+
          '</div>' +
      '</div>' +
      '<div class="events_container"></div>' +
      '<div class="content"></div>' +
    '</div>'
  );
  thisObj.$column = $column;
  eachHour(function(hour, startHour, endHour){
    $column.find('> .content').append($('<div class="cell">').dblclick(function(){
      var maxTo = endHour * 60 - 10;
      var from = hour * 60;
      var to = from + 90;
      if (to > maxTo){
        from += maxTo - to;
        to = maxTo;
      }
      thisObj.createEvent({
        from: from,
        to: to,
        topic: '新的主題',
        sub_title: '新的地點',
        description: ''
      });
    }));
  });
//-----------------------------------------
//  刪除日期
//-----------------------------------------
  $column.find('.deleteDateBtn').click(function(){ //delete date column
    if (!callbacks.couldBeDelete()) return;
    if (!confirm('確認要刪除這個日期，刪除的行程將無法回溯！')) return;
    var force = false; //boolean
    if (_.some(thisObj.events, function(event){ return event.locked() })){
      if (!confirm('此日期包含你所鎖定的行程，將會一併所被刪除，確定刪除嗎？')) return;
      force = true;
    }
    $column.remove();
    callbacks.onDelete(thisObj, force);
  });
//-----------------------------------------
//  打開預定請求table
//-----------------------------------------
  $column.find('.order_whole_day_event_btn').click(function(){
    callbacks.CallOrderWholeDayEvents(thisObj);
  });
  var getCellHeight = (function(cellHeight){
    return function(){
      if (cellHeight == undefined) cellHeight = $column.find('.cell').outerHeight();
      return cellHeight;
    }
  })();
  $.extend(thisObj, {
    createEvent: function(info){
      return callbacks.createEvent(thisObj, getCellHeight, info);
    },
    selfDelete: function(){
      $column.remove();
      return callbacks.selfDelete(thisObj);
    },
    currentDate: function(){ return currentDate; },
  //-----------------------------------------
  //  刷新日期
  //-----------------------------------------
    updateDate: function(offset){
      var dateStr = moment(currentDate).format('ddd DD MMM').split(' ');
      $column.find('.week').text(dateStr[0]);
      $column.find('.day').text(dateStr[1]);
      $column.find('.month').text(dateStr[2]);
    },
    events: events,
  });
//----------------------------------
//  子元素插入父元素內指定位置
//----------------------------------
  jQuery.fn.insertAt = function(index, element, selector) {
      var lastIndex = this.children(selector).length;
      if (index < 0) {
          index = Math.max(0, lastIndex + 1 + index);
      }
      this.append(element);
      if (index < lastIndex) {
          this.children(selector).eq(index).before(this.children(selector).last());
      }
  }
//----------------------------------
//  Day Column Position
//----------------------------------
  ;(function(){
    var maxEventWith, offsetX, offsetY;
    $.extend(thisObj, {
      position: {
        cleanCache: function(){
          maxEventWith = undefined;
          offsetX = undefined;
          offsetY = undefined;
        },
        width: function(){
          if (maxEventWith == undefined) maxEventWith = $column.find('.events_container').width();
          return maxEventWith;
        },
        offX: function(){
          if (offsetX == undefined) offsetX = $column.position().left - $container.position().left;
          return offsetX;
        },
        offY: function(){
          if (offsetY == undefined) offsetY = $column.find('.events_container').position().top - $column.position().top;
          return offsetY;
        }
      }
    });
  })();
  //----------------------------------
  //  Day Column Sort
  //----------------------------------
  $container.insertAt(idx, $column, '.day-column');
  return thisObj;
}
