Ext.ux.IconMgr.setIconPath('/ExtjsGeneratorPlugin/Ext.ux.IconMgr');
Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
  expires : new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 7))
}));

Ext.util.Format.wrapEncode = function(value) {
  return !value 
  ? value 
  : String(value).replace(/-/g, "-&shy;").replace(/\_/g, "_&shy;");
}
