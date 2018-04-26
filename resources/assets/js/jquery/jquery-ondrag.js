$.fn.ondrag = function(attrs){
  var posX, posY, isMouseDown = false, isMouseMoved = false;
  if (!attrs.container) attrs.container = $(window);
  function checkDraggable(){
    if (attrs.checkDraggable) return attrs.checkDraggable();
    return true;
  }
  function mousedown(e){
    isMouseDown = true;
    posX = e.clientX;
    posY = e.clientY;
    if (attrs.onDragStart) attrs.onDragStart(posX, posY);
  }
  function mousemove(e){
    if (attrs.onMove) attrs.onMove(e);
    var offX = e.clientX - posX;
    var offY = e.clientY - posY;
    if (offX == 0 && offY == 0) return; //防呆。不知道為什麼有時候明明沒有移動滑鼠，瀏覽器(chrome)卻會一直觸發mousemove事件（每秒）
    if (attrs.onDrag) attrs.onDrag(offX, offY, isMouseMoved);
    isMouseMoved = true;
    posX = e.clientX;
    posY = e.clientY;
  }
  function mouseup(e){
    if (isMouseMoved && attrs.onDragEnd) attrs.onDragEnd(e);
    isMouseMoved = false;
    isMouseDown = false;
  }
  this.on("touchstart", function(e){
    if (!checkDraggable()) return;
    mousedown(e.originalEvent.touches[0] || e.originalEvent.changedTouches[0]);
    attrs.container.on("touchmove", function(e){
      mousemove(e.originalEvent.touches[0] || e.originalEvent.changedTouches[0]);
      if (e.originalEvent.touches.length == 1) return stopPropagation(e); //避免往下拖曳時，會觸發android chrome的重新整理。但允許二指放大的功能。
    });
    attrs.container.on("touchend", function(e){
      mouseup(e.originalEvent.touches[0] || e.originalEvent.changedTouches[0]);
      attrs.container.unbind("touchmove");
      attrs.container.unbind("touchend");
    });
  });
  this.mousedown(function(e){
    if (e.which != 1) return;
    if (!checkDraggable()) return;
    mousedown(e);
    attrs.container.mousemove(function(e){
      mousemove(e);
    });
    attrs.container.mouseup(function(e){
      if (e.which != 1) return;
      mouseup(e);
      attrs.container.unbind("mousemove");
      attrs.container.unbind("mouseup");
    });
  });
  return this;
};
