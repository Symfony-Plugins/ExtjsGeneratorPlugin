//Adds holdPosition config option to prevent loosing scroll position on reload
Ext.override(Ext.grid.GridView, {
  holdPosition : false,
  onLoad : function() {
    if (!this.holdPosition)
      this.scrollToTop();
  }
});