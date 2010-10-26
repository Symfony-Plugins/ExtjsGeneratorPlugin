Ext.ComponentMgr.create = Ext.ComponentMgr.create.createInterceptor(function(config, defaultType) {
  if (!Ext.ComponentMgr.isRegistered(config.xtype || defaultType)) this.loadType(config.xtype || defaultType);
});

Ext.ComponentMgr.createPlugin = Ext.ComponentMgr.createPlugin.createInterceptor(function(config, defaultType) {
  if (!Ext.ComponentMgr.isPluginRegistered(config.ptype || defaultType)) this.loadType(config.ptype || defaultType);
});