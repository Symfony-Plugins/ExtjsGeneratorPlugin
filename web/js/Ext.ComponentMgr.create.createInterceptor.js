Ext.ComponentMgr.create = Ext.ComponentMgr.create.createInterceptor(function(config, defaultType) {
  var xtype = config.xtype || defaultType;
  if (!Ext.ComponentMgr.isRegistered(xtype)) {
    Ext.MessageBox.wait('Loading Panel', 'Please Wait...');
    Ext.app.CodeLoader.load({
      async : false,
      method : 'GET',
      cacheResponses : false
    }, '/js/getXtype/' + xtype);
    Ext.MessageBox.hide();
  }
});