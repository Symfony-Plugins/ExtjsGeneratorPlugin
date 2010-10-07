Ext.override(Ext.list.ListView, {  
  // onResize for Ext 3.3.0 to fix IE bug
  onResize : function(w, h) {
    var body = this.innerBody.dom, header = this.innerHd.dom, scrollWidth = w
      - Ext.num(this.scrollOffset, Ext.getScrollBarWidth()) + 'px', parentNode;

    if (!body) {
      return;
    }
    parentNode = body.parentNode;
    if (Ext.isNumber(w)) {
      if (this.reserveScrollOffset || ((parentNode.offsetWidth - parentNode.clientWidth) > 10)) {
        body.style.width = scrollWidth;
        header.style.width = scrollWidth;
      } else {
        body.style.width = w + 'px';
        header.style.width = w + 'px';
        setTimeout(function() {
          if ((parentNode.offsetWidth - parentNode.clientWidth) > 10) {
            body.style.width = scrollWidth;
            header.style.width = scrollWidth;
          }
        }, 10);
      }
    }
    if (Ext.isNumber(h)) {
      parentNode.style.height = Math.max(0, h - header.parentNode.offsetHeight) + 'px';
    }
  }
});