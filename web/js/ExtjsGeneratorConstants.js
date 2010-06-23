Ext.ux.IconMgr.setIconPath('/ExtjsGeneratorPlugin/Ext.ux.IconMgr');
Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
  expires : new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 7))
}));
