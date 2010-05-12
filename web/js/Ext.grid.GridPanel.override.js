Ext.override(Ext.grid.GridPanel, {
  // need to override getView so that viewConfig will be applied to the view
  // object if it is already present
  getView : function() {
    if (!this.view) {
      this.view = new Ext.grid.GridView(this.viewConfig);
    } else {
      Ext.apply(this.view, this.viewConfig);
    }
    return this.view;
  }
});