function DraggingLine($container){
  var bindedEvent, $element, $bottom;
  function createElement(){
    if ($element != undefined) return;
    $element = $(
      '<div class="dragging_line">' +
        '<div>' +
          '<div class="time"></div>' +
          '<div class="line"></div>' +
        '</div>' +
        '<div class="bottom_container">' +
          '<div class="time"></div>' +
          '<div class="line"></div>' +
        '</div>' +
      '</div>'
    );
    $bottom = $element.find('.bottom_container');
    $container.find('.relative_area').append($element.css({left: -999999, top: -999999}));
  }
  function destroyElement(){
    if ($element == undefined) return;
    $element.remove();
    $element = undefined;
  }
  return {
    bindWith: function(event){
      if (bindedEvent == event) return;
      destroyElement();
      bindedEvent = event;
      if (bindedEvent != undefined) createElement();
    },
    updateLeft: function(event, left, width){
      if (bindedEvent != event) return;
      left = bindedEvent.getColumn().position.width() * left / 100;
      var padding = 68;
      $element.css({
        left: padding,
        width: left + bindedEvent.getColumn().position.offX() - padding,
      });
    },
    updateTop: function(event, top, height, time1, time2){
      if (bindedEvent != event) return;
      $bottom.css('margin-top', height);
      $element.css({
        top: top + bindedEvent.getColumn().position.offY()
      });
      $element.find('.time').eq(0).text(time1);
      $element.find('.time').eq(1).text(time2);
    }
  }
}
